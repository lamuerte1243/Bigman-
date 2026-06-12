'use strict';

// ═══════════════════════════════════════════════════════════════
// ORDER MODEL
// ═══════════════════════════════════════════════════════════════

const mongoose = require('mongoose');

const OrderSchema = new mongoose.Schema({

  orderNumber: {
    type: String,
    unique: true
    // Generated in pre-save hook
  },

  // Who placed the order (optional — can be a guest)
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    default: null
  },

  // Client contact info (always stored directly for guest orders too)
  clientName:    { type: String, required: true, trim: true },
  clientEmail:   { type: String, required: true, lowercase: true, trim: true },
  contactMethod: {
    type: String,
    enum: ['email','instagram','whatsapp','text'],
    default: 'email'
  },
  contactHandle: { type: String, trim: true },  // Instagram handle / WhatsApp number

  // Work details
  workType: {
    type: String,
    required: true,
    enum: [
      'essay','research-paper','discussion-post','dissertation-chapter',
      'thesis','case-study','annotated-bibliography','literature-review',
      'term-paper','coursework','lab-report','other'
    ]
  },

  academicLevel: {
    type: String,
    required: true,
    enum: ['high-school','undergraduate','junior','graduate','doctoral','professional']
  },

  subject:       { type: String, required: true, trim: true },
  topic:         { type: String, required: true, trim: true },
  wordCount:     { type: Number, required: true, min: 250 },
  numEssays:     { type: String, default: '1' },
  citationStyle: { type: String, default: 'any' },
  numSources:    { type: String, default: 'any' },

  // Deadline
  deadlineOption:  { type: String },   // "24 hours", "3 days", etc.
  exactDeadline:   { type: Date },
  dueAt:           { type: Date },     // Computed deadline timestamp

  // Additional details
  specialRequests: { type: String, trim: true },
  fileInstructions: { type: String, trim: true },

  // Pricing
  estimatedPrice:  { type: String },
  agreedPrice:     { type: Number },    // Set by admin after review
  currency:        { type: String, default: 'USD' },

  // Order lifecycle
  status: {
    type: String,
    enum: [
      'pending',       // Just submitted, waiting for admin review
      'quoted',        // Admin has set a price, waiting for client confirmation
      'confirmed',     // Client confirmed, payment pending
      'paid',          // Payment received
      'in-progress',   // Writer working on it
      'review',        // Delivered internally, under admin review
      'delivered',     // Sent to client
      'revision',      // Client requested revision
      'completed',     // Client accepted, order closed
      'cancelled',     // Cancelled by either party
      'refunded'       // Refund issued
    ],
    default: 'pending'
  },

  // Internal notes from admin/writer
  adminNotes: { type: String, trim: true },

  // Assignment
  assignedWriter: { type: String, trim: true },

  // Timestamps for key events
  quotedAt:    { type: Date },
  confirmedAt: { type: Date },
  paidAt:      { type: Date },
  deliveredAt: { type: Date },
  completedAt: { type: Date },

}, {
  timestamps: true,
  toJSON: { virtuals: true, transform: (doc, ret) => { delete ret.__v; return ret; } }
});

// ── Pre-save: generate order number ──────────────────────────
OrderSchema.pre('save', async function(next) {
  if (this.orderNumber) return next();
  const date = new Date();
  const year  = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const random = Math.floor(Math.random() * 90000) + 10000;
  this.orderNumber = `BAS-${year}${month}-${random}`;
  next();
});

// ── Indexes ───────────────────────────────────────────────────
OrderSchema.index({ clientEmail: 1 });
OrderSchema.index({ status: 1 });
OrderSchema.index({ createdAt: -1 });
OrderSchema.index({ orderNumber: 1 }, { unique: true });

module.exports = mongoose.model('Order', OrderSchema);
