'use strict';

// ═══════════════════════════════════════════════════════════════
// BIGMAN ACADEMIC SERVICES — FRONTEND API CLIENT
// Handles auth, orders, contacts, newsletter
// Automatically refreshes JWT access tokens
// ═══════════════════════════════════════════════════════════════

const API = (() => {

  // ── Resolve API base URL ──────────────────────────────────────
  // Netlify: requests go to /.netlify/functions/api (proxied by netlify.toml)
  // VPS production: https://api.bigmanacademicservices.com
  // Local dev: http://localhost:5000
  const API_BASE = (() => {
    const host = window.location.hostname;
    if (host === 'localhost' || host === '127.0.0.1') {
      // Local dev — backend runs on port 5000
      return 'http://localhost:5000/api';
    }
    // Netlify preview / production — proxy via netlify.toml redirect rule
    return '/api';
  })();

  // ── Storage helpers ───────────────────────────────────────────
  const getAccessToken  = () => localStorage.getItem('bigman_access_token');
  const getRefreshToken = () => localStorage.getItem('bigman_refresh_token');
  const getUser         = () => {
    try { return JSON.parse(localStorage.getItem('bigman_user') || 'null'); } catch { return null; }
  };
  const setTokens = (a, r) => {
    localStorage.setItem('bigman_access_token', a);
    if (r) localStorage.setItem('bigman_refresh_token', r);
  };
  const setUser = (u) => {
    localStorage.setItem('bigman_user', JSON.stringify(u));
    localStorage.setItem('bigman_session', JSON.stringify({ ...u, loggedIn: true }));
  };
  const clearAuth = () => {
    ['bigman_access_token','bigman_refresh_token','bigman_user','bigman_session'].forEach(k => localStorage.removeItem(k));
  };

  // ── Token refresh ─────────────────────────────────────────────
  let refreshing = false;
  let queue      = [];

  async function doRefresh() {
    const rt = getRefreshToken();
    if (!rt) throw new Error('No refresh token');
    const res = await fetch(`${API_BASE}/auth/refresh`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Refresh-Token': rt },
      body: JSON.stringify({ refreshToken: rt })
    });
    const d = await res.json();
    if (!res.ok || !d.accessToken) { clearAuth(); throw new Error('Session expired'); }
    setTokens(d.accessToken, d.refreshToken);
    return d.accessToken;
  }

  // ── Core fetch wrapper ────────────────────────────────────────
  async function req(method, path, body = null, auth = false) {
    const url = `${API_BASE}${path}`;
    const headers = { 'Content-Type': 'application/json' };
    const token = getAccessToken();
    if (auth && token) headers['Authorization'] = `Bearer ${token}`;

    const opts = { method, headers };
    if (body && method !== 'GET') opts.body = JSON.stringify(body);

    let res = await fetch(url, opts);

    // Auto-refresh on 401
    if (res.status === 401 && getRefreshToken()) {
      if (!refreshing) {
        refreshing = true;
        try {
          const newTok = await doRefresh();
          headers['Authorization'] = `Bearer ${newTok}`;
          opts.headers = headers;
          res = await fetch(url, opts);
          queue.forEach(fn => fn(newTok));
        } catch (e) {
          queue.forEach(fn => fn(null));
          clearAuth();
          window.dispatchEvent(new CustomEvent('bigman:auth-expired'));
          throw e;
        } finally {
          refreshing = false;
          queue = [];
        }
      } else {
        await new Promise((resolve, reject) =>
          queue.push(tok => tok ? resolve() : reject(new Error('Session expired')))
        );
        res = await fetch(url, opts);
      }
    }

    if (res.status === 204) return { success: true };
    const data = await res.json();
    if (!res.ok) throw Object.assign(new Error(data.message || 'Request failed'), { status: res.status, data });
    return data;
  }

  // ── Auth ──────────────────────────────────────────────────────
  const auth = {
    async signup({ firstName, lastName, email, password, serviceNeeded }) {
      const d = await req('POST', '/auth/signup', { firstName, lastName, email, password, serviceNeeded });
      setTokens(d.accessToken, d.refreshToken);
      setUser(d.user);
      return d;
    },

    async login(email, password) {
      const d = await req('POST', '/auth/login', { email, password });
      setTokens(d.accessToken, d.refreshToken);
      setUser(d.user);
      return d;
    },

    async logout() {
      try { await req('POST', '/auth/logout', null, true); } catch {}
      clearAuth();
    },

    async me() {
      return req('GET', '/auth/me', null, true);
    },

    getUser,
    isLoggedIn: () => !!getAccessToken() && !!getUser(),
    isAdmin:    () => getUser()?.role === 'admin',
    clearAuth
  };

  // ── Orders ────────────────────────────────────────────────────
  const orders = {
    async submit(payload) {
      return req('POST', '/orders', payload);
    },
    async mine(page = 1) {
      return req('GET', `/orders/mine?page=${page}`, null, true);
    }
  };

  // ── Contact form ──────────────────────────────────────────────
  const contact = {
    async send(payload) {
      return req('POST', '/contacts', payload);
    }
  };

  // ── Newsletter ────────────────────────────────────────────────
  const newsletter = {
    async subscribe(email, name = '') {
      return req('POST', '/newsletter/subscribe', { email, name });
    }
  };

  return { auth, orders, contact, newsletter, clearAuth, API_BASE };
})();

window.API = API;

// Redirect to login if session expires during page activity
window.addEventListener('bigman:auth-expired', () => {
  const user = JSON.parse(localStorage.getItem('bigman_user') || 'null');
  if (user) {
    window.location.href = 'login.html?expired=1';
  }
});
