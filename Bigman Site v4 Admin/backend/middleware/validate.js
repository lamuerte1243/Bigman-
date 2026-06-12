'use strict';

// ═══════════════════════════════════════════════════════════════
// INPUT VALIDATION MIDDLEWARE (express-validator)
// ═══════════════════════════════════════════════════════════════

const { body, param, query, validationResult } = require('express-validator');

// ── Run validation result check ───────────────────────────────
exports.validate = (req, res, next) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(422).json({
      success: false,
      message: 'Validation failed',
      errors: errors.array().map(e => ({ field: e.path, message: e.msg }))
    });
  }
  next();
};

// ── Auth validators ───────────────────────────────────────────
exports.signupRules = [
  body('firstName').trim().notEmpty().withMessage('First name is required').isLength({ max: 50 }),
  body('lastName').trim().notEmpty().withMessage('Last name is required').isLength({ max: 50 }),
  body('email').trim().isEmail().withMessage('Please enter a valid email address').normalizeEmail(),
  body('password')
    .isLength({ min: 8 }).withMessage('Password must be at least 8 characters')
    .matches(/[A-Z]/).withMessage('Password must contain at least one uppercase letter')
    .matches(/[0-9]/).withMessage('Password must contain at least one number'),
  body('serviceNeeded').optional().trim(),
];

exports.loginRules = [
  body('email').trim().isEmail().withMessage('Please enter a valid email address').normalizeEmail(),
  body('password').notEmpty().withMessage('Password is required'),
];

exports.changePasswordRules = [
  body('currentPassword').notEmpty().withMessage('Current password is required'),
  body('newPassword')
    .isLength({ min: 8 }).withMessage('New password must be at least 8 characters')
    .matches(/[A-Z]/).withMessage('Must contain uppercase')
    .matches(/[0-9]/).withMessage('Must contain a number'),
];

// ── Order validators ──────────────────────────────────────────
exports.createOrderRules = [
  body('clientName').trim().notEmpty().withMessage('Your name is required'),
  body('clientEmail').trim().isEmail().withMessage('Please enter a valid email address').normalizeEmail(),
  body('workType').notEmpty().withMessage('Type of work is required')
    .isIn(['essay','research-paper','discussion-post','dissertation-chapter','thesis',
           'case-study','annotated-bibliography','literature-review','term-paper',
           'coursework','lab-report','other']),
  body('academicLevel').notEmpty().withMessage('Academic level is required')
    .isIn(['high-school','undergraduate','junior','graduate','doctoral','professional']),
  body('subject').trim().notEmpty().withMessage('Subject is required'),
  body('topic').trim().notEmpty().withMessage('Essay topic is required'),
  body('wordCount').isInt({ min: 250, max: 100000 }).withMessage('Word count must be between 250 and 100,000'),
];

// ── Contact validators ────────────────────────────────────────
exports.contactRules = [
  body('firstName').trim().notEmpty().withMessage('First name is required'),
  body('lastName').trim().notEmpty().withMessage('Last name is required'),
  body('email').trim().isEmail().withMessage('Please enter a valid email address').normalizeEmail(),
  body('message').trim().notEmpty().withMessage('Message is required')
    .isLength({ min: 10 }).withMessage('Message must be at least 10 characters'),
];

// ── Newsletter validators ────────────────────────────────────
exports.newsletterRules = [
  body('email').trim().isEmail().withMessage('Please enter a valid email address').normalizeEmail(),
];
