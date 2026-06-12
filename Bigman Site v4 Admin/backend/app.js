/* ═══════════════════════════════════════════════════════════════
   BIGMAN ACADEMIC SERVICES — EXPRESS APP FACTORY
   ═══════════════════════════════════════════════════════════════
   This file creates and exports the configured Express `app`
   WITHOUT calling app.listen().

   WHY SEPARATE FROM server.js:
   - server.js  → used for VPS/local: creates app, connects DB, listens
   - app.js     → used by Netlify Function: just the Express app object
   - This pattern is the standard way to make Express work serverless

   Both server.js and netlify/functions/api.js import from this file.
   ═══════════════════════════════════════════════════════════════ */

'use strict';

require('dotenv').config();

const express              = require('express');
const cors                 = require('cors');
const helmet               = require('helmet');
const morgan               = require('morgan');
const rateLimit            = require('express-rate-limit');
const mongoSanitize        = require('express-mongo-sanitize');

// ── Route imports
const authRoutes           = require('./routes/auth');
const ordersRoutes         = require('./routes/orders');
const contactsRoutes       = require('./routes/contacts');
const newsletterRoutes     = require('./routes/newsletter');
const adminRoutes          = require('./routes/admin');

// ── DB connection (idempotent — safe to call multiple times)
const connectDB            = require('./config/db');

// ── Connect to MongoDB (won't reconnect if already connected)
connectDB();

// ════════════════════════════════════════════════════════════════
// APP SETUP
// ════════════════════════════════════════════════════════════════
const app = express();

// ── Trust proxy (needed for rate limiting behind Netlify/Nginx)
app.set('trust proxy', 1);

// ── Security headers
app.use(helmet({
  crossOriginResourcePolicy: { policy: 'cross-origin' },
  contentSecurityPolicy: false, // Disabled — frontend handles its own CSP
}));

// ── CORS — allow requests from frontend origins only
const allowedOrigins = [
  process.env.FRONTEND_URL,
  'http://localhost:3000',
  'http://localhost:5500',
  'http://127.0.0.1:5500',
  'http://127.0.0.1:3000',
  // Netlify preview URLs (wildcard subdomain)
].filter(Boolean);

app.use(cors({
  origin: (origin, callback) => {
    // Allow requests with no origin (curl, Postman, same-origin)
    if (!origin) return callback(null, true);
    // Allow any *.netlify.app subdomain for preview deploys
    if (/\.netlify\.app$/.test(origin)) return callback(null, true);
    if (allowedOrigins.includes(origin))  return callback(null, true);
    callback(new Error(`CORS: Origin "${origin}" not allowed`));
  },
  credentials: true,
  methods:     ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization'],
}));

// ── Body parsing (limit request sizes)
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// ── NoSQL injection prevention
app.use(mongoSanitize());

// ── HTTP request logging (development only)
if (process.env.NODE_ENV !== 'production') {
  app.use(morgan('dev'));
}

// ── Global rate limiter (all routes)
const globalLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max:      parseInt(process.env.RATE_LIMIT_MAX || '200'),
  standardHeaders: true,
  legacyHeaders:   false,
  keyGenerator:    (req) => req.headers['x-forwarded-for'] || req.ip || '127.0.0.1',
  message: { error: 'Too many requests, please try again later.' },
});
app.use('/api', globalLimiter);

// ── Stricter limiter for auth routes
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max:      parseInt(process.env.AUTH_RATE_LIMIT_MAX || '20'),
  standardHeaders: true,
  legacyHeaders:   false,
  keyGenerator:    (req) => req.headers['x-forwarded-for'] || req.ip || '127.0.0.1',
  message: { error: 'Too many authentication attempts, please wait 15 minutes.' },
  skipSuccessfulRequests: true,
});

// ════════════════════════════════════════════════════════════════
// ROUTES
// ════════════════════════════════════════════════════════════════

// Health check (unauthenticated — used by Netlify and monitoring tools)
app.get('/api/health', (req, res) => {
  const mongoose = require('mongoose');
  const states   = ['disconnected', 'connected', 'connecting', 'disconnecting'];
  res.json({
    status:   'ok',
    service:  'Bigman Academic Services API',
    version:  '1.0.0',
    db:       states[mongoose.connection.readyState] || 'unknown',
    env:      process.env.NODE_ENV || 'development',
    timestamp: new Date().toISOString(),
  });
});

// Auth routes (with stricter rate limiting)
app.use('/api/auth',       authLimiter, authRoutes);
app.use('/auth',           authLimiter, authRoutes);

// Public routes
app.use('/api/orders',     ordersRoutes);
app.use('/api/contacts',   contactsRoutes);
app.use('/api/newsletter', newsletterRoutes);

// Admin routes (all protected internally by protect + adminOnly middleware)
app.use('/api/admin',      adminRoutes);

// ── 404 handler
app.use((req, res) => {
  res.status(404).json({
    error: 'Not Found',
    message: `Route ${req.method} ${req.originalUrl} does not exist`,
  });
});

// ── Global error handler
app.use((err, req, res, next) => {
  console.error('[Bigman API Error]', err);

  // Mongoose validation errors
  if (err.name === 'ValidationError') {
    const errors = Object.values(err.errors).map(e => ({ field: e.path, msg: e.message }));
    return res.status(422).json({ error: 'Validation failed', errors });
  }

  // Mongoose duplicate key error (e.g. duplicate email)
  if (err.code === 11000) {
    const field = Object.keys(err.keyValue || {})[0] || 'field';
    return res.status(409).json({
      error:   'Duplicate value',
      message: `An account with this ${field} already exists.`,
    });
  }

  // JWT errors
  if (err.name === 'JsonWebTokenError') {
    return res.status(401).json({ error: 'Invalid token' });
  }
  if (err.name === 'TokenExpiredError') {
    return res.status(401).json({ error: 'Token expired' });
  }

  // CORS error
  if (err.message && err.message.startsWith('CORS:')) {
    return res.status(403).json({ error: err.message });
  }

  // Generic server error
  const statusCode = err.statusCode || err.status || 500;
  res.status(statusCode).json({
    error:   err.message || 'Internal server error',
    ...(process.env.NODE_ENV !== 'production' && { stack: err.stack }),
  });
});

module.exports = app;
