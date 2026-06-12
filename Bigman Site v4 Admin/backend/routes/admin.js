'use strict';

// ═══════════════════════════════════════════════════════════════
// ADMIN ROUTES — All protected by protect + adminOnly middleware
// GET    /api/admin/stats
// GET    /api/admin/orders
// GET    /api/admin/orders/:id
// PUT    /api/admin/orders/:id
// GET    /api/admin/users
// GET    /api/admin/users/:id
// PUT    /api/admin/users/:id
// DELETE /api/admin/users/:id
// GET    /api/admin/contacts
// PUT    /api/admin/contacts/:id
// DELETE /api/admin/contacts/:id
// GET    /api/admin/newsletter
// DELETE /api/admin/newsletter/:id
// ═══════════════════════════════════════════════════════════════

const express    = require('express');
const Order      = require('../models/Order');
const User       = require('../models/User');
const Contact    = require('../models/Contact');
const Newsletter = require('../models/Newsletter');
const { protect, adminOnly } = require('../middleware/auth');
const { sendEmail } = require('../utils/email');

const router = express.Router();

// All admin routes require authentication + admin role
router.use(protect, adminOnly);

// ═══════════════════════════════════════════════════════════════
// DASHBOARD STATS
// ═══════════════════════════════════════════════════════════════
router.get('/stats', async (req, res, next) => {
  try {
    const [
      totalOrders, pendingOrders, inProgressOrders, completedOrders,
      totalUsers, totalContacts, newContacts, totalSubscribers,
      recentOrders
    ] = await Promise.all([
      Order.countDocuments(),
      Order.countDocuments({ status: 'pending' }),
      Order.countDocuments({ status: 'in-progress' }),
      Order.countDocuments({ status: 'completed' }),
      User.countDocuments({ role: 'student' }),
      Contact.countDocuments(),
      Contact.countDocuments({ status: 'new' }),
      Newsletter.countDocuments({ isActive: true }),
      Order.find().sort({ createdAt: -1 }).limit(5).select('orderNumber clientName workType status createdAt estimatedPrice')
    ]);

    // Revenue estimate (sum of agreedPrice on paid/completed orders)
    const revenueData = await Order.aggregate([
      { $match: { status: { $in: ['paid','completed'] }, agreedPrice: { $exists: true, $ne: null } } },
      { $group: { _id: null, total: { $sum: '$agreedPrice' } } }
    ]);
    const totalRevenue = revenueData[0]?.total || 0;

    // Orders this month
    const monthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
    const ordersThisMonth = await Order.countDocuments({ createdAt: { $gte: monthStart } });

    // Orders by status breakdown
    const ordersByStatus = await Order.aggregate([
      { $group: { _id: '$status', count: { $sum: 1 } } }
    ]);

    // Orders by work type
    const ordersByType = await Order.aggregate([
      { $group: { _id: '$workType', count: { $sum: 1 } } },
      { $sort: { count: -1 } },
      { $limit: 6 }
    ]);

    // New signups last 7 days
    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
    const newUsersThisWeek = await User.countDocuments({ createdAt: { $gte: weekAgo } });

    res.json({
      success: true,
      stats: {
        orders: {
          total: totalOrders,
          pending: pendingOrders,
          inProgress: inProgressOrders,
          completed: completedOrders,
          thisMonth: ordersThisMonth,
          byStatus: ordersByStatus,
          byType: ordersByType
        },
        users:       { total: totalUsers, newThisWeek: newUsersThisWeek },
        contacts:    { total: totalContacts, new: newContacts },
        subscribers: { active: totalSubscribers },
        revenue:     { total: totalRevenue },
        recentOrders
      }
    });
  } catch (err) {
    next(err);
  }
});

// ═══════════════════════════════════════════════════════════════
// ORDERS
// ═══════════════════════════════════════════════════════════════
router.get('/orders', async (req, res, next) => {
  try {
    const page   = parseInt(req.query.page)   || 1;
    const limit  = parseInt(req.query.limit)  || 20;
    const status = req.query.status;
    const search = req.query.search;
    const skip   = (page - 1) * limit;

    const filter = {};
    if (status && status !== 'all') filter.status = status;
    if (search) {
      filter.$or = [
        { orderNumber:  { $regex: search, $options: 'i' } },
        { clientName:   { $regex: search, $options: 'i' } },
        { clientEmail:  { $regex: search, $options: 'i' } },
        { topic:        { $regex: search, $options: 'i' } },
      ];
    }

    const [orders, total] = await Promise.all([
      Order.find(filter).sort({ createdAt: -1 }).skip(skip).limit(limit),
      Order.countDocuments(filter)
    ]);

    res.json({ success: true, orders, total, page, pages: Math.ceil(total / limit) });
  } catch (err) {
    next(err);
  }
});

