'use strict';

// ═══════════════════════════════════════════════════════════════
// BIGMAN ADMIN PANEL — FULL SPA CONTROLLER
// ═══════════════════════════════════════════════════════════════

const Admin = (() => {

  let currentPage = 'dashboard';

  // ── Toast notifications ───────────────────────────────────────
  function toast(msg, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    const icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle' };
    el.innerHTML = `<i class="fas fa-${icons[type] || 'check-circle'}"></i> ${msg}`;
    container.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(() => el.remove(), 300); }, 3500);
  }

  // ── Helper: format date ──────────────────────────────────────
  function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
  }
  function fmtDateTime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'2-digit', minute:'2-digit' });
  }

  // ── Helper: status badge ─────────────────────────────────────
  function badge(status) {
    const map = {
      pending:'badge-pending', 'in-progress':'badge-in-progress', completed:'badge-completed',
      delivered:'badge-delivered', cancelled:'badge-cancelled', paid:'badge-paid',
      quoted:'badge-quoted', confirmed:'badge-in-progress', refunded:'badge-cancelled',
      revision:'badge-warning', review:'badge-in-progress',
      new:'badge-new', read:'badge-read', replied:'badge-replied', archived:'badge-archived'
    };
    return `<span class="status-badge ${map[status]||'badge-archived'}">${status}</span>`;
  }

  // ── Show login / main layout ──────────────────────────────────
  function showLogin() {
    document.getElementById('loginOverlay').style.display = 'flex';
    document.getElementById('adminLayout').style.display = 'none';
  }

  function showLayout(user) {
    document.getElementById('loginOverlay').style.display = 'none';
    document.getElementById('adminLayout').style.display = '';
    document.getElementById('adminGreeting').textContent = `Hi, ${user.firstName}`;
  }

  // ── Navigation ────────────────────────────────────────────────
  function navigate(page) {
    currentPage = page;
    document.querySelectorAll('.nav-item').forEach(el => {
      el.classList.toggle('active', el.dataset.page === page);
    });
    const titles = { dashboard:'Dashboard', orders:'Orders', users:'Students', contacts:'Contacts', newsletter:'Newsletter' };
    document.getElementById('topbarTitle').textContent = titles[page] || page;
    renderPage(page);
  }

  async function renderPage(page) {
    const content = document.getElementById('adminContent');
    content.innerHTML = '<div class="page-loader"><i class="fas fa-spinner fa-spin"></i></div>';
    try {
      switch (page) {
        case 'dashboard':  await renderDashboard(content);  break;
        case 'orders':     await renderOrders(content);     break;
        case 'users':      await renderUsers(content);      break;
        case 'contacts':   await renderContacts(content);   break;
        case 'newsletter': await renderNewsletter(content); break;
        default: content.innerHTML = '<p style="padding:40px;color:#64748b;">Page not found.</p>';
      }
    } catch (err) {
      content.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Failed to load: ${err.message}</p><button class="action-btn btn-primary" style="margin-top:16px" onclick="Admin._navigate('${page}')">Retry</button></div>`;
    }
  }

  // ═══════════════════════════════════════════════════════════════
  // DASHBOARD PAGE
  // ═══════════════════════════════════════════════════════════════
  async function renderDashboard(container) {
    const { stats } = await API.admin.stats();

    container.innerHTML = `
      <div class="stats-grid">
        <div class="stat-card blue">
          <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
          <div class="stat-body">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">${stats.orders.total.toLocaleString()}</div>
            <div class="stat-sub">${stats.orders.thisMonth} this month</div>
          </div>
        </div>
        <div class="stat-card yellow">
          <div class="stat-icon"><i class="fas fa-clock"></i></div>
          <div class="stat-body">
            <div class="stat-label">Pending</div>
            <div class="stat-value">${stats.orders.pending}</div>
            <div class="stat-sub">Needs attention</div>
          </div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon"><i class="fas fa-users"></i></div>
          <div class="stat-body">
            <div class="stat-label">Students</div>
            <div class="stat-value">${stats.users.total.toLocaleString()}</div>
            <div class="stat-sub">${stats.users.newThisWeek} new this week</div>
          </div>
        </div>
        <div class="stat-card red">
          <div class="stat-icon"><i class="fas fa-envelope"></i></div>
          <div class="stat-body">
            <div class="stat-label">New Contacts</div>
            <div class="stat-value">${stats.contacts.new}</div>
            <div class="stat-sub">${stats.contacts.total} total</div>
          </div>
        </div>
        <div class="stat-card purple">
          <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
          <div class="stat-body">
            <div class="stat-label">Subscribers</div>
            <div class="stat-value">${stats.subscribers.active.toLocaleString()}</div>
            <div class="stat-sub">Active newsletter</div>
          </div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
          <div class="stat-body">
            <div class="stat-label">Revenue</div>
            <div class="stat-value">$${(stats.revenue.total || 0).toLocaleString()}</div>
            <div class="stat-sub">Paid + completed orders</div>
          </div>
        </div>
        <div class="stat-card blue">
          <div class="stat-icon"><i class="fas fa-spinner"></i></div>
          <div class="stat-body">
            <div class="stat-label">In Progress</div>
            <div class="stat-value">${stats.orders.inProgress}</div>
            <div class="stat-sub">Active work</div>
          </div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
          <div class="stat-body">
            <div class="stat-label">Completed</div>
            <div class="stat-value">${stats.orders.completed}</div>
            <div class="stat-sub">All time</div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-file-alt"></i> Recent Orders</div>
          <button class="action-btn btn-primary" onclick="Admin._navigate('orders')">View All</button>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead>
              <tr>
                <th>Order #</th><th>Client</th><th>Work Type</th><th>Status</th>
                <th>Est. Price</th><th>Date</th>
              </tr>
            </thead>
            <tbody>
              ${stats.recentOrders.length ? stats.recentOrders.map(o => `
                <tr>
                  <td><strong>${o.orderNumber}</strong></td>
                  <td>${o.clientName}</td>
                  <td>${(o.workType||'').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</td>
                  <td>${badge(o.status)}</td>
                  <td>${o.estimatedPrice || '—'}</td>
                  <td>${fmtDate(o.createdAt)}</td>
                </tr>`).join('') : '<tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px;">No orders yet.</td></tr>'}
            </tbody>
          </table>
        </div>
      </div>`;

    // Update sidebar badges
    document.getElementById('pendingBadge').textContent = stats.orders.pending || '';
    document.getElementById('contactsBadge').textContent = stats.contacts.new || '';
  }

  // ═══════════════════════════════════════════════════════════════
  // ORDERS PAGE
  // ═══════════════════════════════════════════════════════════════
  async function renderOrders(container, page = 1, status = 'all', search = '') {
    const data = await API.admin.getOrders({ page, limit: 20, status, search });
    const { orders, total, pages } = data;

    container.innerHTML = `
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-file-alt"></i> All Orders (${total})</div>
        </div>
        <div class="filters-row">
          <select class="filter-select" id="orderStatusFilter" onchange="Admin._reloadOrders()">
            <option value="all" ${status==='all'?'selected':''}>All Statuses</option>
            <option value="pending"     ${status==='pending'?'selected':''}>Pending</option>
            <option value="quoted"      ${status==='quoted'?'selected':''}>Quoted</option>
            <option value="in-progress" ${status==='in-progress'?'selected':''}>In Progress</option>
            <option value="delivered"   ${status==='delivered'?'selected':''}>Delivered</option>
            <option value="completed"   ${status==='completed'?'selected':''}>Completed</option>
            <option value="cancelled"   ${status==='cancelled'?'selected':''}>Cancelled</option>
          </select>
          <input class="filter-input" id="orderSearchInput" placeholder="Search order#, name, email, topic…" value="${search}"
            onkeyup="if(event.key==='Enter')Admin._reloadOrders()" />
          <button class="action-btn" onclick="Admin._reloadOrders()"><i class="fas fa-search"></i></button>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead>
              <tr>
                <th>Order #</th><th>Client</th><th>Work</th><th>Words</th>
                <th>Deadline</th><th>Est. Price</th><th>Status</th><th>Date</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              ${orders.length ? orders.map(o => `
                <tr>
                  <td><strong style="color:var(--primary)">${o.orderNumber}</strong></td>
                  <td>
                    <div style="font-weight:600;">${o.clientName}</div>
                    <div style="font-size:.75rem;color:#94a3b8;">${o.clientEmail}</div>
                  </td>
                  <td>${(o.workType||'').replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</td>
                  <td>${(o.wordCount||0).toLocaleString()}</td>
                  <td>${o.deadlineOption || '—'}</td>
                  <td>${o.estimatedPrice || '—'}</td>
                  <td>${badge(o.status)}</td>
                  <td>${fmtDate(o.createdAt)}</td>
                  <td style="white-space:nowrap;">
                    <button class="action-btn" onclick="Admin._viewOrder('${o._id}')"><i class="fas fa-eye"></i></button>
                    <button class="action-btn btn-primary" onclick="Admin._editOrder('${o._id}')"><i class="fas fa-edit"></i></button>
                  </td>
                </tr>`).join('') : '<tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:40px;">No orders found.</td></tr>'}
            </tbody>
          </table>
        </div>
        ${pages > 1 ? `<div class="pagination-row">
          <span class="pagination-info">Page ${page} of ${pages} · ${total} orders</span>
          <div class="pagination-btns">
            <button class="page-btn" ${page<=1?'disabled':''} onclick="Admin._ordersPage(${page-1},'${status}','${search}')">← Prev</button>
            <button class="page-btn active">${page}</button>
            <button class="page-btn" ${page>=pages?'disabled':''} onclick="Admin._ordersPage(${page+1},'${status}','${search}')">Next →</button>
          </div>
        </div>` : ''}
      </div>`;
  }

  async function viewOrder(id) {
    const { order } = await API.admin.getOrder(id);
    showModal(`Order ${order.orderNumber}`, `
      <div class="detail-grid">
        <div class="detail-item"><label>Order #</label><span>${order.orderNumber}</span></div>
        <div class="detail-item"><label>Status</label><span>${badge(order.status)}</span></div>
        <div class="detail-item"><label>Client</label><span>${order.clientName}</span></div>
        <div class="detail-item"><label>Email</label><span>${order.clientEmail}</span></div>
        <div class="detail-item"><label>Work Type</label><span>${order.workType}</span></div>
        <div class="detail-item"><label>Academic Level</label><span>${order.academicLevel}</span></div>
        <div class="detail-item"><label>Subject</label><span>${order.subject}</span></div>
        <div class="detail-item"><label>Word Count</label><span>${(order.wordCount||0).toLocaleString()}</span></div>
        <div class="detail-item"><label>Deadline</label><span>${order.deadlineOption || '—'}</span></div>
        <div class="detail-item"><label>Est. Price</label><span>${order.estimatedPrice || '—'}</span></div>
        <div class="detail-item"><label>Agreed Price</label><span>${order.agreedPrice ? '$'+order.agreedPrice : '—'}</span></div>
        <div class="detail-item"><label>Writer</label><span>${order.assignedWriter || 'Unassigned'}</span></div>
        <div class="detail-item"><label>Submitted</label><span>${fmtDateTime(order.createdAt)}</span></div>
        <div class="detail-item"><label>Contact Via</label><span>${order.contactMethod}${order.contactHandle ? ' — '+order.contactHandle : ''}</span></div>
      </div>
      ${order.topic ? `<div style="margin-top:16px;"><label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;">Topic</label><p style="margin-top:4px;color:#0f172a;">${order.topic}</p></div>` : ''}
      ${order.specialRequests ? `<div style="margin-top:12px;"><label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;">Special Requests</label><p style="margin-top:4px;color:#475569;font-size:.88rem;line-height:1.6;">${order.specialRequests}</p></div>` : ''}
      ${order.adminNotes ? `<div style="margin-top:12px;"><label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;">Admin Notes</label><p style="margin-top:4px;color:#475569;font-size:.88rem;">${order.adminNotes}</p></div>` : ''}
    `, [{ label: 'Close', action: closeModal }, { label: 'Edit Order', cls: 'btn-primary', action: () => { closeModal(); editOrder(id); } }]);
  }

  async function editOrder(id) {
    const { order } = await API.admin.getOrder(id);
    showModal(`Edit Order ${order.orderNumber}`, `
      <div class="form-row">
        <label>Status</label>
        <select id="editStatus">
          ${['pending','quoted','confirmed','paid','in-progress','review','delivered','revision','completed','cancelled','refunded'].map(s =>
            `<option value="${s}" ${order.status===s?'selected':''}>${s}</option>`).join('')}
        </select>
      </div>
      <div class="form-row">
        <label>Agreed Price (USD)</label>
        <input type="number" id="editPrice" value="${order.agreedPrice||''}" placeholder="e.g. 45" />
      </div>
      <div class="form-row">
        <label>Assigned Writer</label>
        <input type="text" id="editWriter" value="${order.assignedWriter||''}" placeholder="Writer name or ID" />
      </div>
      <div class="form-row">
        <label>Admin Notes (internal)</label>
        <textarea id="editNotes">${order.adminNotes||''}</textarea>
      </div>
      <div class="form-row">
        <label><input type="checkbox" id="notifyClient" checked style="margin-right:6px;"/>Notify client of status change via email</label>
      </div>
    `, [
      { label: 'Cancel', action: closeModal },
      { label: 'Save Changes', cls: 'btn-primary', action: async () => {
        const status     = document.getElementById('editStatus').value;
        const agreedPrice= parseFloat(document.getElementById('editPrice').value) || undefined;
        const assignedWriter = document.getElementById('editWriter').value.trim() || undefined;
        const adminNotes = document.getElementById('editNotes').value.trim() || undefined;
        const notifyClient = document.getElementById('notifyClient').checked;
        try {
          await API.admin.updateOrder(id, { status, agreedPrice, assignedWriter, adminNotes, notifyClient });
          toast('Order updated successfully!');
          closeModal();
          renderOrders(document.getElementById('adminContent'));
        } catch(e) { toast(e.message, 'error'); }
      }}
    ]);
  }

  // ═══════════════════════════════════════════════════════════════
  // USERS PAGE
  // ═══════════════════════════════════════════════════════════════
  async function renderUsers(container, page = 1, search = '') {
    const data = await API.admin.getUsers({ page, limit: 20, search });
    const { users, total, pages } = data;

    container.innerHTML = `
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-users"></i> Students (${total})</div>
        </div>
        <div class="filters-row">
          <input class="filter-input" id="userSearchInput" placeholder="Search name or email…" value="${search}"
            onkeyup="if(event.key==='Enter')Admin._reloadUsers()" />
          <button class="action-btn" onclick="Admin._reloadUsers()"><i class="fas fa-search"></i></button>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead>
              <tr><th>Name</th><th>Email</th><th>Service</th><th>Status</th><th>Joined</th><th>Last Login</th><th>Actions</th></tr>
            </thead>
            <tbody>
              ${users.length ? users.map(u => `
                <tr>
                  <td><strong>${u.firstName} ${u.lastName}</strong></td>
                  <td>${u.email}</td>
                  <td>${u.serviceNeeded || '—'}</td>
                  <td>${u.isActive ? '<span class="status-badge badge-completed">Active</span>' : '<span class="status-badge badge-cancelled">Inactive</span>'}</td>
                  <td>${fmtDate(u.createdAt)}</td>
                  <td>${fmtDate(u.lastLoginAt)}</td>
                  <td>
                    <button class="action-btn" onclick="Admin._viewUser('${u._id}')"><i class="fas fa-eye"></i></button>
                    <button class="action-btn btn-danger" onclick="Admin._deactivateUser('${u._id}','${u.firstName} ${u.lastName}')"><i class="fas fa-ban"></i></button>
                  </td>
                </tr>`).join('') : '<tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:40px;">No students found.</td></tr>'}
            </tbody>
          </table>
        </div>
        ${pages > 1 ? `<div class="pagination-row">
          <span class="pagination-info">Page ${page} of ${pages} · ${total} students</span>
          <div class="pagination-btns">
            <button class="page-btn" ${page<=1?'disabled':''} onclick="Admin._usersPage(${page-1},'${search}')">← Prev</button>
            <button class="page-btn active">${page}</button>
            <button class="page-btn" ${page>=pages?'disabled':''} onclick="Admin._usersPage(${page+1},'${search}')">Next →</button>
          </div>
        </div>` : ''}
      </div>`;
  }

  async function viewUser(id) {
    const { user, orders } = await API.admin.getUser(id);
    showModal(`${user.firstName} ${user.lastName}`, `
      <div class="detail-grid">
        <div class="detail-item"><label>Full Name</label><span>${user.firstName} ${user.lastName}</span></div>
        <div class="detail-item"><label>Email</label><span>${user.email}</span></div>
        <div class="detail-item"><label>Service</label><span>${user.serviceNeeded||'—'}</span></div>
        <div class="detail-item"><label>Phone</label><span>${user.phone||'—'}</span></div>
        <div class="detail-item"><label>Status</label><span>${user.isActive?'Active':'Inactive'}</span></div>
        <div class="detail-item"><label>Joined</label><span>${fmtDateTime(user.createdAt)}</span></div>
        <div class="detail-item"><label>Last Login</label><span>${fmtDateTime(user.lastLoginAt)}</span></div>
        <div class="detail-item"><label>Orders</label><span>${orders.length}</span></div>
      </div>
      ${orders.length ? `
        <h4 style="margin:20px 0 12px;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Recent Orders</h4>
        <table class="data-table" style="font-size:.82rem;">
          <thead><tr><th>Order #</th><th>Work</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>${orders.slice(0,5).map(o=>`
            <tr>
              <td>${o.orderNumber}</td>
              <td>${o.workType}</td>
              <td>${badge(o.status)}</td>
              <td>${fmtDate(o.createdAt)}</td>
            </tr>`).join('')}
          </tbody>
        </table>` : ''}
    `, [{ label: 'Close', action: closeModal }]);
  }

  // ═══════════════════════════════════════════════════════════════
  // CONTACTS PAGE
  // ═══════════════════════════════════════════════════════════════
  async function renderContacts(container, page = 1, status = 'all') {
    const data = await API.admin.getContacts({ page, limit: 20, status });
    const { contacts, total, pages } = data;

    container.innerHTML = `
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-envelope"></i> Contact Requests (${total})</div>
        </div>
        <div class="filters-row">
          <select class="filter-select" id="contactStatusFilter" onchange="Admin._reloadContacts()">
            <option value="all" ${status==='all'?'selected':''}>All</option>
            <option value="new"      ${status==='new'?'selected':''}>New</option>
            <option value="read"     ${status==='read'?'selected':''}>Read</option>
            <option value="replied"  ${status==='replied'?'selected':''}>Replied</option>
            <option value="archived" ${status==='archived'?'selected':''}>Archived</option>
          </select>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead>
              <tr><th>Name</th><th>Email</th><th>Service</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
              ${contacts.length ? contacts.map(c => `
                <tr>
                  <td><strong>${c.firstName} ${c.lastName}</strong></td>
                  <td>${c.email}</td>
                  <td>${c.service||'—'}</td>
                  <td>${badge(c.status)}</td>
                  <td>${fmtDate(c.createdAt)}</td>
                  <td>
                    <button class="action-btn" onclick="Admin._viewContact('${c._id}')"><i class="fas fa-eye"></i></button>
                    <button class="action-btn btn-danger" onclick="Admin._deleteContact('${c._id}')"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>`).join('') : '<tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:40px;">No contacts found.</td></tr>'}
            </tbody>
          </table>
        </div>
      </div>`;
  }

  async function viewContact(id) {
    const contacts_data = await API.admin.getContacts({ limit: 200 });
    const c = contacts_data.contacts.find(x => x._id === id);
    if (!c) return;
    // Mark as read
    if (c.status === 'new') await API.admin.updateContact(id, { status: 'read' }).catch(()=>{});
    showModal(`${c.firstName} ${c.lastName}`, `
      <div class="detail-grid">
        <div class="detail-item"><label>Name</label><span>${c.firstName} ${c.lastName}</span></div>
        <div class="detail-item"><label>Email</label><span>${c.email}</span></div>
        <div class="detail-item"><label>Service</label><span>${c.service||'—'}</span></div>
        <div class="detail-item"><label>Date</label><span>${fmtDateTime(c.createdAt)}</span></div>
      </div>
      <div style="margin-top:16px;">
        <label style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#64748b;">Message</label>
        <div style="margin-top:6px;padding:14px;background:#f8f9fc;border-radius:8px;color:#475569;font-size:.9rem;line-height:1.6;">${c.message}</div>
      </div>
      <div class="form-row" style="margin-top:14px;">
        <label>Update Status</label>
        <select id="contactStatusUpdate">
          ${['new','read','replied','archived'].map(s=>`<option value="${s}" ${c.status===s?'selected':''}>${s}</option>`).join('')}
        </select>
      </div>
      <div class="form-row">
        <label>Admin Notes</label>
        <textarea id="contactNotesUpdate">${c.adminNotes||''}</textarea>
      </div>
    `, [
      { label: 'Close', action: closeModal },
      { label: 'Save', cls:'btn-primary', action: async () => {
        await API.admin.updateContact(id, {
          status: document.getElementById('contactStatusUpdate').value,
          adminNotes: document.getElementById('contactNotesUpdate').value
        });
        toast('Contact updated!');
        closeModal();
        renderContacts(document.getElementById('adminContent'));
      }}
    ]);
  }

  // ═══════════════════════════════════════════════════════════════
  // NEWSLETTER PAGE
  // ═══════════════════════════════════════════════════════════════
  async function renderNewsletter(container, page = 1) {
    const data = await API.admin.getSubscribers({ page, limit: 50 });
    const { subscribers, total, pages } = data;

    container.innerHTML = `
      <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-paper-plane"></i> Send Broadcast</div>
        </div>
        <div class="broadcast-area">
          <label>Subject Line</label>
          <input type="text" id="bcastSubject" placeholder="e.g. TEAS Exam Tips from Bigman Academic Services" />
          <label>HTML Body</label>
          <textarea id="bcastBody" placeholder="Write your HTML email content here…"></textarea>
          <div style="display:flex;gap:12px;margin-top:14px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;">
              <input type="checkbox" id="bcastTest" />
              <label style="margin:0;text-transform:none;font-size:.85rem;">Send test only to:</label>
              <input type="email" id="bcastTestEmail" placeholder="test@email.com" style="width:200px;" />
            </div>
            <button class="action-btn btn-primary" onclick="Admin._sendBroadcast()"><i class="fas fa-paper-plane"></i> Send Email</button>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fas fa-users"></i> Subscribers (${total})</div>
        </div>
        <div class="card-body">
          <table class="data-table">
            <thead><tr><th>Email</th><th>Name</th><th>Source</th><th>Subscribed</th><th>Actions</th></tr></thead>
            <tbody>
              ${subscribers.length ? subscribers.map(s => `
                <tr>
                  <td>${s.email}</td>
                  <td>${s.name||'—'}</td>
                  <td>${s.source||'—'}</td>
                  <td>${fmtDate(s.createdAt)}</td>
                  <td>
                    <button class="action-btn btn-danger" onclick="Admin._removeSubscriber('${s._id}','${s.email}')">
                      <i class="fas fa-times"></i> Remove
                    </button>
                  </td>
                </tr>`).join('') : '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:40px;">No subscribers yet.</td></tr>'}
            </tbody>
          </table>
        </div>
      </div>`;
  }

  // ═══════════════════════════════════════════════════════════════
  // MODAL
  // ═══════════════════════════════════════════════════════════════
  function showModal(title, body, buttons = []) {
    closeModal();
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.id = 'activeModal';
    overlay.innerHTML = `
      <div class="modal">
        <div class="modal-header">
          <span class="modal-title">${title}</span>
          <button class="modal-close" onclick="Admin._closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">${body}</div>
        <div class="modal-footer">
          ${buttons.map((b,i) => `<button class="action-btn ${b.cls||''}" id="modalBtn${i}">${b.label}</button>`).join('')}
        </div>
      </div>`;
    document.body.appendChild(overlay);
    buttons.forEach((b,i) => {
      document.getElementById(`modalBtn${i}`).addEventListener('click', b.action);
    });
    overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
  }

  function closeModal() {
    document.getElementById('activeModal')?.remove();
  }

  // ═══════════════════════════════════════════════════════════════
  // INIT
  // ═══════════════════════════════════════════════════════════════
  async function init() {
    // Check if already logged in
    const user = API.auth.getUser();
    const token = localStorage.getItem('bigman_access_token');

    if (user && token && user.role === 'admin') {
      showLayout(user);
      setupLayout(user);
      navigate('dashboard');
    } else {
      // Clear any stale tokens
      API.clearAuth();
      showLogin();
      setupLoginForm();
    }

    // Listen for session expiry
    window.addEventListener('bigman:session-expired', () => {
      toast('Session expired. Please log in again.', 'error');
      showLogin();
      setupLoginForm();
      document.getElementById('adminLayout').style.display = 'none';
      document.getElementById('loginOverlay').style.display = 'flex';
    });
  }

  function setupLoginForm() {
    const form = document.getElementById('loginForm');
    form.onsubmit = async (e) => {
      e.preventDefault();
      const btn  = document.getElementById('loginBtn');
      const msg  = document.getElementById('loginMsg');
      const email= document.getElementById('adminEmail').value.trim();
      const pw   = document.getElementById('adminPassword').value;

      btn.querySelector('.btn-lbl').style.display = 'none';
      btn.querySelector('.btn-spin').style.display = 'inline';
      btn.disabled = true;
      msg.style.display = 'none';

      try {
        const data = await API.auth.login(email, pw);
        if (data.user.role !== 'admin') {
          throw new Error('Access denied. Admin accounts only.');
        }
        showLayout(data.user);
        setupLayout(data.user);
        navigate('dashboard');
      } catch (err) {
        msg.className = 'login-msg error';
        msg.textContent = err.message || 'Login failed. Please check your credentials.';
        msg.style.display = 'block';
      } finally {
        btn.querySelector('.btn-lbl').style.display = 'inline';
        btn.querySelector('.btn-spin').style.display = 'none';
        btn.disabled = false;
      }
    };
  }

  function setupLayout(user) {
    // Sidebar nav
    document.querySelectorAll('.nav-item').forEach(el => {
      el.addEventListener('click', (e) => {
        e.preventDefault();
        navigate(el.dataset.page);
      });
    });

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', async () => {
      await API.auth.logout();
      showLogin();
      setupLoginForm();
      document.getElementById('adminLayout').style.display = 'none';
      document.getElementById('loginOverlay').style.display = 'flex';
    });

    // Mobile sidebar toggle
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    toggle?.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // Public API (exposed to onclick handlers)
  return {
    init,
    _navigate: navigate,
    _viewOrder: viewOrder,
    _editOrder: editOrder,
    _viewUser:  viewUser,
    _viewContact: viewContact,
    _closeModal: closeModal,
    _deactivateUser: (id, name) => {
      if (!confirm(`Deactivate account for ${name}?`)) return;
      API.admin.deleteUser(id)
        .then(() => { toast('User deactivated.'); renderUsers(document.getElementById('adminContent')); })
        .catch(e => toast(e.message, 'error'));
    },
    _deleteContact: (id) => {
      if (!confirm('Delete this contact?')) return;
      API.admin.deleteContact(id)
        .then(() => { toast('Contact deleted.'); renderContacts(document.getElementById('adminContent')); })
        .catch(e => toast(e.message, 'error'));
    },
    _removeSubscriber: (id, email) => {
      if (!confirm(`Remove ${email}?`)) return;
      API.admin.removeSubscriber(id)
        .then(() => { toast('Subscriber removed.'); renderNewsletter(document.getElementById('adminContent')); })
        .catch(e => toast(e.message, 'error'));
    },
    _sendBroadcast: async () => {
      const subject  = document.getElementById('bcastSubject')?.value.trim();
      const html     = document.getElementById('bcastBody')?.value.trim();
      const testOnly = document.getElementById('bcastTest')?.checked;
      const testEmail= document.getElementById('bcastTestEmail')?.value.trim();
      if (!subject || !html) return toast('Subject and body are required.', 'error');
      if (testOnly && !testEmail) return toast('Enter a test email address.', 'error');
      if (!testOnly && !confirm(`Send broadcast to ALL subscribers?`)) return;
      try {
        const res = await API.admin.broadcast({ subject, html, testOnly, testEmail });
        toast(res.message || 'Sent!');
      } catch(e) { toast(e.message, 'error'); }
    },
    _reloadOrders: () => {
      const status = document.getElementById('orderStatusFilter')?.value || 'all';
      const search = document.getElementById('orderSearchInput')?.value || '';
      renderOrders(document.getElementById('adminContent'), 1, status, search);
    },
    _ordersPage: (page, status, search) => {
      renderOrders(document.getElementById('adminContent'), page, status, search);
    },
    _reloadUsers: () => {
      const search = document.getElementById('userSearchInput')?.value || '';
      renderUsers(document.getElementById('adminContent'), 1, search);
    },
    _usersPage: (page, search) => {
      renderUsers(document.getElementById('adminContent'), page, search);
    },
    _reloadContacts: () => {
      const status = document.getElementById('contactStatusFilter')?.value || 'all';
      renderContacts(document.getElementById('adminContent'), 1, status);
    }
  };
})();

// Toggle password eye
function togglePwd(btn) {
  const inp = btn.closest('.lf-wrap').querySelector('input');
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
}

document.addEventListener('DOMContentLoaded', Admin.init);
