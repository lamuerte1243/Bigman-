/* ═══════════════════════════════════════════════════════════════
   BIGMAN ACADEMIC SERVICES — NETLIFY SERVERLESS FUNCTION
   ═══════════════════════════════════════════════════════════════
   This is the entry point for ALL /api/* requests on Netlify.
   It wraps the full Express app using serverless-http, so the
   exact same backend/server.js code runs in both environments:
     • Netlify (serverless, preview)     → this file
     • VPS / proper hosting (production) → node backend/server.js

   HOW THE ROUTING WORKS:
   1. Browser hits: GET https://yoursite.netlify.app/api/auth/login
   2. netlify.toml rewrites that to:  /.netlify/functions/api/auth/login
   3. This function receives it, strips the prefix, passes to Express
   4. Express routes handle it exactly as on the VPS

   ENVIRONMENT VARIABLES:
   Set these in Netlify UI → Site Settings → Environment Variables.
   They mirror backend/.env.example exactly.
   ═══════════════════════════════════════════════════════════════ */

'use strict';

const serverless = require('serverless-http');

// ── Import the full Express app from the backend directory
// The app must export just the `app` object (not call app.listen)
let handler;

try {
  const app = require('../../backend/app');   // see note below
  handler   = serverless(app, {
    // Tell serverless-http the basePath so Express routing works correctly
    basePath: '/api'
  });
} catch (err) {
  // If app fails to load (bad env vars, etc.) return a 500 with details
  console.error('[Bigman API] Failed to load Express app:', err);
  handler = async () => ({
    statusCode: 500,
    headers:    { 'Content-Type': 'application/json' },
    body:       JSON.stringify({
      error:   'Backend initialization failed',
      details: process.env.NODE_ENV === 'development' ? err.message : 'Check Netlify function logs',
    }),
  });
}

// ── Netlify Function export
exports.handler = async (event, context) => {
  // Keep MongoDB connection alive across warm invocations
  context.callbackWaitsForEmptyEventLoop = false;

  return handler(event, context);
};
