/* ═══════════════════════════════════════════════════════════════
   BIGMAN ACADEMIC SERVICES — MONGODB CONNECTION
   ═══════════════════════════════════════════════════════════════
   Idempotent connection — safe to call multiple times.
   On Netlify, Lambda functions reuse warm connections, so we
   check readyState before connecting again.
   ═══════════════════════════════════════════════════════════════ */

'use strict';

const mongoose = require('mongoose');

let isConnecting = false;

async function connectDB() {
  // Already connected or connecting — skip
  if (mongoose.connection.readyState === 1) return;
  if (mongoose.connection.readyState === 2) return; // Connecting
  if (isConnecting) return;

  const uri = process.env.MONGO_URI;
  if (!uri) {
    console.error('[DB] MONGO_URI environment variable is not set!');
    console.error('[DB] Create backend/.env from backend/.env.example and set MONGO_URI');
    throw new Error('MONGO_URI is required');
  }

  isConnecting = true;

  try {
    await mongoose.connect(uri, {
      // These options ensure compatibility across MongoDB driver versions
      serverSelectionTimeoutMS: 10000,  // 10s timeout to find a server
      socketTimeoutMS:          45000,  // 45s socket timeout
      maxPoolSize:              10,     // Max 10 connections in pool
      minPoolSize:              2,      // Keep 2 connections warm
      connectTimeoutMS:         10000,
      // Heartbeat to keep connection alive on serverless
      heartbeatFrequencyMS:     10000,
    });

    console.log(`[DB] ✅ MongoDB connected: ${mongoose.connection.host}`);

    mongoose.connection.on('error', (err) => {
      console.error('[DB] Connection error:', err.message);
    });

    mongoose.connection.on('disconnected', () => {
      console.warn('[DB] MongoDB disconnected. Reconnecting...');
      isConnecting = false;
    });

  } catch (err) {
    isConnecting = false;
    console.error('[DB] ❌ MongoDB connection failed:', err.message);
    throw err;
  } finally {
    isConnecting = false;
  }
}

module.exports = connectDB;
