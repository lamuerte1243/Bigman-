'use strict';

// ═══════════════════════════════════════════════════════════════
// NEWSLETTER SUBSCRIBER MODEL
// ═══════════════════════════════════════════════════════════════

const mongoose = require('mongoose');

const NewsletterSchema = new mongoose.Schema({

  email: {
    type: String,
    required: true,
    unique: true,
    lowercase: true,
    trim: true,
    match: [/^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Please enter a valid email address']
  },

  name: { type: String, trim: true },

  isActive: { type: Boolean, default: true },

  // Unsubscribe token (sent in email footer)
  unsubscribeToken: { type: String },

  source: {
    type: String,
    enum: ['footer','blog','popup','other'],
    default: 'footer'
  },

  ipAddress: { type: String },

}, {
  timestamps: true,
  toJSON: { transform: (doc, ret) => { delete ret.__v; delete ret.unsubscribeToken; return ret; } }
});

NewsletterSchema.index({ email: 1 }, { unique: true });
NewsletterSchema.index({ isActive: 1 });

module.exports = mongoose.model('Newsletter', NewsletterSchema);
