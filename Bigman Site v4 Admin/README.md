# Bigman Academic Services — Full-Stack Website

**Agency-level fullstack web application** — GED, TEAS, HESI, GRE, NCLEX, online course help, essays, and academic services.

---

## ✅ Completed Features

### Frontend
- Multi-page static site: Home, Courses, Expert Help, Materials, Blog, Order Essay, Login, Signup, Dashboard, Admin
- Brand: `#194ba5` Bigman Royal Blue + `#f59e0b` accent, Oswald heading font
- Responsive design, dark/light mode, scroll animations
- Instagram `@bigman_academic_services` CTAs throughout

### Backend API (Node.js + Express + MongoDB Atlas)
- JWT authentication (15m access tokens + 7d refresh tokens, rotation on refresh)
- bcryptjs password hashing, express-mongo-sanitize NoSQL protection
- express-rate-limit (global + per-route), Helmet security headers
- Full order lifecycle management (`pending → completed`)
- Auto-generated order numbers (`BAS-202506-XXXXX`)
- Nodemailer email: welcome, order confirmation, admin notifications, auto-reply
- Admin REST API with full CRUD for orders, users, contacts, newsletter

### Admin Panel (`/admin/index.html`)
- Vanilla JS SPA — no framework, no build step
- Dashboard: stats, revenue, recent orders
- Orders: full CRUD, status updates, client email notifications
- Students: list, view, soft-delete
- Contacts: view, status workflow (new → replied → archived)
- Newsletter: subscriber list, broadcast email sender

### Frontend API Integration (zero localStorage auth)
- `js/api.js` — auto-detects environment (localhost:5000 vs `/api` proxy)
- Token refresh with request queue (no duplicate refresh calls)
- `bigman:auth-expired` event for automatic redirect to login
- `signup.html` → `API.auth.signup()`
- `login.html` → `API.auth.login()`
- `order-essay.html` → `API.orders.submit()` with real order number on success
- `js/main.js` ContactForm → `API.contact.send()`
- `js/main.js` NewsletterForm → `API.newsletter.subscribe()`

### Deployment Infrastructure
- `netlify.toml` — CDN headers, `/api/*` redirect to Netlify Function
- `netlify/functions/api.js` — serverless Express adapter via `serverless-http`
- `backend/app.js` — shared Express factory (used by both server.js and Netlify Function)
- `backend/config/db.js` — idempotent MongoDB connection (safe for serverless warm starts)
- `backend/server.js` — VPS entry point with graceful shutdown
- `.gitignore` — excludes node_modules, .env, logs, SSL certs

---

## 📁 Project Structure

```
bigman-academic/
├── index.html                    ← Homepage
├── courses.html                  ← Exams & Courses
├── expert-help.html              ← Expert Help services
├── materials.html                ← Study materials
├── order-essay.html              ← Essay order form (→ API)
├── blog.html                     ← Blog articles
├── login.html                    ← Login (→ JWT API)
├── signup.html                   ← Signup (→ JWT API)
├── dashboard.html                ← Student dashboard
│
├── css/
│   ├── style.css                 ← Global styles (brand colors, fonts)
│   └── pages.css                 ← Page-specific styles
│
├── js/
│   ├── api.js                    ← Frontend API client (JWT, auto-refresh)
│   ├── components.js             ← Header/footer injection
│   └── main.js                   ← All frontend modules (ContactForm, Newsletter, etc.)
│
├── admin/
│   ├── index.html                ← Admin panel SPA
│   ├── css/admin.css             ← Admin styles
│   └── js/
│       ├── api.js                ← Admin API client
│       └── admin.js              ← Admin SPA controller
│
├── backend/
│   ├── app.js                    ← Express app factory (shared by server.js & Netlify)
│   ├── server.js                 ← VPS entry point (node server.js)
│   ├── package.json              ← Backend dependencies
│   ├── .env.example              ← Environment variable template
│   ├── config/
│   │   └── db.js                 ← MongoDB connection (idempotent)
│   ├── models/
│   │   ├── User.js               ← User schema (bcrypt, JWT)
│   │   ├── Order.js              ← Order lifecycle schema
│   │   ├── Contact.js            ← Contact request schema
│   │   └── Newsletter.js         ← Subscriber schema
│   ├── middleware/
│   │   ├── auth.js               ← protect(), adminOnly(), generateTokens()
│   │   └── validate.js           ← express-validator rule sets
│   ├── routes/
│   │   ├── auth.js               ← /api/auth/* endpoints
│   │   ├── orders.js             ← /api/orders/* endpoints
│   │   ├── contacts.js           ← /api/contacts endpoint
│   │   ├── newsletter.js         ← /api/newsletter/* endpoints
│   │   └── admin.js              ← /api/admin/* endpoints (all protected)
│   ├── utils/
│   │   └── email.js              ← Nodemailer email templates
│   └── scripts/
│       └── seed-admin.js         ← One-time admin account creator
│
├── netlify/
│   └── functions/
│       ├── api.js                ← Netlify serverless function adapter
│       └── package.json          ← Netlify function dependencies
│
├── netlify.toml                  ← Netlify config (redirects, headers, functions)
├── .gitignore                    ← Excludes node_modules, .env, logs
├── BACKEND_SETUP.md              ← Complete deployment guide
└── README.md                     ← This file
```

