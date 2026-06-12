'use strict';

// ═══════════════════════════════════════════════════════════════
// ORDER ROUTES
// POST   /api/orders              — Place order (public/student)
// GET    /api/orders/mine         — Get own orders (student)
// GET    /api/orders/:id          — Get single order
// ═══════════════════════════════════════════════════════════════

const express = require('express');
const Order   = require('../models/Order');
const { protect }  = require('../middleware/auth');
const { createOrderRules, validate } = require('../middleware/validate');
const { sendOrderConfirmation, sendAdminOrderNotification } = require('../utils/email');

const router = express.Router();

// ── POST /api/orders ──────────────────────────────────────────
// Public endpoint — guests and logged-in users can place orders
router.post('/', createOrderRules, validate, async (req, res, next) => {
  try {
    const {
      clientName, clientEmail, contactMethod, contactHandle,
      workType, academicLevel, subject, topic, wordCount, numEssays,
      citationStyle, numSources, deadlineOption, exactDeadline,
      specialRequests, fileInstructions, estimatedPrice
    } = req.body;

    // If user is logged in, optionally extract from token
    let userId = null;
    const authHeader = req.headers.authorization;
    if (authHeader?.startsWith('Bearer ')) {
      try {
        const jwt  = require('jsonwebtoken');
        const dec  = jwt.verify(authHeader.split(' ')[1], process.env.JWT_SECRET);
        userId = dec.id;
      } catch { /* not required — guest checkout ok */ }
    }

    // Compute dueAt from deadlineOption
    let dueAt = null;
    if (exactDeadline) {
      dueAt = new Date(exactDeadline);
    } else if (deadlineOption) {
      const now   = new Date();
      const maps  = {
        '6 hours':  6*60*60*1000,
        '12 hours': 12*60*60*1000,
        '24 hours': 24*60*60*1000,
        '3 days':   3*24*60*60*1000,
        '7 days':   7*24*60*60*1000,
        '14 days':  14*24*60*60*1000,
      };
      const ms = maps[deadlineOption];
      if (ms) dueAt = new Date(now.getTime() + ms);
    }

    const order = await Order.create({
      user: userId,
      clientName, clientEmail, contactMethod, contactHandle,
      workType, academicLevel, subject, topic,
      wordCount: parseInt(wordCount),
      numEssays, citationStyle, numSources,
      deadlineOption, exactDeadline: exactDeadline ? new Date(exactDeadline) : undefined,
      dueAt,
      specialRequests, fileInstructions, estimatedPrice,
      status: 'pending'
    });

    // Send emails async
    sendOrderConfirmation(order).catch(e => console.error('Order email error:', e));
    sendAdminOrderNotification(order).catch(e => console.error('Admin order email error:', e));

    res.status(201).json({
      success: true,
      message: 'Order placed successfully! We will contact you within 15 minutes.',
      order: {
        id:          order._id,
        orderNumber: order.orderNumber,
        status:      order.status,
        workType:    order.workType,
        topic:       order.topic,
        dueAt:       order.dueAt,
        createdAt:   order.createdAt
      }
    });
  } catch (err) {
    next(err);
  }
});

// ── GET /api/orders/mine ──────────────────────────────────────
router.get('/mine', protect, async (req, res, next) => {
  try {
    const page  = parseInt(req.query.page)  || 1;
    const limit = parseInt(req.query.limit) || 10;
    const skip  = (page - 1) * limit;

    const [orders, total] = await Promise.all([
      Order.find({ $or: [{ user: req.user._id }, { clientEmail: req.user.email }] })
        .sort({ createdAt: -1 })
        .skip(skip)
        .limit(limit),
      Order.countDocuments({ $or: [{ user: req.user._id }, { clientEmail: req.user.email }] })
    ]);

    res.json({ success: true, orders, total, page, pages: Math.ceil(total / limit) });
  } catch (err) {
    next(err);
  }
});

// ── GET /api/orders/:id ───────────────────────────────────────
router.get('/:id', protect, async (req, res, next) => {
  try {
    const order = await Order.findById(req.params.id);
    if (!order) return res.status(404).json({ success: false, message: 'Order not found.' });

    // Students can only view their own orders
    if (req.user.role !== 'admin') {
      const owns = (order.user?.toString() === req.user._id.toString()) ||
                   (order.clientEmail === req.user.email);
      if (!owns) return res.status(403).json({ success: false, message: 'Access denied.' });
    }

    res.json({ success: true, order });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
