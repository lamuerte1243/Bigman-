'use strict';

// ═══════════════════════════════════════════════════════════════
// USER MODEL
// ═══════════════════════════════════════════════════════════════

const mongoose = require('mongoose');
const bcrypt   = require('bcryptjs');

const UserSchema = new mongoose.Schema({

  firstName: {
    type: String,
    required: [true, 'First name is required'],
    trim: true,
    maxlength: [50, 'First name cannot exceed 50 characters']
  },

  lastName: {
    type: String,
    required: [true, 'Last name is required'],
    trim: true,
    maxlength: [50, 'Last name cannot exceed 50 characters']
  },

  email: {
    type: String,
    required: [true, 'Email is required'],
    unique: true,
    lowercase: true,
    trim: true,
    match: [/^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'Please enter a valid email address']
  },

  password: {
    type: String,
    required: [true, 'Password is required'],
    minlength: [8, 'Password must be at least 8 characters'],
    select: false   // never returned in queries by default
  },

  role: {
    type: String,
    enum: ['student', 'admin'],
    default: 'student'
  },

  serviceNeeded: {
    type: String,
    trim: true
  },

  // Profile extras
  phone: { type: String, trim: true },
  country: { type: String, trim: true },
  timezone: { type: String, trim: true },

  // Email verification
  isEmailVerified: { type: Boolean, default: false },
  emailVerifyToken: { type: String, select: false },
  emailVerifyExpires: { type: Date, select: false },

  // Password reset
  passwordResetToken: { type: String, select: false },
  passwordResetExpires: { type: Date, select: false },

  // Account state
  isActive: { type: Boolean, default: true },

  // Refresh token (hashed)
  refreshToken: { type: String, select: false },

  lastLoginAt: { type: Date },
  lastLoginIp: { type: String },

}, {
  timestamps: true,    // adds createdAt, updatedAt automatically
  toJSON: {
    virtuals: true,
    transform: (doc, ret) => {
      delete ret.password;
      delete ret.refreshToken;
      delete ret.emailVerifyToken;
      delete ret.passwordResetToken;
      delete ret.__v;
      return ret;
    }
  }
});

// ── Virtual: full name ────────────────────────────────────────
UserSchema.virtual('fullName').get(function() {
  return `${this.firstName} ${this.lastName}`;
});

// ── Pre-save: hash password ───────────────────────────────────
UserSchema.pre('save', async function(next) {
  if (!this.isModified('password')) return next();
  const salt = await bcrypt.genSalt(12);
  this.password = await bcrypt.hash(this.password, salt);
  next();
});

// ── Instance method: compare password ────────────────────────
UserSchema.methods.comparePassword = async function(candidatePassword) {
  return bcrypt.compare(candidatePassword, this.password);
};

// ── Indexes ───────────────────────────────────────────────────
UserSchema.index({ email: 1 });
UserSchema.index({ createdAt: -1 });

module.exports = mongoose.model('User', UserSchema);
