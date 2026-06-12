'use strict';

// ═══════════════════════════════════════════════════════════════
// AUTH ROUTES
// POST /api/auth/signup
// POST /api/auth/login
// POST /api/auth/refresh
// POST /api/auth/logout
// GET  /api/auth/me
// PUT  /api/auth/me
// POST /api/auth/change-password
// ═══════════════════════════════════════════════════════════════

const express       = require('express');
const rateLimit     = require('express-rate-limit');
const User          = require('../models/User');
const { protect, generateTokens, verifyRefreshToken } = require('../middleware/auth');
const { signupRules, loginRules, changePasswordRules, validate } = require('../middleware/validate');
const { sendWelcomeEmail } = require('../utils/email');

const router = express.Router();

// Strict rate limit on auth endpoints
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: parseInt(process.env.AUTH_RATE_LIMIT_MAX) || 10,
  message: { success: false, message: 'Too many attempts. Please wait 15 minutes.' }
});

// ── POST /api/auth/signup ─────────────────────────────────────
router.post('/signup', authLimiter, signupRules, validate, async (req, res, next) => {
  try {
    const { firstName, lastName, email, password, serviceNeeded } = req.body;

    // Check if email already exists
    const existing = await User.findOne({ email });
    if (existing) {
      return res.status(409).json({ success: false, message: 'An account with this email already exists.' });
    }

    // Create user
    const user = await User.create({ firstName, lastName, email, password, serviceNeeded });

    // Generate tokens
    const { accessToken, refreshToken } = generateTokens(user._id);

    // Store hashed refresh token
    const bcrypt = require('bcryptjs');
    const hashed = await bcrypt.hash(refreshToken, 10);
    user.refreshToken  = hashed;
    user.lastLoginAt   = new Date();
    user.lastLoginIp   = req.ip;
    await user.save({ validateBeforeSave: false });

    // Send welcome email (async, don't block response)
    sendWelcomeEmail(user).catch(err => console.error('Welcome email error:', err));

    res.status(201).json({
      success: true,
      message: 'Account created successfully!',
      accessToken,
      refreshToken,
      user: {
        id:           user._id,
        firstName:    user.firstName,
        lastName:     user.lastName,
        fullName:     user.fullName,
        email:        user.email,
        role:         user.role,
        serviceNeeded: user.serviceNeeded,
        createdAt:    user.createdAt
      }
    });
  } catch (err) {
    next(err);
  }
});

// ── POST /api/auth/login ──────────────────────────────────────
router.post('/login', authLimiter, loginRules, validate, async (req, res, next) => {
  try {
    const { email, password } = req.body;

    // Select password field (excluded by default)
    const user = await User.findOne({ email }).select('+password +refreshToken +isActive');

    if (!user || !(await user.comparePassword(password))) {
      return res.status(401).json({ success: false, message: 'Invalid email or password.' });
    }

    if (!user.isActive) {
      return res.status(403).json({ success: false, message: 'This account has been deactivated. Please contact support.' });
    }

    const { accessToken, refreshToken } = generateTokens(user._id);

    const bcrypt = require('bcryptjs');
    user.refreshToken = await bcrypt.hash(refreshToken, 10);
    user.lastLoginAt  = new Date();
    user.lastLoginIp  = req.ip;
    await user.save({ validateBeforeSave: false });

    res.json({
      success: true,
      message: 'Login successful',
      accessToken,
      refreshToken,
      user: {
        id:           user._id,
        firstName:    user.firstName,
        lastName:     user.lastName,
        fullName:     user.fullName,
        email:        user.email,
        role:         user.role,
        serviceNeeded: user.serviceNeeded,
        lastLoginAt:  user.lastLoginAt
      }
    });
  } catch (err) {
    next(err);
  }
});

// ── POST /api/auth/refresh ────────────────────────────────────
router.post('/refresh', async (req, res, next) => {
  try {
    const token = req.headers['x-refresh-token'] || req.body.refreshToken;
    if (!token) return res.status(401).json({ success: false, message: 'No refresh token provided.' });

    let decoded;
    try {
      decoded = verifyRefreshToken(token);
    } catch {
      return res.status(401).json({ success: false, message: 'Invalid or expired refresh token. Please log in again.' });
    }

    const user = await User.findById(decoded.id).select('+refreshToken +isActive');
    if (!user || !user.isActive) {
      return res.status(401).json({ success: false, message: 'User not found.' });
    }

    // Verify stored refresh token matches
    const bcrypt = require('bcryptjs');
    const valid  = await bcrypt.compare(token, user.refreshToken || '');
    if (!valid) {
      return res.status(401).json({ success: false, message: 'Token reuse detected. Please log in again.' });
    }

    const { accessToken, refreshToken: newRefresh } = generateTokens(user._id);
    user.refreshToken = await bcrypt.hash(newRefresh, 10);
    await user.save({ validateBeforeSave: false });

    res.json({ success: true, accessToken, refreshToken: newRefresh });
  } catch (err) {
    next(err);
  }
});

// ── POST /api/auth/logout ─────────────────────────────────────
router.post('/logout', protect, async (req, res, next) => {
  try {
    await User.findByIdAndUpdate(req.user._id, { $unset: { refreshToken: 1 } });
    res.json({ success: true, message: 'Logged out successfully.' });
  } catch (err) {
    next(err);
  }
});

// ── GET /api/auth/me ──────────────────────────────────────────
router.get('/me', protect, (req, res) => {
  res.json({ success: true, user: req.user });
});

// ── PUT /api/auth/me ──────────────────────────────────────────
router.put('/me', protect, async (req, res, next) => {
  try {
    const allowed = ['firstName','lastName','phone','country','timezone','serviceNeeded'];
    const updates = {};
    allowed.forEach(f => { if (req.body[f] !== undefined) updates[f] = req.body[f]; });

    const user = await User.findByIdAndUpdate(req.user._id, updates, {
      new: true, runValidators: true
    });

    res.json({ success: true, user });
  } catch (err) {
    next(err);
  }
});

// ── POST /api/auth/change-password ────────────────────────────
router.post('/change-password', protect, changePasswordRules, validate, async (req, res, next) => {
  try {
    const { currentPassword, newPassword } = req.body;
    const user = await User.findById(req.user._id).select('+password');

    if (!(await user.comparePassword(currentPassword))) {
      return res.status(400).json({ success: false, message: 'Current password is incorrect.' });
    }

    user.password = newPassword;
    await user.save();

    res.json({ success: true, message: 'Password changed successfully.' });
  } catch (err) {
    next(err);
  }
});

module.exports = router;
