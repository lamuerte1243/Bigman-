'use strict';

// ═══════════════════════════════════════════════════════════════
// CONTACT ROUTES
// POST /api/contacts   — Submit contact form (public)
// ═══════════════════════════════════════════════════════════════

const express  = require('express');
const Contact  = require('../models/Contact');
const { contactRules, validate } = require('../middleware/validate');
const { sendAdminContactNotification, sendContactAutoReply } = require('../utils/email');

const router = express.Router();

// ── POST /api/contacts ────────────────────────────────────────
router.post('/', contactRules, validate, async (req, res, next) => {
  try {
    const {
      firstName, lastName, email, phone, service,
      deadline, pages, message
    } = req.body;

    const contact = await Contact.create({
      firstName, lastName, email, phone,
      service: service || 'other',
      deadline, pages, message,
      ipAddress: req.ip,
      status: 'new'
    });

    // Fire emails async — don't block response
    sendAdminContactNotification(contact).catch(e => console.error('Admin contact email error:', e));
    sendContactAutoReply(contact).catch(e => console.error('Auto-reply email error:', e));

    res.status(201).json({
      success: true,
      message: "Message received! We'll be in touch within 2–4 hours."
    });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
