'use strict';

// ═══════════════════════════════════════════════════════════════
// API CLIENT — Shared by admin panel and frontend pages
// Auto-refreshes access tokens using refresh token
// ═══════════════════════════════════════════════════════════════

const API = (() => {

  // ── Configuration ─────────────────────────────────────────────
  // In production this is your backend URL, e.g. https://api.bigmanacademicservices.com
  // In development it's http://localhost:5000
  // When using Netlify Functions it's /.netlify/functions/api
  const BASE_URL = (() => {
    // Auto-detect environment
    if (typeof window === 'undefined') return '';
    const host = window.location.hostname;
    if (host === 'localhost' || host === '127.0.0.1') {
      return 'http://localhost:5000';
    }
    // Netlify preview or production — functions proxy
    return '';  // relative path — handled by netlify.toml redirect
  })();

  const API_BASE = `${BASE_URL}/api`;

  // ── Token storage ─────────────────────────────────────────────
  const getAccessToken  = () => localStorage.getItem('bigman_access_token');
  const getRefreshToken = () => localStorage.getItem('bigman_refresh_token');
  const getUser         = () => {
    try { return JSON.parse(localStorage.getItem('bigman_user') || 'null'); } catch { return null; }
  };

  const setTokens = (access, refresh) => {
    localStorage.setItem('bigman_access_token',  access);
    if (refresh) localStorage.setItem('bigman_refresh_token', refresh);
  };

  const setUser = (user) => {
    localStorage.setItem('bigman_user', JSON.stringify(user));
    // Keep legacy key for compatibility
    localStorage.setItem('bigman_session', JSON.stringify({ ...user, loggedIn: true }));
  };

  const clearAuth = () => {
    localStorage.removeItem('bigman_access_token');
    localStorage.removeItem('bigman_refresh_token');
    localStorage.removeItem('bigman_user');
    localStorage.removeItem('bigman_session');
  };

  const isLoggedIn = () => !!getAccessToken() && !!getUser();

  // ── Token refresh ──────────────────────────────────────────────
  let isRefreshing = false;
  let refreshQueue = [];

  async function refreshAccessToken() {
    const rt = getRefreshToken();
    if (!rt) throw new Error('No refresh token');

    const res = await fetch(`${API_BASE}/auth/refresh`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Refresh-Token': rt
      },
      body: JSON.stringify({ refreshToken: rt })
    });

    const data = await res.json();
    if (!res.ok || !data.accessToken) {
      clearAuth();
      throw new Error('Session expired. Please log in again.');
    }

    setTokens(data.accessToken, data.refreshToken);
    return data.accessToken;
  }

  // ── Core request function ─────────────────────────────────────
  async function request(method, path, body = null, requireAuth = false) {
    const url = `${API_BASE}${path}`;

    const headers = { 'Content-Type': 'application/json' };
    if (requireAuth || getAccessToken()) {
      const token = getAccessToken();
      if (token) headers['Authorization'] = `Bearer ${token}`;
    }

    const options = { method, headers };
    if (body && method !== 'GET') options.body = JSON.stringify(body);

    let res = await fetch(url, options);

    // If 401 and we have a refresh token, try to refresh once
    if (res.status === 401 && getRefreshToken()) {
      if (!isRefreshing) {
        isRefreshing = true;
        try {
          const newToken = await refreshAccessToken();
          headers['Authorization'] = `Bearer ${newToken}`;
          options.headers = headers;
          res = await fetch(url, options);
          refreshQueue.forEach(fn => fn(newToken));
        } catch (err) {
          refreshQueue.forEach(fn => fn(null));
          clearAuth();
          window.dispatchEvent(new CustomEvent('bigman:session-expired'));
          throw err;
        } finally {
          isRefreshing = false;
          refreshQueue = [];
        }
      } else {
        // Queue while already refreshing
        await new Promise((resolve, reject) => {
          refreshQueue.push((token) => {
            if (token) {
              options.headers['Authorization'] = `Bearer ${token}`;
              resolve();
            } else {
              reject(new Error('Session expired.'));
            }
          });
        });
        res = await fetch(url, options);
      }
    }

    if (res.status === 204) return { success: true };

    const data = await res.json();
    if (!res.ok) throw Object.assign(new Error(data.message || 'Request failed'), { status: res.status, data });
    return data;
  }

  // ── Auth API ──────────────────────────────────────────────────
  const auth = {
    async signup(payload) {
      const data = await request('POST', '/auth/signup', payload);
      setTokens(data.accessToken, data.refreshToken);
      setUser(data.user);
      return data;
    },

    async login(email, password) {
      const data = await request('POST', '/auth/login', { email, password });
      setTokens(data.accessToken, data.refreshToken);
      setUser(data.user);
      return data;
    },

    async logout() {
      try { await request('POST', '/auth/logout', null, true); } catch {}
      clearAuth();
    },

    async me() {
      return request('GET', '/auth/me', null, true);
    },

    async updateProfile(payload) {
      return request('PUT', '/auth/me', payload, true);
    },

    async changePassword(currentPassword, newPassword) {
      return request('POST', '/auth/change-password', { currentPassword, newPassword }, true);
    },

    isLoggedIn,
    getUser,
    isAdmin: () => getUser()?.role === 'admin'
  };

  // ── Orders API ────────────────────────────────────────────────
  const orders = {
    async create(payload) {
      return request('POST', '/orders', payload);
    },
    async mine(page = 1) {
      return request('GET', `/orders/mine?page=${page}`, null, true);
    },
    async get(id) {
      return request('GET', `/orders/${id}`, null, true);
    }
  };

  // ── Contact API ───────────────────────────────────────────────
  const contacts = {
    async submit(payload) {
      return request('POST', '/contacts', payload);
    }
  };

  // ── Newsletter API ────────────────────────────────────────────
  const newsletter = {
    async subscribe(email, name) {
      return request('POST', '/newsletter/subscribe', { email, name });
    }
  };

  // ── Admin API ─────────────────────────────────────────────────
  const admin = {
    async stats() {
      return request('GET', '/admin/stats', null, true);
    },

    // Orders
    async getOrders(params = {}) {
      const q = new URLSearchParams(params).toString();
      return request('GET', `/admin/orders?${q}`, null, true);
    },
    async getOrder(id) {
      return request('GET', `/admin/orders/${id}`, null, true);
    },
    async updateOrder(id, payload) {
      return request('PUT', `/admin/orders/${id}`, payload, true);
    },

    // Users
    async getUsers(params = {}) {
      const q = new URLSearchParams(params).toString();
      return request('GET', `/admin/users?${q}`, null, true);
    },
    async getUser(id) {
      return request('GET', `/admin/users/${id}`, null, true);
    },
    async updateUser(id, payload) {
      return request('PUT', `/admin/users/${id}`, payload, true);
    },
    async deleteUser(id) {
      return request('DELETE', `/admin/users/${id}`, null, true);
    },

    // Contacts
    async getContacts(params = {}) {
      const q = new URLSearchParams(params).toString();
      return request('GET', `/admin/contacts?${q}`, null, true);
    },
    async updateContact(id, payload) {
      return request('PUT', `/admin/contacts/${id}`, payload, true);
    },
    async deleteContact(id) {
      return request('DELETE', `/admin/contacts/${id}`, null, true);
    },

    // Newsletter
    async getSubscribers(params = {}) {
      const q = new URLSearchParams(params).toString();
      return request('GET', `/admin/newsletter?${q}`, null, true);
    },
    async broadcast(payload) {
      return request('POST', '/admin/newsletter/broadcast', payload, true);
    },
    async removeSubscriber(id) {
      return request('DELETE', `/admin/newsletter/${id}`, null, true);
    },

    // Health
    async health() {
      return request('GET', '/health');
    }
  };

  return { auth, orders, contacts, newsletter, admin, clearAuth, isLoggedIn, getUser, BASE_URL: API_BASE };
})();

// Make globally available
if (typeof window !== 'undefined') window.API = API;
