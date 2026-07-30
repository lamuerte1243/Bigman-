<?php
/**
 * Sheila The Writer — Admin Dashboard
 * Full-featured admin with: contacts, subscribers, settings, blog, students
 */
$in_subdir = true;
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

require_admin_login();
$admin = current_admin();

// ── Panel routing ──────────────────────────────────────────────
$panel = $_GET['panel'] ?? 'overview';

// ── Safe DB wrapper ────────────────────────────────────────────
function safe_query(string $sql, array $params = []): array {
    try {
        $pdo  = get_db();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function safe_count(string $table, string $where = '1'): int {
    try {
        $pdo  = get_db();
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table` WHERE $where");
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

// ── Handle POST actions ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!csrf_check()) { flash('error', 'CSRF check failed.'); redirect('dashboard.php?panel=' . $panel); }

    $action = $_POST['action'];

    try {
        $pdo = get_db();

        if ($action === 'update_contact_status') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'read';
            $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?")->execute([$status, $id]);
            flash('success', 'Contact status updated.');

        } elseif ($action === 'delete_contact') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
            flash('success', 'Contact deleted.');

        } elseif ($action === 'update_subscriber') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            $pdo->prepare("UPDATE newsletter_subscribers SET status = ? WHERE id = ?")->execute([$status, $id]);
            flash('success', 'Subscriber updated.');

        } elseif ($action === 'delete_subscriber') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([$id]);
            flash('success', 'Subscriber removed.');

        } elseif ($action === 'update_blog') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'draft';
            $pdo->prepare("UPDATE blog_posts SET status = ? WHERE id = ?")->execute([$status, $id]);
            flash('success', 'Blog post updated.');

        } elseif ($action === 'delete_blog') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$id]);
            flash('success', 'Blog post deleted.');

        } elseif ($action === 'update_student') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            $pdo->prepare("UPDATE students SET status = ? WHERE id = ?")->execute([$status, $id]);
            flash('success', 'Student updated.');

        } elseif ($action === 'delete_student') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
            flash('success', 'Student deleted.');

        } elseif ($action === 'save_settings') {
            $keys = ['site_name', 'site_tagline', 'contact_email', 'phone', 'facebook_url', 'twitter_url', 'instagram_url'];
            $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $stmt->execute([$key, sanitize($_POST[$key])]);
                }
            }
            flash('success', 'Settings saved successfully.');

        } elseif ($action === 'change_password') {
            $current  = $_POST['current_password'] ?? '';
            $new      = $_POST['new_password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';
            if (strlen($new) < 8) {
                flash('error', 'New password must be at least 8 characters.');
            } elseif ($new !== $confirm) {
                flash('error', 'Passwords do not match.');
            } else {
                $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
                $stmt->execute([$admin['id']]);
                $row = $stmt->fetch();
                if ($row && password_verify($current, $row['password_hash'])) {
                    $hash = password_hash($new, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")->execute([$hash, $admin['id']]);
                    flash('success', 'Password changed successfully.');
                } else {
                    flash('error', 'Current password is incorrect.');
                }
            }
        }
    } catch (Exception $e) {
        flash('error', 'Database error. Please check your configuration.');
    }

    redirect('dashboard.php?panel=' . $panel);
}

// ── Load data for current panel ────────────────────────────────
$contacts     = [];
$subscribers  = [];
$blog_posts   = [];
$students     = [];
$settings     = [];

$stats = [
    'contacts'    => safe_count('contacts'),
    'new_contacts'=> safe_count('contacts', "status='new'"),
    'subscribers' => safe_count('newsletter_subscribers', "status='active'"),
    'blog_posts'  => safe_count('blog_posts'),
    'students'    => safe_count('students'),
];

if ($panel === 'contacts') {
    $contacts = safe_query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 100");
}
if ($panel === 'subscribers') {
    $subscribers = safe_query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 200");
}
if ($panel === 'blog') {
    $blog_posts = safe_query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 100");
}
if ($panel === 'students') {
    $students = safe_query("SELECT * FROM students ORDER BY created_at DESC LIMIT 200");
}
if ($panel === 'settings') {
    $rows = safe_query("SELECT setting_key, setting_value FROM site_settings");
    foreach ($rows as $r) { $settings[$r['setting_key']] = $r['setting_value']; }
}

$page_title = 'Admin Dashboard — ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title) ?></title>
<link rel="icon" href="../images/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,800;0,900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/clearstreet.css">
<script>document.documentElement.setAttribute('data-theme','dark');</script>
</head>
<body>

<div class="admin-layout">

  <!-- ── Sidebar ──────────────────────────────────────────────── -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar-header">
      <a href="../index.php" style="text-decoration:none;display:block;">
        <img src="../sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-sidebar">
      </a>
      <div class="admin-badge-pill"><i class="fa-solid fa-shield-halved"></i> Admin Panel</div>
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-section">
        <div class="admin-nav-label">Main</div>
        <a href="?panel=overview" class="admin-nav-link <?= $panel === 'overview' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-line"></i> Overview
        </a>
        <a href="?panel=contacts" class="admin-nav-link <?= $panel === 'contacts' ? 'active' : '' ?>">
          <i class="fa-solid fa-envelope"></i> Contact Inquiries
          <?php if ($stats['new_contacts'] > 0): ?>
          <span class="nav-badge"><?= $stats['new_contacts'] ?></span>
          <?php endif; ?>
        </a>
        <a href="?panel=subscribers" class="admin-nav-link <?= $panel === 'subscribers' ? 'active' : '' ?>">
          <i class="fa-solid fa-users"></i> Subscribers
        </a>
        <a href="?panel=students" class="admin-nav-link <?= $panel === 'students' ? 'active' : '' ?>">
          <i class="fa-solid fa-graduation-cap"></i> Students
        </a>
      </div>

      <div class="admin-nav-section">
        <div class="admin-nav-label">Content</div>
        <a href="?panel=blog" class="admin-nav-link <?= $panel === 'blog' ? 'active' : '' ?>">
          <i class="fa-solid fa-newspaper"></i> Blog Posts
        </a>
        <a href="?panel=testimonials" class="admin-nav-link <?= $panel === 'testimonials' ? 'active' : '' ?>">
          <i class="fa-solid fa-star"></i> Testimonials
        </a>
      </div>

      <div class="admin-nav-section">
        <div class="admin-nav-label">System</div>
        <a href="?panel=settings" class="admin-nav-link <?= $panel === 'settings' ? 'active' : '' ?>">
          <i class="fa-solid fa-gear"></i> Site Settings
        </a>
        <a href="?panel=account" class="admin-nav-link <?= $panel === 'account' ? 'active' : '' ?>">
          <i class="fa-solid fa-user-shield"></i> My Account
        </a>
        <a href="../index.php" class="admin-nav-link" target="_blank">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> View Site
        </a>
      </div>
    </nav>

    <div class="admin-sidebar-footer">
      <form method="POST" action="" style="margin:0;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="logout_placeholder">
        <a href="../admin-logout.php" class="admin-nav-link" style="color:rgba(244,63,94,0.70);">
          <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </a>
      </form>
    </div>
  </aside>

  <!-- ── Main ─────────────────────────────────────────────────── -->
  <div class="admin-main">

    <!-- Topbar -->
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:16px;">
        <button id="sidebar-toggle" style="display:none;background:none;border:none;color:rgba(255,255,255,0.5);font-size:1.1rem;cursor:pointer;padding:4px;">
          <i class="fa-solid fa-bars"></i>
        </button>
        <span class="admin-topbar-title">
          <?php
          $panel_labels = [
            'overview'=>'Overview','contacts'=>'Contact Inquiries','subscribers'=>'Newsletter Subscribers',
            'students'=>'Students','blog'=>'Blog Posts','testimonials'=>'Testimonials',
            'settings'=>'Site Settings','account'=>'My Account',
          ];
          echo h($panel_labels[$panel] ?? ucfirst($panel));
          ?>
        </span>
      </div>
      <div class="admin-topbar-right">
        <?php $flash_s = flash('success'); $flash_e = flash('error'); ?>
        <?php if ($flash_s): ?>
        <div class="alert alert-success" style="margin:0;padding:8px 16px;font-size:0.78rem;">
          <i class="fa-solid fa-circle-check"></i> <?= h($flash_s) ?>
        </div>
        <?php endif; ?>
        <?php if ($flash_e): ?>
        <div class="alert alert-error" style="margin:0;padding:8px 16px;font-size:0.78rem;">
          <i class="fa-solid fa-circle-exclamation"></i> <?= h($flash_e) ?>
        </div>
        <?php endif; ?>
        <a href="../index.php" class="btn-action ghost" target="_blank" style="text-decoration:none;">
          <i class="fa-solid fa-globe"></i> Site
        </a>
        <div class="admin-avatar" title="<?= h($admin['name']) ?>"><?= strtoupper(substr($admin['name'], 0, 2)) ?></div>
      </div>
    </header>

    <!-- Content -->
    <main class="admin-content">

<?php if ($panel === 'overview'): ?>
<!-- ═══ OVERVIEW ═══ -->
<div class="admin-section-label">Performance Snapshot</div>
<div class="admin-stats-grid">
  <div class="admin-stat-card" style="--stat-color:#2346FF;">
    <div class="admin-stat-icon"><i class="fa-solid fa-envelope"></i></div>
    <div class="admin-stat-label">Total Inquiries</div>
    <div class="admin-stat-value"><?= number_format($stats['contacts']) ?></div>
    <div class="admin-stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> <?= $stats['new_contacts'] ?> new</div>
  </div>
  <div class="admin-stat-card" style="--stat-color:#C9A84C;">
    <div class="admin-stat-icon"><i class="fa-solid fa-users"></i></div>
    <div class="admin-stat-label">Subscribers</div>
    <div class="admin-stat-value"><?= number_format($stats['subscribers']) ?></div>
    <div class="admin-stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> Active</div>
  </div>
  <div class="admin-stat-card" style="--stat-color:#22C55E;">
    <div class="admin-stat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
    <div class="admin-stat-label">Students</div>
    <div class="admin-stat-value"><?= number_format($stats['students']) ?></div>
    <div class="admin-stat-change up"><i class="fa-solid fa-circle"></i> Registered</div>
  </div>
  <div class="admin-stat-card" style="--stat-color:#8B5CF6;">
    <div class="admin-stat-icon"><i class="fa-solid fa-newspaper"></i></div>
    <div class="admin-stat-label">Blog Posts</div>
    <div class="admin-stat-value"><?= number_format($stats['blog_posts']) ?></div>
    <div class="admin-stat-change up"><i class="fa-solid fa-circle"></i> Published</div>
  </div>
</div>

<div class="admin-section-label">Quick Actions</div>
<div class="admin-qa-grid">
  <a href="?panel=contacts" class="admin-qa-tile">
    <i class="fa-solid fa-envelope" style="color:#2346FF;"></i>
    <span>View Contacts</span>
  </a>
  <a href="?panel=subscribers" class="admin-qa-tile">
    <i class="fa-solid fa-users" style="color:#C9A84C;"></i>
    <span>Subscribers</span>
  </a>
  <a href="?panel=students" class="admin-qa-tile">
    <i class="fa-solid fa-graduation-cap" style="color:#22C55E;"></i>
    <span>Students</span>
  </a>
  <a href="?panel=blog" class="admin-qa-tile">
    <i class="fa-solid fa-newspaper" style="color:#8B5CF6;"></i>
    <span>Blog Posts</span>
  </a>
  <a href="?panel=testimonials" class="admin-qa-tile">
    <i class="fa-solid fa-star" style="color:#C9A84C;"></i>
    <span>Testimonials</span>
  </a>
  <a href="?panel=settings" class="admin-qa-tile">
    <i class="fa-solid fa-gear" style="color:#F43F5E;"></i>
    <span>Settings</span>
  </a>
  <a href="?panel=account" class="admin-qa-tile">
    <i class="fa-solid fa-user-shield" style="color:#2346FF;"></i>
    <span>My Account</span>
  </a>
  <a href="../index.php" class="admin-qa-tile" target="_blank">
    <i class="fa-solid fa-arrow-up-right-from-square" style="color:#22C55E;"></i>
    <span>View Site</span>
  </a>
</div>

<div class="admin-grid-2">
  <!-- Recent Contacts -->
  <div class="admin-panel-card">
    <div class="admin-panel-head">
      <h3>Recent Inquiries</h3>
      <a href="?panel=contacts" class="btn-action ghost">View All</a>
    </div>
    <div class="admin-activity-list">
      <?php
      $recent = safe_query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 6");
      if ($recent):
        foreach ($recent as $c):
      ?>
      <div class="admin-activity-item">
        <div class="admin-activity-dot" style="background:<?= $c['status']==='new' ? '#2346FF' : '#22C55E' ?>;"></div>
        <div class="admin-activity-text">
          <strong><?= h($c['first_name'] . ' ' . $c['last_name']) ?></strong> — <?= h(substr($c['subject'] ?? 'Inquiry', 0, 40)) ?>
          <div style="margin-top:3px;"><span class="status-badge <?= h($c['status']) ?>"><?= h(ucfirst($c['status'])) ?></span></div>
        </div>
        <div class="admin-activity-time"><?= date('M j', strtotime($c['created_at'])) ?></div>
      </div>
      <?php endforeach;
      else: ?>
      <div class="admin-activity-item"><div class="admin-activity-text" style="text-align:center;padding:20px 0;">No inquiries yet.</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Site Stats -->
  <div class="admin-panel-card">
    <div class="admin-panel-head"><h3>System Info</h3></div>
    <div class="admin-panel-body">
      <div style="display:flex;flex-direction:column;gap:14px;">
        <?php
        $info_items = [
          ['label'=>'PHP Version', 'value'=>PHP_VERSION, 'icon'=>'fa-code'],
          ['label'=>'Admin Account', 'value'=>h($admin['name']), 'icon'=>'fa-user-shield'],
          ['label'=>'Role', 'value'=>ucfirst($admin['role']), 'icon'=>'fa-star'],
          ['label'=>'Server Time', 'value'=>date('M j, Y g:i A'), 'icon'=>'fa-clock'],
          ['label'=>'Site URL', 'value'=>SITE_URL, 'icon'=>'fa-globe'],
        ];
        foreach ($info_items as $item):
        ?>
        <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
          <div style="width:32px;height:32px;border-radius:6px;background:rgba(35,70,255,0.1);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:0.78rem;flex-shrink:0;">
            <i class="fa-solid <?= h($item['icon']) ?>"></i>
          </div>
          <div style="flex:1;">
            <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:2px;"><?= h($item['label']) ?></div>
            <div style="font-size:0.82rem;color:rgba(255,255,255,0.75);font-weight:500;"><?= $item['value'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php elseif ($panel === 'contacts'): ?>
<!-- ═══ CONTACTS ═══ -->
<div class="admin-table-card">
  <div class="admin-table-header">
    <h3>Contact Inquiries (<?= count($contacts) ?>)</h3>
    <div style="display:flex;gap:10px;align-items:center;">
      <div class="admin-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="contact-search" placeholder="Search contacts..."></div>
    </div>
  </div>
  <div class="admin-table-wrap">
    <?php if ($contacts): ?>
    <table class="admin-table" id="contacts-table">
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $c): ?>
        <tr>
          <td><strong><?= $c['id'] ?></strong></td>
          <td><strong><?= h($c['first_name'] . ' ' . $c['last_name']) ?></strong></td>
          <td><a href="mailto:<?= h($c['email']) ?>" style="color:var(--accent);text-decoration:none;"><?= h($c['email']) ?></a></td>
          <td><?= h(substr($c['subject'] ?? '—', 0, 40)) ?></td>
          <td><span class="status-badge <?= h($c['status']) ?>"><?= h(ucfirst($c['status'])) ?></span></td>
          <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <button class="btn-action ghost" onclick="viewContact(<?= htmlspecialchars(json_encode($c)) ?>)">
                <i class="fa-solid fa-eye"></i> View
              </button>
              <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Delete this contact?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_contact">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="btn-action danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="student-empty"><i class="fa-solid fa-envelope"></i><h3>No Inquiries Yet</h3><p>Contact form submissions will appear here.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- Contact View Modal -->
<div class="admin-modal-overlay" id="contact-modal">
  <div class="admin-modal">
    <div class="admin-modal-head">
      <h3>Contact Details</h3>
      <button class="admin-modal-close" onclick="closeModal('contact-modal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body" id="contact-modal-body"></div>
    <div class="admin-modal-footer">
      <form method="POST" action="" style="display:flex;gap:8px;" id="contact-status-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_contact_status">
        <input type="hidden" name="id" id="contact-status-id" value="">
        <select name="status" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);color:var(--white);padding:8px 12px;border-radius:4px;font-family:var(--font);font-size:0.8rem;outline:none;">
          <option value="new">New</option>
          <option value="read">Read</option>
          <option value="replied">Replied</option>
          <option value="spam">Spam</option>
        </select>
        <button type="submit" class="btn-action primary">Update Status</button>
      </form>
      <button class="btn-action ghost" onclick="closeModal('contact-modal')">Close</button>
    </div>
  </div>
</div>

<?php elseif ($panel === 'subscribers'): ?>
<!-- ═══ SUBSCRIBERS ═══ -->
<div class="admin-table-card">
  <div class="admin-table-header">
    <h3>Newsletter Subscribers (<?= count($subscribers) ?>)</h3>
    <div class="admin-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="sub-search" placeholder="Search subscribers..."></div>
  </div>
  <div class="admin-table-wrap">
    <?php if ($subscribers): ?>
    <table class="admin-table" id="sub-table">
      <thead><tr><th>#</th><th>Email</th><th>Name</th><th>Source</th><th>Status</th><th>Subscribed</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($subscribers as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><strong><?= h($s['email']) ?></strong></td>
          <td><?= h($s['name'] ?? '—') ?></td>
          <td><?= h($s['source'] ?? 'website') ?></td>
          <td><span class="status-badge <?= h($s['status']) ?>"><?= h(ucfirst($s['status'])) ?></span></td>
          <td><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <form method="POST" action="" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_subscriber">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="hidden" name="status" value="<?= $s['status']==='active' ? 'unsubscribed' : 'active' ?>">
                <button type="submit" class="btn-action <?= $s['status']==='active' ? 'ghost' : 'success' ?>">
                  <?= $s['status']==='active' ? 'Unsub' : 'Resub' ?>
                </button>
              </form>
              <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Delete subscriber?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_subscriber">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn-action danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="student-empty"><i class="fa-solid fa-users"></i><h3>No Subscribers Yet</h3><p>Newsletter signups will appear here.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($panel === 'students'): ?>
<!-- ═══ STUDENTS ═══ -->
<div class="admin-table-card">
  <div class="admin-table-header">
    <h3>Registered Students (<?= count($students) ?>)</h3>
    <div class="admin-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="student-search" placeholder="Search students..."></div>
  </div>
  <div class="admin-table-wrap">
    <?php if ($students): ?>
    <table class="admin-table" id="student-table">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Enrolled</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><strong><?= h($s['name']) ?></strong></td>
          <td><a href="mailto:<?= h($s['email']) ?>" style="color:var(--accent);text-decoration:none;"><?= h($s['email']) ?></a></td>
          <td><span class="status-badge <?= h($s['status'] ?? 'active') ?>"><?= h(ucfirst($s['status'] ?? 'active')) ?></span></td>
          <td><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <form method="POST" action="" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_student">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="hidden" name="status" value="<?= ($s['status']??'active')==='active' ? 'inactive' : 'active' ?>">
                <button type="submit" class="btn-action ghost"><?= ($s['status']??'active')==='active' ? 'Deactivate' : 'Activate' ?></button>
              </form>
              <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Delete student?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_student">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="btn-action danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="student-empty"><i class="fa-solid fa-graduation-cap"></i><h3>No Students Yet</h3><p>Student registrations will appear here.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($panel === 'blog'): ?>
<!-- ═══ BLOG ═══ -->
<div class="admin-table-card">
  <div class="admin-table-header">
    <h3>Blog Posts (<?= count($blog_posts) ?>)</h3>
  </div>
  <div class="admin-table-wrap">
    <?php if ($blog_posts): ?>
    <table class="admin-table">
      <thead><tr><th>#</th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($blog_posts as $b): ?>
        <tr>
          <td><?= $b['id'] ?></td>
          <td><strong><?= h(substr($b['title'], 0, 50)) ?></strong></td>
          <td><?= h($b['category'] ?? '—') ?></td>
          <td><?= h($b['author'] ?? '—') ?></td>
          <td><span class="status-badge <?= h($b['status']) ?>"><?= h(ucfirst($b['status'])) ?></span></td>
          <td><?= $b['published_at'] ? date('M j, Y', strtotime($b['published_at'])) : '—' ?></td>
          <td>
            <div style="display:flex;gap:6px;">
              <form method="POST" action="" style="margin:0;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_blog">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <input type="hidden" name="status" value="<?= $b['status']==='published' ? 'draft' : 'published' ?>">
                <button type="submit" class="btn-action ghost"><?= $b['status']==='published' ? 'Unpublish' : 'Publish' ?></button>
              </form>
              <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Delete post?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_blog">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <button type="submit" class="btn-action danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="student-empty"><i class="fa-solid fa-newspaper"></i><h3>No Blog Posts</h3><p>Blog posts will appear here.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($panel === 'testimonials'): ?>
<!-- ═══ TESTIMONIALS ═══ -->
<div class="admin-table-card">
  <div class="admin-table-header"><h3>Testimonials</h3></div>
  <div class="admin-table-wrap">
    <?php
    $testimonials = safe_query("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");
    if ($testimonials):
    ?>
    <table class="admin-table">
      <thead><tr><th>#</th><th>Name</th><th>Role</th><th>Rating</th><th>Active</th><th>Preview</th></tr></thead>
      <tbody>
        <?php foreach ($testimonials as $t): ?>
        <tr>
          <td><?= $t['id'] ?></td>
          <td><strong><?= h($t['name']) ?></strong></td>
          <td><?= h($t['role'] ?? '—') ?></td>
          <td><?= str_repeat('★', (int)$t['rating']) ?></td>
          <td><span class="status-badge <?= $t['active'] ? 'active' : 'inactive' ?>"><?= $t['active'] ? 'Active' : 'Hidden' ?></span></td>
          <td style="max-width:200px;font-size:0.75rem;color:rgba(255,255,255,0.45);">"<?= h(substr($t['content'], 0, 60)) ?>…"</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="student-empty"><i class="fa-solid fa-star"></i><h3>No Testimonials</h3></div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($panel === 'settings'): ?>
<!-- ═══ SETTINGS ═══ -->
<div class="admin-grid-2">
  <div class="admin-panel-card">
    <div class="admin-panel-head"><h3>Site Settings</h3></div>
    <div class="admin-panel-body">
      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">
        <div class="form-group">
          <label>Site Name</label>
          <input type="text" name="site_name" value="<?= h($settings['site_name'] ?? SITE_NAME) ?>">
        </div>
        <div class="form-group">
          <label>Site Tagline</label>
          <input type="text" name="site_tagline" value="<?= h($settings['site_tagline'] ?? SITE_TAGLINE) ?>">
        </div>
        <div class="form-group">
          <label>Contact Email</label>
          <input type="email" name="contact_email" value="<?= h($settings['contact_email'] ?? SITE_EMAIL) ?>">
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" value="<?= h($settings['phone'] ?? SITE_PHONE) ?>">
        </div>
        <div class="form-group">
          <label>Facebook URL</label>
          <input type="url" name="facebook_url" value="<?= h($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
        </div>
        <div class="form-group">
          <label>Twitter/X URL</label>
          <input type="url" name="twitter_url" value="<?= h($settings['twitter_url'] ?? '') ?>" placeholder="https://x.com/...">
        </div>
        <div class="form-group" style="margin-bottom:24px;">
          <label>Instagram URL</label>
          <input type="url" name="instagram_url" value="<?= h($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
        </div>
        <button type="submit" class="btn-action primary" style="width:100%;justify-content:center;padding:12px;">
          <i class="fa-solid fa-floppy-disk"></i> Save Settings
        </button>
      </form>
    </div>
  </div>

  <div class="admin-panel-card">
    <div class="admin-panel-head"><h3>Admin Info</h3></div>
    <div class="admin-panel-body">
      <div style="text-align:center;padding:20px 0;">
        <div class="admin-avatar" style="width:64px;height:64px;font-size:1.4rem;margin:0 auto 16px;"><?= strtoupper(substr($admin['name'],0,2)) ?></div>
        <div style="font-size:1rem;font-weight:700;color:var(--white);margin-bottom:4px;"><?= h($admin['name']) ?></div>
        <div style="font-size:0.78rem;color:rgba(255,255,255,0.4);margin-bottom:8px;"><?= h($admin['email']) ?></div>
        <span class="status-badge active"><?= h(ucfirst($admin['role'])) ?></span>
      </div>

      <div style="border-top:1px solid rgba(255,255,255,0.07);padding-top:24px;margin-top:20px;">
        <h4 style="font-size:0.78rem;color:rgba(255,255,255,0.5);font-family:var(--font);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:16px;">Useful Links</h4>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <a href="../index.php" class="btn-action ghost" target="_blank" style="text-decoration:none;justify-content:center;"><i class="fa-solid fa-home"></i> View Homepage</a>
          <a href="../contact.php" class="btn-action ghost" target="_blank" style="text-decoration:none;justify-content:center;"><i class="fa-solid fa-envelope"></i> Contact Page</a>
          <a href="../courses.php" class="btn-action ghost" target="_blank" style="text-decoration:none;justify-content:center;"><i class="fa-solid fa-graduation-cap"></i> Courses Page</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php elseif ($panel === 'account'): ?>
<!-- ═══ ACCOUNT ═══ -->
<div class="admin-grid-2">
  <div class="admin-panel-card">
    <div class="admin-panel-head"><h3>Change Password</h3></div>
    <div class="admin-panel-body">
      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" name="current_password" placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="new_password" placeholder="Min. 8 characters" required minlength="8">
        </div>
        <div class="form-group" style="margin-bottom:24px;">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" placeholder="Repeat new password" required>
        </div>
        <button type="submit" class="btn-action primary" style="width:100%;justify-content:center;padding:12px;">
          <i class="fa-solid fa-key"></i> Update Password
        </button>
      </form>
    </div>
  </div>

  <div class="admin-panel-card">
    <div class="admin-panel-head"><h3>Session Info</h3></div>
    <div class="admin-panel-body">
      <div style="display:flex;flex-direction:column;gap:14px;">
        <?php
        $sess_items = [
          ['label'=>'Logged in as','value'=>h($admin['name'])],
          ['label'=>'Email','value'=>h($admin['email'])],
          ['label'=>'Role','value'=>h(ucfirst($admin['role']))],
          ['label'=>'Session Start','value'=>date('M j, Y g:i A')],
          ['label'=>'IP Address','value'=>h($_SERVER['REMOTE_ADDR'] ?? 'Unknown')],
        ];
        foreach ($sess_items as $item):
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
          <span style="font-size:0.72rem;color:rgba(255,255,255,0.35);font-weight:600;letter-spacing:0.1em;text-transform:uppercase;"><?= $item['label'] ?></span>
          <span style="font-size:0.82rem;color:rgba(255,255,255,0.75);font-weight:500;"><?= $item['value'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:24px;">
        <a href="../admin-logout.php" class="btn-action danger" style="text-decoration:none;width:100%;justify-content:center;padding:12px;">
          <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </a>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>

    </main>
  </div><!-- /admin-main -->
</div><!-- /admin-layout -->

<script>
// ── Sidebar mobile toggle
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('admin-sidebar');
if (sidebarToggle) {
  sidebarToggle.style.display = window.innerWidth < 900 ? 'block' : 'none';
  sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  window.addEventListener('resize', () => {
    sidebarToggle.style.display = window.innerWidth < 900 ? 'block' : 'none';
    if (window.innerWidth >= 900) sidebar.classList.remove('open');
  });
}

// ── Contact view modal
function viewContact(data) {
  const body = document.getElementById('contact-modal-body');
  const idInput = document.getElementById('contact-status-id');
  if (!body) return;
  idInput.value = data.id;
  body.innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
      <div>
        <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:5px;">Name</div>
        <div style="font-size:0.9rem;color:rgba(255,255,255,0.85);font-weight:600;">${escHtml((data.first_name||'') + ' ' + (data.last_name||''))}</div>
      </div>
      <div>
        <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:5px;">Email</div>
        <a href="mailto:${escHtml(data.email||'')}" style="color:var(--accent);font-size:0.85rem;">${escHtml(data.email||'—')}</a>
      </div>
      <div>
        <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:5px;">Phone</div>
        <div style="font-size:0.85rem;color:rgba(255,255,255,0.75);">${escHtml(data.phone||'—')}</div>
      </div>
      <div>
        <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:5px;">Date</div>
        <div style="font-size:0.85rem;color:rgba(255,255,255,0.75);">${escHtml(data.created_at||'—')}</div>
      </div>
    </div>
    <div style="margin-bottom:16px;">
      <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:8px;">Subject</div>
      <div style="font-size:0.85rem;color:rgba(255,255,255,0.75);font-weight:500;">${escHtml(data.subject||'—')}</div>
    </div>
    <div>
      <div style="font-size:0.62rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:8px;">Message</div>
      <div style="font-size:0.85rem;color:rgba(255,255,255,0.65);line-height:1.7;background:rgba(255,255,255,0.04);padding:16px;border-radius:6px;border:1px solid rgba(255,255,255,0.07);">${escHtml(data.message||'—')}</div>
    </div>
  `;
  openModal('contact-modal');
}

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

function openModal(id) {
  document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
  document.getElementById(id)?.classList.remove('open');
}

// Close modal on overlay click
document.querySelectorAll('.admin-modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) overlay.classList.remove('open');
  });
});

// ── Live table search
function setupSearch(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;
  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    table.querySelectorAll('tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}
setupSearch('contact-search', 'contacts-table');
setupSearch('sub-search', 'sub-table');
setupSearch('student-search', 'student-table');

// ── Page entrance
document.body.style.opacity = '0';
document.body.style.transition = 'opacity 0.3s ease';
requestAnimationFrame(() => requestAnimationFrame(() => { document.body.style.opacity = '1'; }));
</script>
</body>
</html>