router.get('/orders/:id', async (req, res, next) => {
  try {
    const order = await Order.findById(req.params.id).populate('user', 'firstName lastName email');
    if (!order) return res.status(404).json({ success: false, message: 'Order not found.' });
    res.json({ success: true, order });
  } catch (err) {
    next(err);
  }
});

router.put('/orders/:id', async (req, res, next) => {
  try {
    const allowed = [
      'status','agreedPrice','adminNotes','assignedWriter',
      'quotedAt','confirmedAt','paidAt','deliveredAt','completedAt'
    ];
    const updates = {};
    allowed.forEach(f => { if (req.body[f] !== undefined) updates[f] = req.body[f]; });

    // Auto-set timestamps
    if (req.body.status === 'quoted'    && !req.body.quotedAt)    updates.quotedAt    = new Date();
    if (req.body.status === 'confirmed' && !req.body.confirmedAt) updates.confirmedAt = new Date();
    if (req.body.status === 'paid'      && !req.body.paidAt)      updates.paidAt      = new Date();
    if (req.body.status === 'delivered' && !req.body.deliveredAt) updates.deliveredAt = new Date();
    if (req.body.status === 'completed' && !req.body.completedAt) updates.completedAt = new Date();

    const order = await Order.findByIdAndUpdate(req.params.id, updates, { new: true });
    if (!order) return res.status(404).json({ success: false, message: 'Order not found.' });

    // Notify client of status change if email is configured
    if (req.body.status && req.body.notifyClient !== false) {
      const statusMessages = {
        'quoted':      'Your order has been reviewed and we have a price quote ready for you.',
        'in-progress': 'Great news! Your writer has started working on your order.',
        'delivered':   'Your completed work has been delivered. Please check your email.',
        'completed':   'Your order is complete. Thank you for choosing Bigman Academic Services!'
      };
      const msg = statusMessages[req.body.status];
      if (msg) {
        sendEmail({
          to: order.clientEmail,
          subject: `Order ${order.orderNumber} Update — Bigman Academic Services`,
          html: `<p style="font-family:Arial;color:#475569;padding:24px;">
            Hi <strong>${order.clientName}</strong>,<br><br>
            ${msg}<br><br>
            Order: <strong>${order.orderNumber}</strong><br><br>
            <a href="${process.env.FRONTEND_URL}/dashboard.html" style="color:#194ba5;font-weight:700;">View in Dashboard →</a><br><br>
            Need to talk? 
            <a href="https://www.instagram.com/bigman_academic_services" style="color:#194ba5;">@bigman_academic_services</a>
          </p>`
        }).catch(e => console.error('Status email error:', e));
      }
    }

    res.json({ success: true, order });
  } catch (err) {
    next(err);
  }
});

// ═══════════════════════════════════════════════════════════════
// USERS
// ═══════════════════════════════════════════════════════════════
router.get('/users', async (req, res, next) => {
  try {
    const page   = parseInt(req.query.page)  || 1;
    const limit  = parseInt(req.query.limit) || 20;
    const search = req.query.search;
    const skip   = (page - 1) * limit;

    const filter = { role: 'student' };
    if (search) {
      filter.$or = [
        { firstName: { $regex: search, $options: 'i' } },
        { lastName:  { $regex: search, $options: 'i' } },
        { email:     { $regex: search, $options: 'i' } },
      ];
    }

    const [users, total] = await Promise.all([
      User.find(filter).sort({ createdAt: -1 }).skip(skip).limit(limit),
      User.countDocuments(filter)
    ]);

    res.json({ success: true, users, total, page, pages: Math.ceil(total / limit) });
  } catch (err) {
    next(err);
  }
});

router.get('/users/:id', async (req, res, next) => {
  try {
    const user   = await User.findById(req.params.id);
    if (!user) return res.status(404).json({ success: false, message: 'User not found.' });
    const orders = await Order.find({ $or: [{ user: user._id }, { clientEmail: user.email }] })
      .sort({ createdAt: -1 }).limit(10);
    res.json({ success: true, user, orders });
  } catch (err) {
    next(err);
  }
});

