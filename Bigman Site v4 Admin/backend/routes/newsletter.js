'use strict';

// ═══════════════════════════════════════════════════════════════
// NEWSLETTER ROUTES
// POST /api/newsletter/subscribe
// GET  /api/newsletter/unsubscribe/:token
// ═══════════════════════════════════════════════════════════════

const express    = require('express');
const { v4: uuidv4 } = require('uuid');
const Newsletter = require('../models/Newsletter');
const { newsletterRules, validate } = require('../middleware/validate');
const { sendEmail } = require('../utils/email');

const router = express.Router();

// ── POST /api/newsletter/subscribe ───────────────────────────
router.post('/subscribe', newsletterRules, validate, async (req, res, next) => {
  try {
    const { email, name, source } = req.body;

    const existing = await Newsletter.findOne({ email });
    if (existing) {
      if (existing.isActive) {
        return res.status(200).json({ success: true, message: "You're already subscribed!" });
      }
      // Re-subscribe
      existing.isActive = true;
      await existing.save();
      return res.json({ success: true, message: 'Welcome back! You have been re-subscribed.' });
    }

    const token = uuidv4();

    await Newsletter.create({
      email, name,
      unsubscribeToken: token,
      source: source || 'footer',
      ipAddress: req.ip
    });

    // Send confirmation email (async)
    sendEmail({
      to: email,
      subject: '📚 You\'re subscribed to Bigman Academic Tips!',
      html: `
        <div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto;padding:32px;background:#fff;border-radius:8px;">
          <div style="background:#194ba5;padding:20px;border-radius:6px;text-align:center;margin-bottom:24px;">
            <h1 style="color:#fff;font-size:20px;margin:0;font-family:Arial,sans-serif;">🎓 BIGMAN ACADEMIC SERVICES</h1>
          </div>
          <h2 style="color:#194ba5;">You're subscribed! 🎉</h2>
          <p style="color:#475569;line-height:1.7;">
            Hi${name ? ' ' + name : ''}! You'll now receive expert tips on GED, TEAS, HESI, NCLEX, 
            StraighterLine, online courses, essays, and more — straight to your inbox.
          </p>
          <p style="margin-top:28px;">
            <a href="${process.env.FRONTEND_URL}/api/newsletter/unsubscribe/${token}" 
               style="color:#94a3b8;font-size:12px;">Unsubscribe at any time</a>
          </p>
        </div>`
    }).catch(e => console.error('Newsletter email error:', e));

    res.status(201).json({ success: true, message: 'Subscribed! Check your email for confirmation.' });
  } catch (err) {
    next(err);
  }
});

// ── GET /api/newsletter/unsubscribe/:token ────────────────────
router.get('/unsubscribe/:token', async (req, res, next) => {
  try {
    const sub = await Newsletter.findOne({ unsubscribeToken: req.params.token });

    if (!sub) {
      return res.status(200).send(`
        <html><body style="font-family:Arial;text-align:center;padding:60px;color:#475569;">
          <h2>Link not found or already unsubscribed.</h2>
          <a href="${process.env.FRONTEND_URL}" style="color:#194ba5;">Return to website</a>
        </body></html>`);
    }

    sub.isActive = false;
    await sub.save();

    res.status(200).send(`
      <html><body style="font-family:Arial;text-align:center;padding:60px;color:#475569;">
        <h2 style="color:#194ba5;">You've been unsubscribed.</h2>
        <p>You will no longer receive emails from Bigman Academic Services.</p>
        <a href="${process.env.FRONTEND_URL}" style="color:#194ba5;font-weight:700;">Return to website</a>
      </body></html>`);
  } catch (err) {
    next(err);
  }
});

module.exports = router;
