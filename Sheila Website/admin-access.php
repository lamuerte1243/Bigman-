<?php
/**
 * Sheila The Writer — Admin Access Instructions
 * This page provides clear instructions for accessing the admin panel.
 * Remove this file from production once setup is complete.
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Admin Access — ' . SITE_NAME;
$in_subdir  = false;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title) ?></title>
<link rel="icon" href="images/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,800;0,900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/clearstreet.css">
<style>
  body { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
  .access-page { max-width: 740px; width: 100%; }
  .access-brand { text-align: center; margin-bottom: 40px; }
  .access-brand .brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 900;
    letter-spacing: 0.12em; color: var(--white);
  }
  .access-brand .brand-sub {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 0.78rem; color: var(--white-40);
    letter-spacing: 0.14em; text-transform: uppercase;
    margin-top: 2px;
  }
  .access-card {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 40px;
  }
  .access-card h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem; font-weight: 700;
    color: var(--white); margin-bottom: 8px;
  }
  .access-card > .subtitle {
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem; color: var(--white-50);
    margin-bottom: 32px; line-height: 1.6;
  }
  .access-section { margin-bottom: 32px; }
  .access-section h2 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 0.68rem; font-weight: 700;
    letter-spacing: 0.16em; text-transform: uppercase;
    color: var(--accent); margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
  }
  .access-section h2::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
  }
  .step-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
  .step-item {
    display: flex; align-items: flex-start; gap: 14px;
    background: var(--surface-glass); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 16px;
  }
  .step-num {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--accent); color: #fff;
    font-family: 'Inter', sans-serif; font-size: 0.72rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
  }
  .step-content { flex: 1; }
  .step-content strong {
    display: block; font-family: 'Inter', sans-serif;
    font-size: 0.88rem; font-weight: 700;
    color: var(--white-90); margin-bottom: 4px;
  }
  .step-content span {
    font-family: 'Inter', sans-serif;
    font-size: 0.78rem; color: var(--white-50);
    line-height: 1.5; display: block;
  }
  .url-box {
    display: flex; align-items: center; gap: 12px;
    background: var(--surface-3); border: 1px solid var(--border-accent);
    border-radius: var(--radius); padding: 14px 18px;
    margin-top: 16px;
  }
  .url-box i { color: var(--accent); font-size: 0.85rem; flex-shrink: 0; }
  .url-box code {
    font-family: 'Space Grotesk', 'Inter', monospace;
    font-size: 0.82rem; color: var(--accent-bright);
    font-weight: 600; letter-spacing: 0.02em; flex: 1;
  }
  .url-box a.copy-btn {
    font-family: 'Inter', sans-serif;
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--white-40);
    padding: 5px 10px; border: 1px solid var(--border);
    border-radius: var(--radius-sm); background: transparent;
    cursor: pointer; text-decoration: none; transition: all 0.2s ease;
    white-space: nowrap;
  }
  .url-box a.copy-btn:hover { color: var(--white); border-color: var(--border-light); }
  .cred-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
  .cred-item {
    background: var(--surface-glass); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 14px;
  }
  .cred-item label {
    display: block; font-family: 'Inter', sans-serif;
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--white-30);
    margin-bottom: 6px;
  }
  .cred-item code {
    font-family: 'Space Grotesk', monospace;
    font-size: 0.84rem; color: var(--gold);
    font-weight: 600;
  }
  .warn-box {
    display: flex; align-items: flex-start; gap: 12px;
    background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.20);
    border-radius: var(--radius); padding: 14px 18px;
    margin-top: 24px;
  }
  .warn-box i { color: var(--rose); margin-top: 2px; flex-shrink: 0; }
  .warn-box p { font-family: 'Inter', sans-serif; font-size: 0.78rem; color: var(--rose); line-height: 1.5; margin: 0; }
  .access-cta {
    display: flex; gap: 12px; margin-top: 32px;
    padding-top: 24px; border-top: 1px solid var(--border);
    flex-wrap: wrap;
  }
  @media (max-width: 600px) {
    .access-card { padding: 24px; }
    .cred-grid { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<div class="access-page">

  <!-- Brand -->
  <div class="access-brand">
    <a href="index.php" style="text-decoration:none;display:inline-block;">
      <img src="sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-login">
    </a>
    <p style="font-size:0.65rem;letter-spacing:0.18em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,0.35);margin-top:8px;">Admin Access Guide</p>
  </div>

  <div class="access-card">
    <h1>Admin Panel Access</h1>
    <p class="subtitle">
      The admin panel is accessible via a separate, secured URL. It is intentionally not linked from the public navigation to prevent unauthorized access.
    </p>

    <!-- Admin URL -->
    <div class="access-section">
      <h2><i class="fa-solid fa-shield-halved"></i> Admin Login URL</h2>
      <p style="font-family:'Inter',sans-serif;font-size:0.82rem;color:var(--white-50);margin-bottom:12px;line-height:1.6;">
        Navigate directly to this URL in your browser to access the admin login page:
      </p>
      <div class="url-box">
        <i class="fa-solid fa-link"></i>
        <code>/admin-login.php</code>
        <a href="admin-login.php" class="copy-btn" target="_blank">Open &rarr;</a>
      </div>
      <p style="font-family:'Inter',sans-serif;font-size:0.74rem;color:var(--white-30);margin-top:10px;line-height:1.5;">
        On your live server this will be: <code style="color:var(--white-50);font-family:'Space Grotesk',monospace;">https://yourdomain.com/admin-login.php</code>
      </p>
    </div>

    <!-- Steps -->
    <div class="access-section">
      <h2><i class="fa-solid fa-list-check"></i> Step-by-Step Access</h2>
      <ul class="step-list">
        <li class="step-item">
          <span class="step-num">1</span>
          <div class="step-content">
            <strong>Go to the Admin Login Page</strong>
            <span>Visit <code style="font-family:'Space Grotesk',monospace;color:var(--accent);">yourdomain.com/admin-login.php</code> in your browser. This is separate from the student login at <code style="font-family:'Space Grotesk',monospace;color:var(--white-40);">student/login.php</code>.</span>
          </div>
        </li>
        <li class="step-item">
          <span class="step-num">2</span>
          <div class="step-content">
            <strong>Enter Admin Credentials</strong>
            <span>Use the admin email and password configured in <code style="font-family:'Space Grotesk',monospace;color:var(--white-40);">includes/config.php</code>. The defaults are shown below — change them immediately on a live server.</span>
          </div>
        </li>
        <li class="step-item">
          <span class="step-num">3</span>
          <div class="step-content">
            <strong>Database vs. Config Fallback</strong>
            <span>If the database is set up, admin credentials are verified against the <code style="font-family:'Space Grotesk',monospace;color:var(--white-40);">admin_users</code> table. If not yet set up, the system falls back to the config credentials below.</span>
          </div>
        </li>
        <li class="step-item">
          <span class="step-num">4</span>
          <div class="step-content">
            <strong>Access the Dashboard</strong>
            <span>After login you are redirected to <code style="font-family:'Space Grotesk',monospace;color:var(--white-40);">admin/dashboard.php</code> — the full admin control panel.</span>
          </div>
        </li>
      </ul>
    </div>

    <!-- Default Credentials -->
    <div class="access-section">
      <h2><i class="fa-solid fa-key"></i> Default Credentials</h2>
      <p style="font-family:'Inter',sans-serif;font-size:0.78rem;color:var(--white-50);margin-bottom:0;line-height:1.5;">
        Set in <code style="font-family:'Space Grotesk',monospace;color:var(--white-40);">includes/config.php</code>. Change these before going live.
      </p>
      <div class="cred-grid">
        <div class="cred-item">
          <label>Admin Email</label>
          <code><?= defined('ADMIN_EMAIL') ? h(ADMIN_EMAIL) : 'admin@sheilathewriter.com' ?></code>
        </div>
        <div class="cred-item">
          <label>Admin Password</label>
          <code><?= defined('ADMIN_PASSWORD') ? h(ADMIN_PASSWORD) : 'Admin@2024!' ?></code>
        </div>
      </div>
    </div>

    <!-- Student Login -->
    <div class="access-section">
      <h2><i class="fa-solid fa-graduation-cap"></i> Student Login & Sign Up</h2>
      <p style="font-family:'Inter',sans-serif;font-size:0.82rem;color:var(--white-50);margin-bottom:12px;line-height:1.6;">
        Student authentication links are now visible in the main navigation bar on all pages.
      </p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="url-box" style="flex-direction:column;align-items:flex-start;gap:6px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span style="font-family:'Inter',sans-serif;font-size:0.72rem;font-weight:700;color:var(--white-60);text-transform:uppercase;letter-spacing:0.08em;">Student Login</span>
          </div>
          <code>student/login.php</code>
        </div>
        <div class="url-box" style="flex-direction:column;align-items:flex-start;gap:6px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-user-plus"></i>
            <span style="font-family:'Inter',sans-serif;font-size:0.72rem;font-weight:700;color:var(--white-60);text-transform:uppercase;letter-spacing:0.08em;">Student Sign Up</span>
          </div>
          <code>student/register.php</code>
        </div>
      </div>
    </div>

    <!-- Warning -->
    <div class="warn-box">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <p>
        <strong>Security Notice:</strong> Delete or restrict this <code style="font-family:'Space Grotesk',monospace;">admin-access.php</code> file from your production server once setup is complete. Change all default credentials immediately. All admin access is logged.
      </p>
    </div>

    <!-- CTAs -->
    <div class="access-cta">
      <a href="admin-login.php" class="btn btn-primary">
        <i class="fa-solid fa-shield-halved"></i> Go to Admin Login
      </a>
      <a href="student/login.php" class="btn btn-outline">
        <i class="fa-solid fa-graduation-cap"></i> Student Login
      </a>
      <a href="index.php" class="btn btn-ghost">
        <i class="fa-solid fa-arrow-left"></i> Back to Website
      </a>
    </div>
  </div>

</div>

<script>
// Apply saved theme
(function(){
  var t = localStorage.getItem('sheila-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', t);
})();
// Fade in
document.body.style.opacity='0';
document.body.style.transition='opacity 0.4s ease';
requestAnimationFrame(function(){requestAnimationFrame(function(){document.body.style.opacity='1';});});
</script>
</body>
</html>