router.put('/users/:id', async (req, res, next) => {
  try {
    const allowed = ['firstName','lastName','phone','country','isActive','serviceNeeded'];
    const updates = {};
    allowed.forEach(f => { if (req.body[f] !== undefined) updates[f] = req.body[f]; });
    const user = await User.findByIdAndUpdate(req.params.id, updates, { new: true, runValidators: true });
    if (!user) return res.status(404).json({ success: false, message: 'User not found.' });
    res.json({ success: true, user });
  } catch (err) {
    next(err);
  }
});

router.delete('/users/:id', async (req, res, next) => {
  try {
    // Soft delete — just deactivate
    const user = await User.findByIdAndUpdate(req.params.id, { isActive: false }, { new: true });
    if (!user) return res.status(404).json({ success: false, message: 'User not found.' });
    res.json({ success: true, message: 'User deactivated.' });
  } catch (err) {
    next(err);
  }
});

// ═══════════════════════════════════════════════════════════════
// CONTACTS
// ═══════════════════════════════════════════════════════════════
router.get('/contacts', async (req, res, next) => {
  try {
    const page   = parseInt(req.query.page)  || 1;
    const limit  = parseInt(req.query.limit) || 20;
    const status = req.query.status;
    const skip   = (page - 1) * limit;

    const filter = {};
    if (status && status !== 'all') filter.status = status;

    const [contacts, total] = await Promise.all([
      Contact.find(filter).sort({ createdAt: -1 }).skip(skip).limit(limit),
      Contact.countDocuments(filter)
    ]);

    res.json({ success: true, contacts, total, page, pages: Math.ceil(total / limit) });
  } catch (err) {
    next(err);
  }
});

router.put('/contacts/:id', async (req, res, next) => {
  try {
    const allowed = ['status','adminNotes'];
    const updates = {};
    allowed.forEach(f => { if (req.body[f] !== undefined) updates[f] = req.body[f]; });
    const contact = await Contact.findByIdAndUpdate(req.params.id, updates, { new: true });
    if (!contact) return res.status(404).json({ success: false, message: 'Contact not found.' });
    res.json({ success: true, contact });
  } catch (err) {
    next(err);
  }
});

router.delete('/contacts/:id', async (req, res, next) => {
  try {
    await Contact.findByIdAndDelete(req.params.id);
    res.json({ success: true, message: 'Contact deleted.' });
  } catch (err) {
    next(err);
  }
});

// ═══════════════════════════════════════════════════════════════
// NEWSLETTER
// ═══════════════════════════════════════════════════════════════
router.get('/newsletter', async (req, res, next) => {
  try {
    const page   = parseInt(req.query.page)  || 1;
    const limit  = parseInt(req.query.limit) || 50;
    const skip   = (page - 1) * limit;

    const [subscribers, total] = await Promise.all([
      Newsletter.find({ isActive: true }).sort({ createdAt: -1 }).skip(skip).limit(limit),
      Newsletter.countDocuments({ isActive: true })
    ]);

    res.json({ success: true, subscribers, total, page, pages: Math.ceil(total / limit) });
  } catch (err) {
    next(err);
  }
});

// Send newsletter broadcast
router.post('/newsletter/broadcast', async (req, res, next) => {
  try {
    const { subject, html, testOnly, testEmail } = req.body;
    if (!subject || !html) {
      return res.status(400).json({ success: false, message: 'Subject and html body are required.' });
    }

    if (testOnly && testEmail) {
      await sendEmail({ to: testEmail, subject, html });
      return res.json({ success: true, message: `Test email sent to ${testEmail}` });
    }

    // Real broadcast
    const subscribers = await Newsletter.find({ isActive: true }).select('email');
    let sent = 0, failed = 0;

    for (const sub of subscribers) {
      try {
        await sendEmail({ to: sub.email, subject, html });
        sent++;
      } catch {
        failed++;
      }
    }

    res.json({ success: true, message: `Broadcast complete. Sent: ${sent}, Failed: ${failed}` });
  } catch (err) {
    next(err);
  }
});

router.delete('/newsletter/:id', async (req, res, next) => {
  try {
    const sub = await Newsletter.findByIdAndUpdate(req.params.id, { isActive: false }, { new: true });
    if (!sub) return res.status(404).json({ success: false, message: 'Subscriber not found.' });
    res.json({ success: true, message: 'Subscriber removed.' });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
