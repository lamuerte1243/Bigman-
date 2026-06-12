# Bigman Academic Services — Complete Backend Setup Guide

> **Agency-level fullstack backend** — Node.js + Express + MongoDB Atlas + JWT + Nodemailer  
> Two deployment targets: **Netlify** (preview/staging) → **VPS** (production)

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Prerequisites](#2-prerequisites)
3. [MongoDB Atlas Setup](#3-mongodb-atlas-setup)
4. [Environment Variables](#4-environment-variables)
5. [Local Development Setup](#5-local-development-setup)
6. [Create the Admin Account](#6-create-the-admin-account)
7. [Netlify Preview Deployment](#7-netlify-preview-deployment)
8. [VPS Production Deployment (PM2 + Nginx + SSL)](#8-vps-production-deployment)
9. [Email Setup (Nodemailer)](#9-email-setup-nodemailer)
10. [Accessing the Admin Panel](#10-accessing-the-admin-panel)
11. [API Reference](#11-api-reference)
12. [Security Checklist](#12-security-checklist)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                   FRONTEND (Static HTML)                 │
│  Served by: Netlify CDN (preview) / Nginx (production)  │
│  js/api.js — auto-detects environment, routes /api/*    │
└─────────────────────┬───────────────────────────────────┘
                       │  All API calls go to /api/*
           ┌───────────▼───────────┐
           │   PREVIEW (Netlify)   │    │  PRODUCTION (VPS)    │
           │  netlify.toml routes  │    │  Nginx reverse proxy  │
           │  /api/* → Function    │    │  /api/* → :5000       │
           │  netlify/functions/   │    │  pm2 start server.js  │
           │  api.js (serverless)  │    │                       │
           └───────────┬───────────┘    └──────────┬────────────┘
                       │                           │
                       └──────────┬────────────────┘
                                  │  Both use the same code
                       ┌──────────▼──────────────┐
                       │    backend/app.js        │
                       │  Express + all routes    │
                       │  helmet, cors, rate-limit│
                       │  mongoSanitize, JWT auth │
                       └──────────┬──────────────┘
                                  │
                       ┌──────────▼──────────────┐
                       │    MongoDB Atlas         │
                       │  Users, Orders,          │
                       │  Contacts, Newsletter    │
                       └─────────────────────────┘
```

---

## 2. Prerequisites

Install these on your local machine before starting:

| Tool | Version | Install |
|------|---------|---------|
| Node.js | 18 LTS or 20 LTS | https://nodejs.org |
| npm | Included with Node | — |
| Git | Any recent | https://git-scm.com |
| Netlify CLI | Latest | `npm i -g netlify-cli` |

Check your versions:
```bash
node --version   # should be v18.x or v20.x
npm --version    # should be 9.x or 10.x
git --version
netlify --version
```

---

## 3. MongoDB Atlas Setup

MongoDB Atlas is a free, fully managed cloud database — this is where all your data lives.

### Step 1 — Create Account
1. Go to https://cloud.mongodb.com
2. Sign up for a free account
3. Create a new **Organization** → Create a new **Project** (name it "Bigman Academic")

### Step 2 — Create a Cluster
1. Click **"Build a Database"**
2. Choose **"M0 FREE"** tier (512 MB free — more than enough to start)
3. Pick a cloud provider (AWS or Google Cloud) and the region **closest to your users**
4. Name it: `bigman-prod`
5. Click **"Create"**

### Step 3 — Create Database User
1. In the left menu → **"Database Access"**
2. Click **"Add New Database User"**
3. Authentication: Password
4. Username: `bigman_api`
5. Password: Click **"Autogenerate Secure Password"** → **copy and save it**
6. Built-in Role: **"Atlas admin"** (or "Read and write to any database")
7. Click **"Add User"**

### Step 4 — Whitelist IP Addresses
1. Left menu → **"Network Access"**
2. Click **"Add IP Address"**
3. For **Netlify** (serverless — dynamic IPs): Click **"Allow Access from Anywhere"** → `0.0.0.0/0`
   > ⚠️ This is required for Netlify since serverless functions have no fixed IP.  
   > On a VPS you can whitelist just your server IP instead for extra security.
4. Click **"Confirm"**

### Step 5 — Get Connection String
1. Left menu → **"Database"** → Click **"Connect"** next to your cluster
2. Choose **"Connect your application"**
3. Driver: **Node.js**, Version: **5.5 or later**
4. Copy the connection string — it looks like:
   ```
   mongodb+srv://bigman_api:<password>@bigman-prod.xxxxx.mongodb.net/?retryWrites=true&w=majority
   ```
5. Replace `<password>` with the password you saved in Step 3
6. Add your database name after the `/`:
   ```
   mongodb+srv://bigman_api:YOUR_PASSWORD@bigman-prod.xxxxx.mongodb.net/bigman_db?retryWrites=true&w=majority
   ```
7. **Save this full URI** — you'll need it as `MONGO_URI`

---

## 4. Environment Variables

### Create your .env file
```bash
cd backend
cp .env.example .env
```

Now open `backend/.env` and fill in ALL values:

```env
# ── Server ────────────────────────────────────────────────────
NODE_ENV=development
PORT=5000

# ── MongoDB ───────────────────────────────────────────────────
# Paste your full Atlas connection string here
MONGO_URI=mongodb+srv://bigman_api:YOUR_PASSWORD@bigman-prod.xxxxx.mongodb.net/bigman_db?retryWrites=true&w=majority

# ── JWT ───────────────────────────────────────────────────────
# Generate two DIFFERENT random secrets — minimum 64 characters each
# Run this in terminal: node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"
JWT_SECRET=paste_64+_char_random_hex_string_here
JWT_REFRESH_SECRET=paste_DIFFERENT_64+_char_random_hex_string_here
JWT_EXPIRES_IN=15m
JWT_REFRESH_EXPIRES_IN=7d

# ── Email (SMTP) ───────────────────────────────────────────────
# See Section 9 for full email setup instructions
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=false
SMTP_USER=yourgmail@gmail.com
SMTP_PASS=your_gmail_app_password_16_chars
EMAIL_FROM=Bigman Academic Services <yourgmail@gmail.com>
ADMIN_EMAIL=youradminemail@gmail.com

# ── Admin Account ─────────────────────────────────────────────
ADMIN_NAME=Your Name
ADMIN_PASSWORD=YourStrongAdminPassword123!

# ── Frontend URL ──────────────────────────────────────────────
# Local development:
FRONTEND_URL=http://localhost:5500
# Change to your Netlify URL after deploying:
# FRONTEND_URL=https://bigman-academic.netlify.app
# Change to production URL when live:
# FRONTEND_URL=https://bigmanacademicservices.com

# ── Rate Limiting ─────────────────────────────────────────────
RATE_LIMIT_WINDOW_MS=900000
RATE_LIMIT_MAX=200
AUTH_RATE_LIMIT_MAX=20
```

### Generate secure JWT secrets
Run this command twice to generate two unique secrets:
```bash
node -e "console.log(require('crypto').randomBytes(64).toString('hex'))"
```
Copy the output — use the first as `JWT_SECRET`, run again for `JWT_REFRESH_SECRET`.

---

## 5. Local Development Setup

```bash
# 1. Clone / open the project
cd /path/to/your/bigman-site

# 2. Install backend dependencies
cd backend
npm install

# 3. Install Netlify CLI globally (if not already)
npm install -g netlify-cli

# 4. Install Netlify function dependencies
cd ../netlify/functions
npm install

# 5. Go back to project root
cd ../..

# 6. Start the backend server (from the backend directory)
cd backend
npm run dev
# OR: node server.js
# Server starts at http://localhost:5000

# 7. Open the frontend
# Open the project root in VS Code → use Live Server extension
# OR: serve with any static file server:
npx serve . -p 3000
# Frontend at http://localhost:3000
```

### Verify the backend is running
Open your browser and go to: http://localhost:5000/api/health

You should see:
```json
{
  "status": "ok",
  "service": "Bigman Academic Services API",
  "db": "connected",
  "env": "development"
}
```

### Test with the frontend
The `js/api.js` file auto-detects localhost and points to `http://localhost:5000/api`.  
Try signing up at `signup.html` — you should get a real JWT token back.

---

## 6. Create the Admin Account

Run the seed script **once** after your backend is running and MongoDB is connected:

```bash
# Make sure you're in the backend directory
cd backend

# Make sure ADMIN_EMAIL, ADMIN_PASSWORD, ADMIN_NAME are set in .env
node scripts/seed-admin.js
```

Expected output:
```
✅ Admin account created:
   Email:    admin@yourdomain.com
   Name:     Your Name
   Role:     admin

Login at: http://localhost:5000/api/auth/login
```

> ⚠️ **Only run this once.** Running it again will say the account already exists.

### Access the Admin Panel
After creating the admin:
1. Open `admin/index.html` in your browser
2. Log in with your `ADMIN_EMAIL` and `ADMIN_PASSWORD`
3. You'll see the full dashboard with orders, users, contacts, newsletter

---

## 7. Netlify Preview Deployment

This gives you a live preview URL for testing before going to production.

### Step 1 — Push to Git
```bash
# From the project root
git init  # if not already a git repo
git add .
git commit -m "Initial commit — Bigman Academic Services"

# Push to GitHub (create a repo at github.com first)
git remote add origin https://github.com/YOUR_USERNAME/bigman-academic.git
git branch -M main
git push -u origin main
```

### Step 2 — Connect to Netlify
1. Go to https://app.netlify.com → **"Add new site"** → **"Import an existing project"**
2. Connect to GitHub → select your repository
3. Build settings:
   - **Base directory**: *(leave blank — root)*
   - **Build command**: *(leave blank)*
   - **Publish directory**: `.` (just a dot — the root)
4. Click **"Deploy site"**

### Step 3 — Set Environment Variables on Netlify
1. In Netlify dashboard → **Site Settings** → **Environment Variables**
2. Click **"Add a variable"** and add ALL variables from your `backend/.env`:

| Key | Value |
|-----|-------|
| `NODE_ENV` | `production` |
| `MONGO_URI` | your full Atlas connection string |
| `JWT_SECRET` | your 64-char hex secret |
| `JWT_REFRESH_SECRET` | your DIFFERENT 64-char hex secret |
| `JWT_EXPIRES_IN` | `15m` |
| `JWT_REFRESH_EXPIRES_IN` | `7d` |
| `SMTP_HOST` | `smtp.gmail.com` |
| `SMTP_PORT` | `587` |
| `SMTP_SECURE` | `false` |
| `SMTP_USER` | your Gmail |
| `SMTP_PASS` | your Gmail App Password |
| `EMAIL_FROM` | `Bigman Academic Services <your@gmail.com>` |
| `ADMIN_EMAIL` | your admin email |
| `FRONTEND_URL` | your Netlify URL (e.g. `https://bigman-academic.netlify.app`) |

3. After adding all variables → **"Trigger deploy"** → **"Clear cache and deploy site"**

### Step 4 — Verify the Netlify Deployment

```bash
# Test the health endpoint
curl https://YOUR-SITE.netlify.app/api/health
```

Should return `{"status":"ok","db":"connected",...}`

### Step 5 — Seed the Admin on Netlify
Since you can't run scripts directly on Netlify, either:

**Option A** — Run the seed script locally pointing at Atlas:
```bash
# Your local .env already points to Atlas MONGO_URI
cd backend
node scripts/seed-admin.js
```
This creates the admin in Atlas — Netlify Function will use the same database.

**Option B** — Make a one-time API call:
```bash
# POST to create admin via a temporary endpoint (add to server, call, remove)
# Or just use Option A — it's simpler.
```

### Netlify Site URL
Your Netlify preview URL will be: `https://[random-name].netlify.app`  
You can set a custom subdomain in Netlify → Site Settings → Domain Management.

---

## 8. VPS Production Deployment

For production use a dedicated VPS (DigitalOcean, Linode, Hetzner, Vultr, etc.).

### Recommended: DigitalOcean Droplet
- **Plan**: Basic, $6/month (1 vCPU, 1 GB RAM, 25 GB SSD)
- **OS**: Ubuntu 22.04 LTS
- **Region**: Closest to your users

---

### Step 1 — Initial Server Setup

```bash
# SSH into your server
ssh root@YOUR_SERVER_IP

# Update system packages
apt update && apt upgrade -y

# Install Node.js 20 LTS via NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Verify
node --version   # v20.x
npm --version    # 10.x

# Install PM2 globally (process manager for Node.js)
npm install -g pm2

# Install Nginx (reverse proxy)
apt install -y nginx

# Install Certbot for SSL
apt install -y certbot python3-certbot-nginx

# Allow firewall rules
ufw allow 22      # SSH
ufw allow 80      # HTTP
ufw allow 443     # HTTPS
ufw enable
```

---

### Step 2 — Deploy the Application

```bash
# Create a directory for the app
mkdir -p /var/www/bigman
cd /var/www/bigman

# Clone your repository
git clone https://github.com/YOUR_USERNAME/bigman-academic.git .

# Install backend dependencies
cd backend
npm install --production

# Create production .env
cp .env.example .env
nano .env
# Fill in ALL production values (MONGO_URI, JWT secrets, SMTP, etc.)
# Set NODE_ENV=production
# Set FRONTEND_URL=https://yourdomain.com
```

---

### Step 3 — PM2 Process Manager

```bash
# From the backend directory
cd /var/www/bigman/backend

# Start the server with PM2
pm2 start server.js --name "bigman-api" --env production

# Make PM2 restart on server reboot
pm2 startup systemd
# Copy and run the command PM2 outputs

pm2 save

# Useful PM2 commands:
pm2 status          # View running processes
pm2 logs bigman-api # View live logs
pm2 restart bigman-api
pm2 stop bigman-api
pm2 monit           # Real-time monitoring dashboard
```

---

### Step 4 — Create PM2 Ecosystem File (Recommended)

Create `/var/www/bigman/backend/ecosystem.config.js`:

```javascript
module.exports = {
  apps: [{
    name:         'bigman-api',
    script:       'server.js',
    cwd:          '/var/www/bigman/backend',
    instances:    'max',         // Use all CPU cores
    exec_mode:    'cluster',     // Cluster mode for load balancing
    env_production: {
      NODE_ENV: 'production',
      PORT:     5000,
    },
    error_file:   '/var/log/bigman/error.log',
    out_file:     '/var/log/bigman/out.log',
    merge_logs:   true,
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    max_memory_restart: '500M',  // Auto-restart if memory exceeds 500MB
  }]
};
```

```bash
# Create log directory
mkdir -p /var/log/bigman

# Start with ecosystem file
pm2 start ecosystem.config.js --env production
pm2 save
```

---

### Step 5 — Nginx Reverse Proxy

Create Nginx config for your domain:

```bash
nano /etc/nginx/sites-available/bigman
```

Paste this configuration (replace `yourdomain.com`):

```nginx
# ── HTTP → HTTPS redirect ─────────────────────────────────────
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

# ── HTTPS main server ─────────────────────────────────────────
server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    # SSL — Certbot will fill these in automatically (Step 6)
    # ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # ── Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # ── Serve static frontend files (HTML, CSS, JS, images)
    root /var/www/bigman;
    index index.html;

    # ── Proxy ALL /api/* requests to the Node.js backend
    location /api/ {
        proxy_pass         http://127.0.0.1:5000;
        proxy_http_version 1.1;
        proxy_set_header   Upgrade $http_upgrade;
        proxy_set_header   Connection 'upgrade';
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_read_timeout 60s;
        proxy_connect_timeout 60s;
        client_max_body_size 10m;
    }

    # ── Static file caching
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # ── HTML files — no cache (so updates go live immediately)
    location ~* \.html$ {
        add_header Cache-Control "no-cache, must-revalidate";
    }

    # ── SPA fallback (serve index.html for all unknown routes)
    location / {
        try_files $uri $uri/ /index.html;
    }

    # ── Block access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /backend/ {
        deny all;
    }
}
```

```bash
# Enable the site
ln -s /etc/nginx/sites-available/bigman /etc/nginx/sites-enabled/

# Remove default Nginx site
rm -f /etc/nginx/sites-enabled/default

# Test Nginx config syntax
nginx -t

# Restart Nginx
systemctl restart nginx
systemctl enable nginx
```

---

### Step 6 — SSL Certificate (Free via Let's Encrypt)

```bash
# Replace yourdomain.com with your actual domain
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Follow the prompts:
# - Enter your email
# - Agree to terms
# - Choose option 2 (Redirect HTTP → HTTPS automatically)

# Certbot will automatically update your Nginx config and reload it.

# Test automatic renewal
certbot renew --dry-run
```

Your SSL certificate auto-renews every 90 days. Certbot installs a cron job to handle this automatically.

---

### Step 7 — DNS Configuration

Point your domain to your VPS:

| Type | Name | Value |
|------|------|-------|
| A | `@` | `YOUR_VPS_IP` |
| A | `www` | `YOUR_VPS_IP` |
| CNAME | `api` | `yourdomain.com` (optional, if you want api.yourdomain.com) |

DNS propagation takes 5 minutes to 48 hours.

---

### Step 8 — Update Frontend API Client

Once your domain is live, update `backend/.env`:
```env
FRONTEND_URL=https://yourdomain.com
```

The `js/api.js` frontend client already auto-detects the environment:
- `localhost` → `http://localhost:5000/api`
- Any other host → `/api` (relative, picked up by Nginx proxy)

No code changes needed — it works automatically on your domain.

---

### Step 9 — Seed Admin in Production

```bash
cd /var/www/bigman/backend
node scripts/seed-admin.js
```

---

### Step 10 — Deploy Updates (Git Pull Workflow)

```bash
# SSH into server
ssh root@YOUR_SERVER_IP

# Pull latest changes
cd /var/www/bigman
git pull origin main

# If backend dependencies changed
cd backend && npm install --production && cd ..

# Restart backend (zero-downtime with PM2 cluster)
pm2 reload bigman-api

# If Nginx config changed
nginx -t && systemctl reload nginx
```

---

## 9. Email Setup (Nodemailer)

### Option A — Gmail (Easiest for starting out)

Gmail requires an "App Password" — not your normal Gmail password.

1. Enable 2-Factor Authentication on your Google account (required)
2. Go to: https://myaccount.google.com/apppasswords
3. Select app: **"Mail"**, device: **"Other"** → type "Bigman API"
4. Google shows you a 16-character password — **copy it immediately**
5. Set in your `.env`:
   ```env
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_SECURE=false
   SMTP_USER=yourgmail@gmail.com
   SMTP_PASS=xxxx xxxx xxxx xxxx   # the 16-char app password
   EMAIL_FROM=Bigman Academic Services <yourgmail@gmail.com>
   ```

> **Limitation**: Gmail free tier has a daily sending limit (~500 emails/day).  
> For higher volume, use SendGrid or Mailgun (see Option B).

### Option B — SendGrid (Professional, higher volume)

1. Sign up at https://sendgrid.com (free tier: 100 emails/day)
2. Create an API Key: Settings → API Keys → Create API Key (Full Access)
3. Verify your sender domain in SendGrid → Sender Authentication
4. Set in `.env`:
   ```env
   SMTP_HOST=smtp.sendgrid.net
   SMTP_PORT=587
   SMTP_SECURE=false
   SMTP_USER=apikey
   SMTP_PASS=SG.your_sendgrid_api_key_here
   EMAIL_FROM=Bigman Academic Services <noreply@yourdomain.com>
   ```

### Test Email Sending

```bash
# Quick test from Node.js
node -e "
require('dotenv').config();
const nodemailer = require('nodemailer');
const t = nodemailer.createTransport({
  host: process.env.SMTP_HOST,
  port: parseInt(process.env.SMTP_PORT),
  secure: process.env.SMTP_SECURE === 'true',
  auth: { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS }
});
t.verify((e, ok) => e ? console.error('❌ SMTP Error:', e.message) : console.log('✅ SMTP connection OK'));
"
```

---

## 10. Accessing the Admin Panel

The admin panel is a standalone SPA at `/admin/index.html`.

### Local development
1. Start backend: `cd backend && npm run dev`
2. Open in browser: `http://localhost:5500/admin/index.html` (Live Server)
3. Login with `ADMIN_EMAIL` and `ADMIN_PASSWORD` from your `.env`

### On Netlify / Production
- Go to: `https://yoursite.netlify.app/admin/index.html`
- Login with admin credentials

### Admin Panel Features
| Section | What you can do |
|---------|----------------|
| **Dashboard** | Live stats: total orders, revenue, students, contacts, recent activity |
| **Orders** | View all orders, update status, set price, assign writer, notify client |
| **Students** | View all registered users, edit details, soft-delete accounts |
| **Contacts** | View contact form submissions, mark read/replied/archived |
| **Newsletter** | View subscribers, remove them, broadcast emails to all subscribers |

---

## 11. API Reference

Base URL: `http://localhost:5000/api` (local) or `https://yourdomain.com/api` (production)

### Auth Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/signup` | Public | Create account |
| POST | `/auth/login` | Public | Login, get tokens |
| POST | `/auth/refresh` | Public | Refresh access token |
| POST | `/auth/logout` | Bearer | Invalidate refresh token |
| GET | `/auth/me` | Bearer | Get current user profile |
| PUT | `/auth/me` | Bearer | Update profile |
| POST | `/auth/change-password` | Bearer | Change password |

### Orders Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/orders` | Optional | Submit order (works as guest or logged in) |
| GET | `/orders/mine` | Bearer | Get my orders |
| GET | `/orders/:id` | Bearer | Get single order (owner or admin) |

### Contact & Newsletter

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/contacts` | Public | Submit contact form |
| POST | `/newsletter/subscribe` | Public | Subscribe to newsletter |
| GET | `/newsletter/unsubscribe/:token` | Public | Unsubscribe via email link |

### Admin Endpoints (all require admin JWT)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/stats` | Dashboard statistics |
| GET | `/admin/orders` | List all orders (filterable) |
| GET | `/admin/orders/:id` | Get order details |
| PUT | `/admin/orders/:id` | Update order (status, price, writer, notes) |
| GET | `/admin/users` | List all students |
| PUT | `/admin/users/:id` | Update student |
| DELETE | `/admin/users/:id` | Soft-delete student |
| GET | `/admin/contacts` | List all contact submissions |
| PUT | `/admin/contacts/:id` | Update contact status |
| DELETE | `/admin/contacts/:id` | Delete contact |
| GET | `/admin/newsletter` | List subscribers |
| DELETE | `/admin/newsletter/:id` | Remove subscriber |
| POST | `/admin/newsletter/broadcast` | Send broadcast email |

### Health Check

```bash
GET /api/health
# Returns: { status: "ok", db: "connected", ... }
```

---

## 12. Security Checklist

Before going to production, verify all of these:

- [ ] `.env` file is in `.gitignore` and NOT committed to git
- [ ] `JWT_SECRET` and `JWT_REFRESH_SECRET` are at least 64 chars, completely different
- [ ] MongoDB Atlas IP whitelist is set appropriately (0.0.0.0/0 for Netlify, VPS IP for production)
- [ ] Admin password is strong (12+ chars, mixed case, numbers, symbols)
- [ ] `NODE_ENV=production` is set on the server
- [ ] HTTPS/SSL certificate is installed and working
- [ ] Nginx blocks access to `/backend/` directory
- [ ] Rate limiting is configured (see `.env` RATE_LIMIT_* variables)
- [ ] CORS `FRONTEND_URL` is set to your actual domain (not `*`)
- [ ] PM2 is configured to restart on server reboot (`pm2 startup && pm2 save`)
- [ ] Log rotation is set up for PM2 logs (`pm2 install pm2-logrotate`)
- [ ] Database backups are enabled in MongoDB Atlas (enable M10+ for continuous backups, or use Atlas free daily snapshots on M0)

---

## 13. Troubleshooting

### "Cannot connect to MongoDB"
- Check your `MONGO_URI` in `.env` — make sure you replaced `<password>` with the actual password
- Check MongoDB Atlas → Network Access → your IP is whitelisted
- Try connecting with MongoDB Compass to verify the URI works

### "CORS error in browser"
- Make sure `FRONTEND_URL` in `.env` matches exactly what's in your browser address bar
- On Netlify, after deployment update `FRONTEND_URL` to your Netlify URL
- Remember: `http://localhost:5500` and `http://localhost:3000` are different origins

### "Netlify Function returning 500"
1. Check Netlify → Functions → Logs (real-time logs in the UI)
2. Most common cause: missing or wrong environment variable
3. Verify all env vars are set in Netlify UI → Site Settings → Environment Variables
4. Try: `curl https://yoursite.netlify.app/api/health` to see the error

### "JWT token expired immediately"
- Make sure your server clock is correct: `date` on the server
- `JWT_EXPIRES_IN=15m` means 15 minutes — this is the access token
- Use the refresh token endpoint to get a new access token (`POST /api/auth/refresh`)

### "Emails not sending"
- Run the SMTP test command from Section 9
- Gmail: make sure you're using an **App Password**, not your regular Gmail password
- Gmail: make sure 2FA is enabled on your Google account
- SendGrid: verify your sender identity before sending

### "PM2 process keeps restarting"
```bash
pm2 logs bigman-api --lines 100  # View recent logs
pm2 describe bigman-api          # View process details
```
Most common cause: MongoDB connection error or missing `.env` file.

### "Nginx 502 Bad Gateway"
- Check if the backend is running: `pm2 status`
- Check if it's listening on port 5000: `netstat -tlnp | grep 5000`
- Check backend logs: `pm2 logs bigman-api`
- Make sure Nginx proxy_pass points to `http://127.0.0.1:5000`

### Checking all running services
```bash
pm2 status                    # Node.js processes
systemctl status nginx        # Nginx web server
systemctl status mongod       # Local MongoDB (if installed locally)
curl http://localhost:5000/api/health  # Direct backend health check
```

---

## Quick Reference Commands

```bash
# ── Local Development ──────────────────────────────────────────
cd backend && npm run dev                    # Start backend
cd backend && node scripts/seed-admin.js     # Create admin

# ── Production Server ──────────────────────────────────────────
pm2 start ecosystem.config.js --env production
pm2 reload bigman-api                        # Zero-downtime restart
pm2 logs bigman-api                          # Live logs
pm2 monit                                    # Monitoring dashboard

# ── Nginx ─────────────────────────────────────────────────────
nginx -t                                     # Test config
systemctl reload nginx                       # Reload config
systemctl restart nginx                      # Full restart

# ── SSL ───────────────────────────────────────────────────────
certbot renew --dry-run                      # Test renewal
certbot renew                                # Force renew

# ── Git Deploy (on server) ─────────────────────────────────────
cd /var/www/bigman && git pull origin main
cd backend && npm install --production
pm2 reload bigman-api

# ── MongoDB (via Atlas UI) ─────────────────────────────────────
# Monitor: https://cloud.mongodb.com → your cluster → "Browse Collections"
```

---

*Built for Bigman Academic Services — Agency-grade backend, zero compromises.*
