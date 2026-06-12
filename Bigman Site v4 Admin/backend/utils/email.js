'use strict';

// ═══════════════════════════════════════════════════════════════
// EMAIL UTILITY — Nodemailer
// ═══════════════════════════════════════════════════════════════

const nodemailer = require('nodemailer');

// ── Create transporter ────────────────────────────────────────
const createTransporter = () => nodemailer.createTransport({
  host:   process.env.SMTP_HOST   || 'smtp.gmail.com',
  port:   parseInt(process.env.SMTP_PORT) || 587,
  secure: process.env.SMTP_SECURE === 'true',
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS,
  },
});

// ── Base email wrapper ────────────────────────────────────────
async function sendEmail({ to, subject, html, text }) {
  if (!process.env.SMTP_USER || !process.env.SMTP_PASS) {
    console.warn('⚠️  Email not sent — SMTP credentials not configured');
    return;
  }

  const transporter = createTransporter();
  await transporter.sendMail({
    from: process.env.EMAIL_FROM || 'Bigman Academic Services <noreply@bigmanacademicservices.com>',
    to,
    subject,
    html,
    text: text || html.replace(/<[^>]*>/g, '')
  });
}

// ── Email templates ────────────────────────────────────────────

const brandHeader = `
  <div style="background:#194ba5;padding:24px 32px;border-radius:8px 8px 0 0;text-align:center;">
    <h1 style="font-family:'Oswald',Arial,sans-serif;color:#fff;font-size:22px;margin:0;letter-spacing:2px;text-transform:uppercase;">
      🎓 BIGMAN ACADEMIC SERVICES
    </h1>
  </div>
`;

const brandFooter = `
  <div style="background:#f8f9fc;padding:20px 32px;border-top:1px solid #e2e8f0;text-align:center;border-radius:0 0 8px 8px;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">
      © ${new Date().getFullYear()} Bigman Academic Services · 
      <a href="${process.env.FRONTEND_URL}" style="color:#194ba5;">Visit Website</a> · 
      <a href="${process.env.FRONTEND_URL}/index.html#contact" style="color:#194ba5;">Contact Us</a>
    </p>
    <p style="font-size:11px;color:#cbd5e1;margin:8px 0 0;">
      Instagram: <a href="https://www.instagram.com/bigman_academic_services" style="color:#194ba5;">@bigman_academic_services</a>
    </p>
  </div>
`;

// ── Welcome email ──────────────────────────────────────────────
exports.sendWelcomeEmail = async (user) => {
  const html = `
  <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);">
    ${brandHeader}
    <div style="padding:32px;">
      <h2 style="color:#194ba5;font-size:20px;margin-top:0;">Welcome, ${user.firstName}! 🎉</h2>
      <p style="color:#475569;line-height:1.7;">
        Your Bigman Academic Services account is ready. We're here to help you pass exams, 
        complete online courses, write essays, and take over entire online classes.
      </p>
      <div style="background:#e8eef9;border-radius:8px;padding:20px;margin:20px 0;">
        <h3 style="color:#194ba5;margin-top:0;font-size:15px;">What we handle for you:</h3>
        <ul style="color:#475569;line-height:2;margin:0;padding-left:20px;">
          <li>GED, TEAS, HESI, GRE, NCLEX, ACCUPLACER exams</li>
          <li>StraighterLine, Sophia, Study.com, Excelsior courses</li>
          <li>Full online class takeovers</li>
          <li>Essays, research papers, dissertations</li>
        </ul>
      </div>
      <div style="text-align:center;margin:28px 0;">
        <a href="${process.env.FRONTEND_URL}/dashboard.html" 
           style="background:#194ba5;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;">
          Go to My Dashboard →
        </a>
      </div>
      <p style="color:#94a3b8;font-size:13px;">
        Need help right now? Message us on Instagram: 
        <a href="https://www.instagram.com/bigman_academic_services" style="color:#194ba5;">@bigman_academic_services</a>
      </p>
    </div>
    ${brandFooter}
  </div>`;

  await sendEmail({ to: user.email, subject: '🎓 Welcome to Bigman Academic Services!', html });
};

// ── Order confirmation to client ───────────────────────────────
exports.sendOrderConfirmation = async (order) => {
  const html = `
  <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);">
    ${brandHeader}
    <div style="padding:32px;">
      <h2 style="color:#194ba5;font-size:20px;margin-top:0;">Order Received! ✅</h2>
      <p style="color:#475569;">Hi <strong>${order.clientName}</strong>, we've received your order and will be in touch within <strong>15 minutes</strong>.</p>
      
      <div style="background:#f8f9fc;border:1px solid #e2e8f0;border-radius:8px;padding:20px;margin:20px 0;">
        <h3 style="color:#194ba5;margin-top:0;font-size:14px;text-transform:uppercase;letter-spacing:1px;">Order Summary</h3>
        <table style="width:100%;border-collapse:collapse;font-size:14px;color:#475569;">
          <tr><td style="padding:6px 0;font-weight:700;width:40%;">Order #</td><td>${order.orderNumber}</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Work Type</td><td>${order.workType.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Topic</td><td>${order.topic}</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Word Count</td><td>${order.wordCount.toLocaleString()} words</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Academic Level</td><td>${order.academicLevel.replace(/-/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Deadline</td><td>${order.deadlineOption || 'As discussed'}</td></tr>
          <tr><td style="padding:6px 0;font-weight:700;">Est. Price</td><td>${order.estimatedPrice || 'To be confirmed'}</td></tr>
        </table>
      </div>

      <p style="color:#475569;line-height:1.7;">
        Our team will contact you via <strong>${order.contactMethod}</strong> to confirm final pricing and begin your order. 
        You can also reach us directly on Instagram for the fastest response.
      </p>
      <div style="text-align:center;margin:24px 0;">
        <a href="https://www.instagram.com/bigman_academic_services" 
           style="background:linear-gradient(135deg,#833ab4,#fd1d1d,#fcb045);color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;">
          📸 Message Us on Instagram
        </a>
      </div>
    </div>
    ${brandFooter}
  </div>`;

  await sendEmail({ to: order.clientEmail, subject: `✅ Order Confirmed — ${order.orderNumber} | Bigman Academic Services`, html });
};

