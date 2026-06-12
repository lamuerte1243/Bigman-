/* ═══════════════════════════════════════════════════════════════
   BIGMAN ACADEMIC SERVICES — VPS / LOCAL SERVER ENTRY POINT
   ═══════════════════════════════════════════════════════════════
   This file is used ONLY when running on:
     - Your local machine:  node server.js  (or npm run dev)
     - VPS production:      pm2 start server.js --name bigman-api

   For Netlify (serverless), see netlify/functions/api.js instead.
   Both environments import the same Express app from app.js.
   ═══════════════════════════════════════════════════════════════ */

'use strict';

require('dotenv').config();

// ── Import the shared Express app (app.js also connects MongoDB)
const app  = require('./app');
const PORT = parseInt(process.env.PORT || '5000', 10);

// ═══════════════════════════════════════════════════════════════
// START LISTENING
// ═══════════════════════════════════════════════════════════════
const server = app.listen(PORT, () => {
  console.log(`
╔═══════════════════════════════════════════════════════════╗
║          BIGMAN ACADEMIC SERVICES — API SERVER            ║
╠═══════════════════════════════════════════════════════════╣
║  Local:   http://localhost:${PORT}                          ║
║  Health:  http://localhost:${PORT}/api/health               ║
║  Mode:    ${(process.env.NODE_ENV || 'development').padEnd(46)}║
╚═══════════════════════════════════════════════════════════╝
  `);
});

// ── Graceful shutdown handlers
process.on('SIGTERM', () => {
  console.log('[Server] SIGTERM received — shutting down gracefully...');
  server.close(() => {
    console.log('[Server] HTTP server closed');
    const mongoose = require('mongoose');
    mongoose.connection.close(false, () => {
      console.log('[Server] MongoDB connection closed');
      process.exit(0);
    });
  });
});

process.on('SIGINT', () => {
  console.log('\n[Server] SIGINT received — shutting down...');
  server.close(() => process.exit(0));
});

// ── Unhandled promise rejections
process.on('unhandledRejection', (err) => {
  console.error('[Server] Unhandled Rejection:', err.message);
  server.close(() => process.exit(1));
});

module.exports = server;
