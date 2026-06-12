'use strict';

// ═══════════════════════════════════════════════════════════════
// CONTACT REQUEST MODEL
// ═══════════════════════════════════════════════════════════════

const mongoose = require('mongoose');

const ContactSchema = new mongoose.Schema({

  firstName:   { type: String, required: true, trim: true },
  lastName:    { type: String, required: true, trim: true },
  email:       { type: String, required: true, lowercase: true, trim: true },
  phone:       { type: String, trim: true },

  service: {
    type: String,
    enum: [
      'essay-writing','research-paper','dissertation','tutoring',
      'exam-prep','online-course','online-class','other'
    ],
    default: 'other'
  },

  deadline: { type: String },
  pages:    { type: String },
  message:  { type: String, required: true, trim: true },

  // Internal
  status: {
    type: String,
    enum: ['new','read','replied','archived'],
    default: 'new'
  },

  adminNotes: { type: String, trim: true },
  ipAddress:  { type: String },

  // If this contact came from a logged-in user
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    default: null
  }

}, {
  timestamps: true,
  toJSON: { transform: (doc, ret) => { delete ret.__v; return ret; } }
});

ContactSchema.index({ email: 1 });
ContactSchema.index({ status: 1 });
ContactSchema.index({ createdAt: -1 });

module.exports = mongoose.model('Contact', ContactSchema);