// ── New order notification to admin ───────────────────────────
exports.sendAdminOrderNotification = async (order) => {
  if (!process.env.ADMIN_NOTIFY_EMAIL) return;

  const html = `
  <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
    ${brandHeader}
    <div style="padding:28px;background:#fff;">
      <h2 style="color:#194ba5;margin-top:0;">🆕 New Order Received</h2>
      <table style="width:100%;border-collapse:collapse;font-size:14px;color:#475569;">
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Order #</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.orderNumber}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Client</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.clientName} (${order.clientEmail})</td></tr>
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Work Type</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.workType}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Topic</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.topic}</td></tr>
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Subject</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.subject}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Level</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.academicLevel}</td></tr>
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Word Count</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.wordCount}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Deadline</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.deadlineOption}</td></tr>
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Est. Price</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.estimatedPrice}</td></tr>
        <tr><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Contact Via</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.contactMethod}${order.contactHandle ? ' — ' + order.contactHandle : ''}</td></tr>
        <tr style="background:#f8f9fc;"><td style="padding:8px 12px;font-weight:700;border:1px solid #e2e8f0;">Notes</td><td style="padding:8px 12px;border:1px solid #e2e8f0;">${order.specialRequests || 'None'}</td></tr>
      </table>
      <div style="text-align:center;margin:24px 0;">
        <a href="${process.env.FRONTEND_URL}/admin/orders.html" 
           style="background:#194ba5;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;">
          View in Admin Panel →
        </a>
      </div>
    </div>
    ${brandFooter}
  </div>`;

  await sendEmail({ to: process.env.ADMIN_NOTIFY_EMAIL, subject: `🆕 New Order: ${order.orderNumber} — ${order.clientName}`, html });
};

// ── New contact notification ──────────────────────────────────
exports.sendAdminContactNotification = async (contact) => {
  if (!process.env.ADMIN_NOTIFY_EMAIL) return;

  const html = `
  <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;">
    ${brandHeader}
    <div style="padding:28px;background:#fff;">
      <h2 style="color:#194ba5;margin-top:0;">📬 New Contact Request</h2>
      <p><strong>From:</strong> ${contact.firstName} ${contact.lastName} &lt;${contact.email}&gt;</p>
      <p><strong>Service:</strong> ${contact.service}</p>
      <p><strong>Message:</strong></p>
      <blockquote style="border-left:4px solid #194ba5;margin:0;padding:12px 16px;background:#f8f9fc;color:#475569;font-style:italic;">
        ${contact.message}
      </blockquote>
      <div style="text-align:center;margin:24px 0;">
        <a href="${process.env.FRONTEND_URL}/admin/contacts.html" 
           style="background:#194ba5;color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;">
          View in Admin Panel →
        </a>
      </div>
    </div>
    ${brandFooter}
  </div>`;

  await sendEmail({ to: process.env.ADMIN_NOTIFY_EMAIL, subject: `📬 New Contact: ${contact.firstName} ${contact.lastName}`, html });
};

// ── Contact auto-reply ────────────────────────────────────────
exports.sendContactAutoReply = async (contact) => {
  const html = `
  <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08);">
    ${brandHeader}
    <div style="padding:32px;">
      <h2 style="color:#194ba5;font-size:20px;margin-top:0;">We got your message! 📬</h2>
      <p style="color:#475569;line-height:1.7;">
        Hi <strong>${contact.firstName}</strong>, thank you for reaching out. 
        We've received your message and one of our specialists will reply within <strong>2–4 hours</strong>.
      </p>
      <p style="color:#475569;">For urgent matters, message us directly on Instagram for an immediate response:</p>
      <div style="text-align:center;margin:24px 0;">
        <a href="https://www.instagram.com/bigman_academic_services" 
           style="background:linear-gradient(135deg,#833ab4,#fd1d1d,#fcb045);color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;display:inline-block;">
          📸 @bigman_academic_services
        </a>
      </div>
    </div>
    ${brandFooter}
  </div>`;

  await sendEmail({ to: contact.email, subject: `We received your message — Bigman Academic Services`, html });
};

// Export raw sender for custom use
exports.sendEmail = sendEmail;