---

## 🚀 Quick Start

### 1. Set up MongoDB Atlas
See `BACKEND_SETUP.md` Section 3 — takes ~10 minutes.

### 2. Configure environment
```bash
cd backend
cp .env.example .env
# Edit .env — add MONGO_URI, JWT secrets, SMTP settings
```

### 3. Install & run backend
```bash
cd backend
npm install
npm run dev   # Starts on http://localhost:5000
```

### 4. Create admin account
```bash
cd backend
node scripts/seed-admin.js
```

### 5. Open frontend
Use VS Code Live Server or any static server:
```bash
npx serve . -p 3000
# Open http://localhost:3000
```

---

## 🔑 API Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/health` | None | Health check |
| POST | `/api/auth/signup` | None | Create account |
| POST | `/api/auth/login` | None | Login |
| POST | `/api/auth/refresh` | None | Refresh token |
| POST | `/api/auth/logout` | Bearer | Logout |
| GET | `/api/auth/me` | Bearer | Get profile |
| POST | `/api/orders` | Optional | Submit order |
| GET | `/api/orders/mine` | Bearer | My orders |
| POST | `/api/contacts` | None | Contact form |
| POST | `/api/newsletter/subscribe` | None | Subscribe |
| GET | `/api/newsletter/unsubscribe/:token` | None | Unsubscribe |
| GET | `/api/admin/stats` | Admin | Dashboard stats |
| GET | `/api/admin/orders` | Admin | All orders |
| PUT | `/api/admin/orders/:id` | Admin | Update order |
| GET | `/api/admin/users` | Admin | All users |
| GET | `/api/admin/contacts` | Admin | All contacts |
| POST | `/api/admin/newsletter/broadcast` | Admin | Send broadcast |

---

## 🗃️ Data Models

### User
`firstName`, `lastName`, `email` (unique), `password` (bcrypt, hidden), `role` (student/admin), `serviceNeeded`, `refreshTokenHash`, `lastLoginAt`, `isActive`

### Order
`orderNumber` (BAS-YYYYMM-XXXXX), `serviceType`, `workType`, `academicLevel`, `subject`, `topic`, `wordCount`, `deadline`, `clientName`, `clientEmail`, `status` (pending→completed), `price`, `assignedWriter`, `adminNotes`

### Contact
`firstName`, `lastName`, `email`, `service`, `message`, `status` (new→replied→archived)

### Newsletter
`email` (unique), `isActive`, `unsubscribeToken` (UUID), `subscribedAt`

---

## 📋 Deployment

| Environment | Method | Reference |
|-------------|--------|-----------|
| Preview | Netlify (serverless) | `BACKEND_SETUP.md` Section 7 |
| Production | VPS + PM2 + Nginx + Certbot SSL | `BACKEND_SETUP.md` Section 8 |

---

## 📖 Full Deployment Guide

See **`BACKEND_SETUP.md`** for:
- MongoDB Atlas setup with screenshots
- Environment variable configuration
- Local development workflow
- Netlify deployment (step-by-step)
- VPS production deployment (PM2 + Nginx + SSL)
- Email configuration (Gmail App Password or SendGrid)
- Security checklist
- Troubleshooting guide
