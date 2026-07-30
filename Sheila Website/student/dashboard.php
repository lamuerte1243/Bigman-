<?php
/**
 * Sheila The Writer — Student Dashboard
 * Full-featured student portal with course tracking, progress, account management
 */
$in_subdir = true;
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

require_student_login();
$student = current_student();

// ── Panel routing ──────────────────────────────────────────────
$panel = $_GET['panel'] ?? 'overview';

// ── Safe DB wrapper ────────────────────────────────────────────
function s_query(string $sql, array $params = []): array {
    try {
        $pdo  = get_db();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}
function s_count(string $table, string $where = '1', array $params = []): int {
    try {
        $pdo  = get_db();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table` WHERE $where");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

// ── Handle POST actions ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!csrf_check()) {
        flash('error', 'Security check failed. Please try again.');
        redirect('dashboard.php?panel=' . $panel);
    }

    $action = $_POST['action'];

    try {
        $pdo = get_db();

        // ── Enroll in course ──
        if ($action === 'enroll_course') {
            $course_id   = (int)($_POST['course_id'] ?? 0);
            $course_name = sanitize($_POST['course_name'] ?? '');
            $exam_type   = sanitize($_POST['exam_type'] ?? '');
            if ($course_id && $course_name) {
                // Check not already enrolled
                $exists = $pdo->prepare("SELECT id FROM student_enrollments WHERE student_id = ? AND course_id = ?");
                $exists->execute([$student['id'], $course_id]);
                if (!$exists->fetch()) {
                    $pdo->prepare("INSERT INTO student_enrollments (student_id, course_id, course_name, exam_type, status, enrolled_at) VALUES (?,?,?,?,'active',NOW())")
                        ->execute([$student['id'], $course_id, $course_name, $exam_type]);
                    flash('success', 'Successfully enrolled in ' . $course_name . '!');
                } else {
                    flash('info', 'You are already enrolled in this course.');
                }
            }

        // ── Update progress ──
        } elseif ($action === 'update_progress') {
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $progress      = min(100, max(0, (int)($_POST['progress'] ?? 0)));
            $pdo->prepare("UPDATE student_enrollments SET progress = ?, updated_at = NOW() WHERE id = ? AND student_id = ?")
                ->execute([$progress, $enrollment_id, $student['id']]);
            if ($progress >= 100) {
                $pdo->prepare("UPDATE student_enrollments SET status = 'completed', completed_at = NOW() WHERE id = ? AND student_id = ?")
                    ->execute([$enrollment_id, $student['id']]);
            }
            flash('success', 'Progress updated to ' . $progress . '%.');

        // ── Update profile ──
        } elseif ($action === 'update_profile') {
            $name  = sanitize($_POST['name'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            if (empty($name)) {
                flash('error', 'Name cannot be empty.');
            } else {
                $pdo->prepare("UPDATE students SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?")
                    ->execute([$name, $phone, $student['id']]);
                $_SESSION['student_name'] = $name;
                $student['name'] = $name;
                flash('success', 'Profile updated successfully.');
            }

        // ── Change password ──
        } elseif ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (strlen($new) < 8) {
                flash('error', 'New password must be at least 8 characters.');
            } elseif ($new !== $confirm) {
                flash('error', 'New passwords do not match.');
            } else {
                $stmt = $pdo->prepare("SELECT password_hash FROM students WHERE id = ?");
                $stmt->execute([$student['id']]);
                $row = $stmt->fetch();
                if ($row && password_verify($current, $row['password_hash'])) {
                    $hash = password_hash($new, PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE students SET password_hash = ?, updated_at = NOW() WHERE id = ?")
                        ->execute([$hash, $student['id']]);
                    flash('success', 'Password changed successfully.');
                } else {
                    flash('error', 'Current password is incorrect.');
                }
            }

        // ── Drop course ──
        } elseif ($action === 'drop_course') {
            $enrollment_id = (int)($_POST['enrollment_id'] ?? 0);
            $pdo->prepare("DELETE FROM student_enrollments WHERE id = ? AND student_id = ?")
                ->execute([$enrollment_id, $student['id']]);
            flash('success', 'Course removed from your dashboard.');
        }

    } catch (Exception $e) {
        flash('error', 'An error occurred. Please try again.');
    }

    redirect('dashboard.php?panel=' . $panel);
}

// ── Load data ──────────────────────────────────────────────────
// Student full record
$student_row = [];
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student['id']]);
    $student_row = $stmt->fetch() ?: [];
} catch (Exception $e) {}

// Enrollments
$enrollments = s_query(
    "SELECT * FROM student_enrollments WHERE student_id = ? ORDER BY enrolled_at DESC",
    [$student['id']]
);

$total_enrolled  = count($enrollments);
$completed       = count(array_filter($enrollments, fn($e) => $e['status'] === 'completed'));
$in_progress_cnt = count(array_filter($enrollments, fn($e) => $e['status'] === 'active' && ($e['progress'] ?? 0) > 0));
$avg_progress    = $total_enrolled ? round(array_sum(array_column($enrollments, 'progress')) / $total_enrolled) : 0;

// Available courses catalog (static — would be DB in production)
$course_catalog = [
    ['id'=>1, 'name'=>'TEAS Exam Prep — Complete Course',   'exam_type'=>'TEAS',        'duration'=>'6 weeks', 'lessons'=>48, 'level'=>'Beginner to Advanced', 'icon'=>'fa-stethoscope',    'color'=>'#2346FF'],
    ['id'=>2, 'name'=>'HESI A2 Complete Preparation',       'exam_type'=>'HESI',        'duration'=>'5 weeks', 'lessons'=>40, 'level'=>'Intermediate',         'icon'=>'fa-hospital',       'color'=>'#7C3AED'],
    ['id'=>3, 'name'=>'NCLEX-RN Master Prep Program',       'exam_type'=>'NCLEX',       'duration'=>'8 weeks', 'lessons'=>64, 'level'=>'Advanced',             'icon'=>'fa-heart-pulse',    'color'=>'#059669'],
    ['id'=>4, 'name'=>'GED Complete Study Program',         'exam_type'=>'GED',         'duration'=>'10 weeks','lessons'=>80, 'level'=>'All Levels',           'icon'=>'fa-graduation-cap', 'color'=>'#D97706'],
    ['id'=>5, 'name'=>'GRE Verbal & Quantitative Prep',     'exam_type'=>'GRE',         'duration'=>'8 weeks', 'lessons'=>56, 'level'=>'Graduate Level',       'icon'=>'fa-brain',          'color'=>'#DC2626'],
    ['id'=>6, 'name'=>'ACCUPLACER Placement Test Prep',     'exam_type'=>'ACCUPLACER',  'duration'=>'3 weeks', 'lessons'=>24, 'level'=>'Beginner',             'icon'=>'fa-calculator',     'color'=>'#0891B2'],
    ['id'=>7, 'name'=>'StraighterLine Success Blueprint',   'exam_type'=>'Online Course','duration'=>'4 weeks', 'lessons'=>32, 'level'=>'Self-Paced',          'icon'=>'fa-laptop',         'color'=>'#7C3AED'],
    ['id'=>8, 'name'=>'Sophia Learning Mastery Guide',      'exam_type'=>'Online Course','duration'=>'4 weeks', 'lessons'=>28, 'level'=>'Self-Paced',          'icon'=>'fa-book-open',      'color'=>'#059669'],
];

// Get enrolled course IDs to filter catalog
$enrolled_course_ids = array_column($enrollments, 'course_id');

$page_title = 'Student Dashboard — ' . SITE_NAME;
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/clearstreet.css">
<script>document.documentElement.setAttribute('data-theme','dark');</script>
</head>
<body>

<div class="student-layout">

  <!-- ── Sidebar ──────────────────────────────────────────────── -->
  <aside class="student-sidebar" id="student-sidebar">
    <div class="admin-sidebar-header">
      <a href="../index.php" style="text-decoration:none;display:block;">
        <img src="../sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-sidebar">
      </a>
    </div>

    <!-- Student avatar in sidebar -->
    <div style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.06);">
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#7C3AED);display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;color:white;flex-shrink:0;font-family:'Playfair Display',serif;">
          <?= strtoupper(substr($student['name'], 0, 1)) ?>
        </div>
        <div style="min-width:0;">
          <div style="font-size:0.82rem;font-weight:600;color:var(--white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= h($student['name']) ?></div>
          <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= h($student['email']) ?></div>
        </div>
      </div>
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-section">
        <div class="admin-nav-label">My Learning</div>
        <a href="?panel=overview" class="admin-nav-link <?= $panel === 'overview' ? 'active' : '' ?>">
          <i class="fa-solid fa-house"></i> Overview
        </a>
        <a href="?panel=courses" class="admin-nav-link <?= $panel === 'courses' ? 'active' : '' ?>">
          <i class="fa-solid fa-graduation-cap"></i> My Courses
          <?php if ($total_enrolled): ?>
          <span class="nav-badge"><?= $total_enrolled ?></span>
          <?php endif; ?>
        </a>
        <a href="?panel=browse" class="admin-nav-link <?= $panel === 'browse' ? 'active' : '' ?>">
          <i class="fa-solid fa-compass"></i> Browse Courses
        </a>
        <a href="?panel=progress" class="admin-nav-link <?= $panel === 'progress' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-line"></i> My Progress
        </a>
      </div>

      <div class="admin-nav-section">
        <div class="admin-nav-label">Account</div>
        <a href="?panel=profile" class="admin-nav-link <?= $panel === 'profile' ? 'active' : '' ?>">
          <i class="fa-solid fa-user-circle"></i> Profile Settings
        </a>
        <a href="?panel=support" class="admin-nav-link <?= $panel === 'support' ? 'active' : '' ?>">
          <i class="fa-solid fa-headset"></i> Get Support
        </a>
        <a href="../index.php" class="admin-nav-link" target="_blank">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Main Site
        </a>
      </div>
    </nav>

    <div class="admin-sidebar-footer">
      <a href="logout.php" class="admin-nav-link" style="color:rgba(244,63,94,0.70);"
         onclick="return confirm('Sign out of your student account?');">
        <i class="fa-solid fa-right-from-bracket"></i> Sign Out
      </a>
    </div>
  </aside>

  <!-- ── Main ─────────────────────────────────────────────────── -->
  <div class="student-main">

    <!-- Topbar -->
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:16px;">
        <button id="sidebar-toggle" style="background:none;border:none;color:rgba(255,255,255,0.5);font-size:1.1rem;cursor:pointer;padding:4px;display:none;">
          <i class="fa-solid fa-bars"></i>
        </button>
        <span class="admin-topbar-title">
          <?php
          $panel_labels = [
            'overview'=>'Dashboard Overview','courses'=>'My Courses','browse'=>'Browse Courses',
            'progress'=>'My Progress','profile'=>'Profile Settings','support'=>'Get Support',
          ];
          echo h($panel_labels[$panel] ?? ucfirst($panel));
          ?>
        </span>
      </div>
      <div class="admin-topbar-right">
        <?php $flash_s = flash('success'); $flash_e = flash('error'); $flash_i = flash('info'); ?>
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
        <?php if ($flash_i): ?>
        <div class="alert alert-info" style="margin:0;padding:8px 16px;font-size:0.78rem;">
          <i class="fa-solid fa-circle-info"></i> <?= h($flash_i) ?>
        </div>
        <?php endif; ?>
        <a href="../contact.php?intent=enroll" class="btn-action ghost" style="text-decoration:none;" target="_blank">
          <i class="fa-solid fa-headset"></i> Help
        </a>
      </div>
    </header>

    <!-- Content area -->
    <div class="student-content">

<?php /* ════════════════════════════════════════
   OVERVIEW PANEL
═══════════════════════════════════════ */ ?>
<?php if ($panel === 'overview'): ?>

      <!-- Welcome banner -->
      <div style="background:linear-gradient(135deg,rgba(35,70,255,0.15),rgba(124,58,237,0.10));border:1px solid rgba(35,70,255,0.20);border-radius:16px;padding:32px;margin-bottom:28px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(35,70,255,0.12),transparent 70%);pointer-events:none;"></div>
        <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--white);margin-bottom:8px;">
          Welcome back, <?= h(explode(' ', $student['name'])[0]) ?>! 👋
        </h2>
        <p style="color:rgba(255,255,255,0.55);font-size:0.88rem;margin-bottom:20px;max-width:500px;">
          You're making great progress. Keep going — every session gets you closer to passing your exam!
        </p>
        <a href="?panel=browse" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-plus"></i> Enroll in a Course
        </a>
      </div>

      <!-- Stats grid -->
      <div class="student-stats-grid" style="margin-bottom:28px;">
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(35,70,255,0.12);color:var(--accent);">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div class="stat-value"><?= $total_enrolled ?></div>
          <div class="stat-label">Enrolled Courses</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(34,197,94,0.12);color:#22C55E;">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div class="stat-value"><?= $completed ?></div>
          <div class="stat-label">Completed</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(234,179,8,0.12);color:#EAB308;">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </div>
          <div class="stat-value"><?= $in_progress_cnt ?></div>
          <div class="stat-label">In Progress</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(168,85,247,0.12);color:#A855F7;">
            <i class="fa-solid fa-chart-pie"></i>
          </div>
          <div class="stat-value"><?= $avg_progress ?>%</div>
          <div class="stat-label">Avg. Progress</div>
        </div>
      </div>

      <!-- Active courses quick view -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">
        <div>
          <h3 style="font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:16px;">Active Courses</h3>
          <?php $active_enrollments = array_filter($enrollments, fn($e) => $e['status'] === 'active'); ?>
          <?php if (empty($active_enrollments)): ?>
          <div class="student-empty" style="padding:40px 20px;">
            <i class="fa-solid fa-graduation-cap"></i>
            <h3>No courses yet</h3>
            <p>Browse and enroll in a course to get started.</p>
            <a href="?panel=browse" class="btn btn-primary btn-sm">Browse Courses</a>
          </div>
          <?php else: ?>
          <?php foreach (array_slice($active_enrollments, 0, 3) as $enroll): ?>
          <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:20px;margin-bottom:12px;transition:border-color 0.2s;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
              <div style="font-size:0.85rem;font-weight:600;color:var(--white);line-height:1.3;"><?= h($enroll['course_name']) ?></div>
              <span style="font-size:0.65rem;font-weight:700;color:var(--accent);background:rgba(35,70,255,0.12);border:1px solid rgba(35,70,255,0.25);border-radius:8px;padding:2px 8px;white-space:nowrap;margin-left:8px;"><?= h($enroll['exam_type']) ?></span>
            </div>
            <!-- Progress bar -->
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="flex:1;height:6px;background:rgba(255,255,255,0.08);border-radius:3px;overflow:hidden;">
                <div style="width:<?= (int)($enroll['progress'] ?? 0) ?>%;height:100%;background:linear-gradient(90deg,var(--accent),#7C3AED);border-radius:3px;transition:width 0.5s ease;"></div>
              </div>
              <span style="font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.60);min-width:36px;text-align:right;"><?= (int)($enroll['progress'] ?? 0) ?>%</span>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (count($active_enrollments) > 3): ?>
          <a href="?panel=courses" style="font-size:0.78rem;color:var(--accent);text-decoration:none;">View all <?= count($active_enrollments) ?> courses →</a>
          <?php endif; ?>
          <?php endif; ?>
        </div>

        <div>
          <h3 style="font-size:0.75rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:16px;">Quick Actions</h3>
          <div style="display:grid;gap:10px;">
            <?php
            $quick_actions = [
              ['icon'=>'fa-compass',      'label'=>'Browse All Courses',    'href'=>'?panel=browse',   'color'=>'#2346FF'],
              ['icon'=>'fa-chart-line',   'label'=>'View My Progress',      'href'=>'?panel=progress', 'color'=>'#7C3AED'],
              ['icon'=>'fa-user-circle',  'label'=>'Update Profile',        'href'=>'?panel=profile',  'color'=>'#059669'],
              ['icon'=>'fa-headset',      'label'=>'Contact Support',       'href'=>'?panel=support',  'color'=>'#D97706'],
              ['icon'=>'fa-envelope',     'label'=>'Send Inquiry',          'href'=>'../contact.php',  'color'=>'#DC2626'],
              ['icon'=>'fa-book-open',    'label'=>'Study Materials',       'href'=>'../study-materials.php','color'=>'#0891B2'],
            ];
            foreach ($quick_actions as $qa):
            ?>
            <a href="<?= h($qa['href']) ?>" style="display:flex;align-items:center;gap:14px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:14px 16px;text-decoration:none;transition:all 0.18s cubic-bezier(0.25,0.46,0.45,0.94);"
               onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.borderColor='rgba(255,255,255,0.12)';"
               onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.borderColor='rgba(255,255,255,0.06)';">
              <div style="width:34px;height:34px;border-radius:8px;background:<?= h($qa['color']) ?>20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid <?= h($qa['icon']) ?>" style="font-size:0.8rem;color:<?= h($qa['color']) ?>;"></i>
              </div>
              <span style="font-size:0.82rem;font-weight:500;color:rgba(255,255,255,0.75);"><?= h($qa['label']) ?></span>
              <i class="fa-solid fa-chevron-right" style="font-size:0.6rem;color:rgba(255,255,255,0.20);margin-left:auto;"></i>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

<?php /* ════════════════════════════════════════
   MY COURSES PANEL
═══════════════════════════════════════ */ ?>
<?php elseif ($panel === 'courses'): ?>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--white);">My Enrolled Courses</h2>
        <a href="?panel=browse" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Enroll in More</a>
      </div>

      <?php if (empty($enrollments)): ?>
      <div class="student-empty">
        <i class="fa-solid fa-graduation-cap"></i>
        <h3>No courses enrolled yet</h3>
        <p>Browse our catalog and enroll in a course to start your exam prep journey.</p>
        <a href="?panel=browse" class="btn btn-primary btn-sm"><i class="fa-solid fa-compass"></i> Browse Courses</a>
      </div>
      <?php else: ?>
      <div style="display:grid;gap:16px;">
        <?php foreach ($enrollments as $enroll): ?>
        <?php
          $prog = (int)($enroll['progress'] ?? 0);
          $status_color = $enroll['status'] === 'completed' ? '#22C55E' : ($prog > 0 ? '#EAB308' : 'rgba(255,255,255,0.35)');
          $status_label = $enroll['status'] === 'completed' ? 'Completed' : ($prog > 0 ? 'In Progress' : 'Not Started');
        ?>
        <div class="student-course-card" style="position:relative;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:0;">
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                <span style="font-size:0.65rem;font-weight:700;color:var(--accent);background:rgba(35,70,255,0.12);border:1px solid rgba(35,70,255,0.25);border-radius:8px;padding:3px 10px;text-transform:uppercase;letter-spacing:0.08em;"><?= h($enroll['exam_type']) ?></span>
                <span style="font-size:0.65rem;font-weight:600;color:<?= $status_color ?>;background:<?= $status_color ?>1a;border:1px solid <?= $status_color ?>40;border-radius:8px;padding:3px 10px;"><?= $status_label ?></span>
              </div>
              <h3 style="font-size:1rem;font-weight:700;color:var(--white);margin-bottom:6px;font-family:'Playfair Display',serif;"><?= h($enroll['course_name']) ?></h3>
              <p style="font-size:0.75rem;color:rgba(255,255,255,0.35);">Enrolled: <?= date('M j, Y', strtotime($enroll['enrolled_at'])) ?></p>
            </div>
            <div style="text-align:center;min-width:80px;">
              <div style="width:64px;height:64px;border-radius:50%;background:conic-gradient(var(--accent) <?= $prog ?>%,rgba(255,255,255,0.06) 0%);display:flex;align-items:center;justify-content:center;margin:0 auto 6px;position:relative;">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:var(--white);"><?= $prog ?>%</div>
              </div>
              <span style="font-size:0.62rem;color:rgba(255,255,255,0.30);">Progress</span>
            </div>
          </div>

          <!-- Progress bar -->
          <div style="margin:16px 0 12px;">
            <div style="height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden;">
              <div style="width:<?= $prog ?>%;height:100%;background:linear-gradient(90deg,var(--accent),#7C3AED);border-radius:3px;transition:width 0.5s ease;"></div>
            </div>
          </div>

          <!-- Actions -->
          <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <!-- Update progress form -->
            <form method="POST" action="" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="update_progress">
              <input type="hidden" name="enrollment_id" value="<?= (int)$enroll['id'] ?>">
              <input type="hidden" name="panel" value="courses">
              <div style="display:flex;align-items:center;gap:6px;">
                <label style="font-size:0.72rem;color:rgba(255,255,255,0.40);white-space:nowrap;">Update:</label>
                <input type="number" name="progress" min="0" max="100" value="<?= $prog ?>"
                       style="width:60px;padding:6px 10px;font-size:0.78rem;border-radius:8px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);color:var(--white);">
                <span style="font-size:0.72rem;color:rgba(255,255,255,0.35);">%</span>
              </div>
              <button type="submit" class="btn-action" style="font-size:0.72rem;">Save</button>
            </form>

            <!-- Drop course -->
            <form method="POST" action="" onsubmit="return confirm('Remove this course from your dashboard?');" style="margin-left:auto;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="drop_course">
              <input type="hidden" name="enrollment_id" value="<?= (int)$enroll['id'] ?>">
              <input type="hidden" name="panel" value="courses">
              <button type="submit" class="btn-action danger" style="font-size:0.72rem;">
                <i class="fa-solid fa-trash-can"></i> Remove
              </button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

<?php /* ════════════════════════════════════════
   BROWSE COURSES PANEL
═══════════════════════════════════════ */ ?>
<?php elseif ($panel === 'browse'): ?>

      <div style="margin-bottom:24px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--white);margin-bottom:8px;">Course Catalog</h2>
        <p style="font-size:0.83rem;color:rgba(255,255,255,0.45);">Choose a course and enroll instantly. Our expert materials are tailored to get you passing on the first try.</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
        <?php foreach ($course_catalog as $course): ?>
        <?php $is_enrolled = in_array($course['id'], $enrolled_course_ids); ?>
        <div style="background:rgba(255,255,255,0.04);border:1px solid <?= $is_enrolled ? 'rgba(35,70,255,0.35)' : 'rgba(255,255,255,0.07)' ?>;border-radius:16px;padding:24px;transition:all 0.2s cubic-bezier(0.25,0.46,0.45,0.94);position:relative;overflow:hidden;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,0.3)';"
             onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';">
          <!-- Course color accent bar -->
          <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,<?= h($course['color']) ?>,<?= h($course['color']) ?>80);border-radius:16px 16px 0 0;"></div>

          <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">
            <div style="width:48px;height:48px;border-radius:12px;background:<?= h($course['color']) ?>1a;border:1px solid <?= h($course['color']) ?>30;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fa-solid <?= h($course['icon']) ?>" style="font-size:1.1rem;color:<?= h($course['color']) ?>;"></i>
            </div>
            <div>
              <div style="font-size:0.62rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:<?= h($course['color']) ?>;margin-bottom:2px;"><?= h($course['exam_type']) ?></div>
              <h3 style="font-size:0.92rem;font-weight:700;color:var(--white);line-height:1.3;font-family:'Playfair Display',serif;"><?= h($course['name']) ?></h3>
            </div>
          </div>

          <div style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:5px;font-size:0.7rem;color:rgba(255,255,255,0.40);">
              <i class="fa-solid fa-clock" style="font-size:0.62rem;color:rgba(255,255,255,0.25);"></i>
              <?= h($course['duration']) ?>
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.7rem;color:rgba(255,255,255,0.40);">
              <i class="fa-solid fa-play-circle" style="font-size:0.62rem;color:rgba(255,255,255,0.25);"></i>
              <?= h($course['lessons']) ?> lessons
            </div>
            <div style="display:flex;align-items:center;gap:5px;font-size:0.7rem;color:rgba(255,255,255,0.40);">
              <i class="fa-solid fa-signal" style="font-size:0.62rem;color:rgba(255,255,255,0.25);"></i>
              <?= h($course['level']) ?>
            </div>
          </div>

          <?php if ($is_enrolled): ?>
          <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:rgba(35,70,255,0.08);border:1px solid rgba(35,70,255,0.20);border-radius:10px;font-size:0.78rem;color:var(--accent);font-weight:600;">
            <i class="fa-solid fa-circle-check"></i> Already Enrolled
            <a href="?panel=courses" style="margin-left:auto;font-size:0.72rem;color:rgba(255,255,255,0.50);text-decoration:none;">View →</a>
          </div>
          <?php else: ?>
          <form method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="enroll_course">
            <input type="hidden" name="panel" value="browse">
            <input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
            <input type="hidden" name="course_name" value="<?= h($course['name']) ?>">
            <input type="hidden" name="exam_type" value="<?= h($course['exam_type']) ?>">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:0.82rem;">
              <i class="fa-solid fa-plus"></i> Enroll Now — Free
            </button>
          </form>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

<?php /* ════════════════════════════════════════
   PROGRESS PANEL
═══════════════════════════════════════ */ ?>
<?php elseif ($panel === 'progress'): ?>

      <div style="margin-bottom:24px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--white);margin-bottom:8px;">My Progress</h2>
        <p style="font-size:0.83rem;color:rgba(255,255,255,0.45);">Track your performance across all enrolled courses.</p>
      </div>

      <!-- Summary cards -->
      <div class="student-stats-grid" style="margin-bottom:28px;">
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(35,70,255,0.12);color:var(--accent);">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div class="stat-value"><?= $total_enrolled ?></div>
          <div class="stat-label">Total Courses</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(34,197,94,0.12);color:#22C55E;">
            <i class="fa-solid fa-trophy"></i>
          </div>
          <div class="stat-value"><?= $completed ?></div>
          <div class="stat-label">Completed</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(234,179,8,0.12);color:#EAB308;">
            <i class="fa-solid fa-fire"></i>
          </div>
          <div class="stat-value"><?= $in_progress_cnt ?></div>
          <div class="stat-label">In Progress</div>
        </div>
        <div class="admin-stat-card">
          <div class="stat-icon" style="background:rgba(168,85,247,0.12);color:#A855F7;">
            <i class="fa-solid fa-percent"></i>
          </div>
          <div class="stat-value"><?= $avg_progress ?>%</div>
          <div class="stat-label">Avg. Progress</div>
        </div>
      </div>

      <?php if (empty($enrollments)): ?>
      <div class="student-empty">
        <i class="fa-solid fa-chart-line"></i>
        <h3>No progress to show yet</h3>
        <p>Enroll in a course to start tracking your progress.</p>
        <a href="?panel=browse" class="btn btn-primary btn-sm">Browse Courses</a>
      </div>
      <?php else: ?>
      <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.06);">
          <h3 style="font-size:0.85rem;font-weight:700;color:var(--white);">Course Progress Overview</h3>
        </div>
        <div style="padding:0;">
          <?php foreach ($enrollments as $i => $enroll): ?>
          <?php $prog = (int)($enroll['progress'] ?? 0); ?>
          <div style="padding:20px 24px;<?= $i < count($enrollments)-1 ? 'border-bottom:1px solid rgba(255,255,255,0.05);' : '' ?>display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:180px;">
              <div style="font-size:0.85rem;font-weight:600;color:var(--white);margin-bottom:4px;"><?= h($enroll['course_name']) ?></div>
              <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);"><?= h($enroll['exam_type']) ?> • Enrolled <?= date('M j', strtotime($enroll['enrolled_at'])) ?></div>
            </div>
            <div style="flex:2;min-width:200px;">
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                  <div style="width:<?= $prog ?>%;height:100%;background:<?= $enroll['status'] === 'completed' ? '#22C55E' : 'linear-gradient(90deg,var(--accent),#7C3AED)' ?>;border-radius:4px;transition:width 0.6s ease;"></div>
                </div>
                <span style="font-size:0.78rem;font-weight:700;color:var(--white);min-width:38px;text-align:right;"><?= $prog ?>%</span>
              </div>
            </div>
            <div>
              <?php if ($enroll['status'] === 'completed'): ?>
              <span style="font-size:0.65rem;font-weight:700;color:#22C55E;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.25);border-radius:8px;padding:4px 10px;">✓ Completed</span>
              <?php elseif ($prog > 0): ?>
              <span style="font-size:0.65rem;font-weight:700;color:#EAB308;background:rgba(234,179,8,0.12);border:1px solid rgba(234,179,8,0.25);border-radius:8px;padding:4px 10px;">In Progress</span>
              <?php else: ?>
              <span style="font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.35);background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);border-radius:8px;padding:4px 10px;">Not Started</span>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

<?php /* ════════════════════════════════════════
   PROFILE PANEL
═══════════════════════════════════════ */ ?>
<?php elseif ($panel === 'profile'): ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;flex-wrap:wrap;">

        <!-- Profile Card -->
        <div class="student-profile-card">
          <div class="student-avatar">
            <?= strtoupper(substr($student['name'], 0, 1)) ?>
          </div>
          <div class="student-name"><?= h($student['name']) ?></div>
          <div class="student-email"><?= h($student['email']) ?></div>
          <?php if (!empty($student_row['phone'])): ?>
          <div style="font-size:0.75rem;color:rgba(255,255,255,0.35);margin-top:4px;"><?= h($student_row['phone']) ?></div>
          <?php endif; ?>
          <div style="margin-top:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.06);display:flex;gap:24px;justify-content:center;">
            <div style="text-align:center;">
              <div style="font-size:1.2rem;font-weight:700;color:var(--white);font-family:'Playfair Display',serif;"><?= $total_enrolled ?></div>
              <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.08em;">Enrolled</div>
            </div>
            <div style="text-align:center;">
              <div style="font-size:1.2rem;font-weight:700;color:#22C55E;font-family:'Playfair Display',serif;"><?= $completed ?></div>
              <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.08em;">Completed</div>
            </div>
            <div style="text-align:center;">
              <div style="font-size:1.2rem;font-weight:700;color:var(--accent);font-family:'Playfair Display',serif;"><?= $avg_progress ?>%</div>
              <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.08em;">Avg Progress</div>
            </div>
          </div>
          <?php if (!empty($student_row['created_at'])): ?>
          <div style="margin-top:16px;font-size:0.7rem;color:rgba(255,255,255,0.25);">
            Member since <?= date('F Y', strtotime($student_row['created_at'])) ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Edit forms -->
        <div style="display:flex;flex-direction:column;gap:20px;">

          <!-- Update Profile -->
          <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:24px;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--white);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
              <i class="fa-solid fa-user-pen" style="color:var(--accent);font-size:0.85rem;"></i>
              Update Profile
            </h3>
            <form method="POST" action="">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="update_profile">
              <input type="hidden" name="panel" value="profile">
              <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= h($student['name']) ?>" required>
              </div>
              <div class="form-group" style="margin-bottom:20px;">
                <label>Phone Number <span style="color:rgba(255,255,255,0.25);font-weight:400;">(Optional)</span></label>
                <input type="tel" name="phone" value="<?= h($student_row['phone'] ?? '') ?>" placeholder="+1 (302) 555-0100">
              </div>
              <div class="form-group" style="margin-bottom:20px;">
                <label>Email Address</label>
                <input type="email" value="<?= h($student['email']) ?>" disabled style="opacity:0.5;cursor:not-allowed;">
                <small style="font-size:0.65rem;color:rgba(255,255,255,0.25);">Email cannot be changed. Contact support if needed.</small>
              </div>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-save"></i> Save Changes
              </button>
            </form>
          </div>

          <!-- Change Password -->
          <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:24px;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--white);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
              <i class="fa-solid fa-lock" style="color:#D97706;font-size:0.85rem;"></i>
              Change Password
            </h3>
            <form method="POST" action="">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="change_password">
              <input type="hidden" name="panel" value="profile">
              <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" placeholder="Your current password" required>
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Min. 8 characters" required minlength="8">
              </div>
              <div class="form-group" style="margin-bottom:20px;">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat new password" required>
              </div>
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-key"></i> Change Password
              </button>
            </form>
          </div>

        </div>
      </div>

<?php /* ════════════════════════════════════════
   SUPPORT PANEL
═══════════════════════════════════════ */ ?>
<?php elseif ($panel === 'support'): ?>

      <div style="margin-bottom:28px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;color:var(--white);margin-bottom:8px;">Get Expert Support</h2>
        <p style="font-size:0.83rem;color:rgba(255,255,255,0.45);">Our team is available 24/7 to help you succeed. Reach out anytime.</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;margin-bottom:32px;">
        <?php
        $support_options = [
          ['icon'=>'fa-comments','title'=>'Live Chat','desc'=>'Chat with a tutor right now. Available 24/7.','action'=>'Start Chat','href'=>'../contact.php','color'=>'#2346FF'],
          ['icon'=>'fa-phone','title'=>'Call Us','desc'=>'Speak directly with an academic expert.','action'=>'Call Now','href'=>'tel:+13025550142','color'=>'#22C55E'],
          ['icon'=>'fa-envelope','title'=>'Email Support','desc'=>'Send us a detailed message. We reply in under 60 min.','action'=>'Send Email','href'=>'mailto:hello@sheilathewriter.com','color'=>'#D97706'],
          ['icon'=>'fa-calendar-check','title'=>'Book a Session','desc'=>'Schedule a 1-on-1 tutoring session at your convenience.','action'=>'Book Session','href'=>'../tutoring.php','color'=>'#7C3AED'],
        ];
        foreach ($support_options as $opt):
        ?>
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:24px;text-align:center;transition:all 0.2s ease;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.borderColor='rgba(255,255,255,0.15)';"
             onmouseout="this.style.transform='translateY(0)';this.style.borderColor='rgba(255,255,255,0.08)';">
          <div style="width:56px;height:56px;border-radius:14px;background:<?= h($opt['color']) ?>1a;border:1px solid <?= h($opt['color']) ?>30;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i class="fa-solid <?= h($opt['icon']) ?>" style="font-size:1.3rem;color:<?= h($opt['color']) ?>;"></i>
          </div>
          <h3 style="font-size:0.95rem;font-weight:700;color:var(--white);margin-bottom:8px;"><?= h($opt['title']) ?></h3>
          <p style="font-size:0.78rem;color:rgba(255,255,255,0.40);margin-bottom:18px;line-height:1.5;"><?= h($opt['desc']) ?></p>
          <a href="<?= h($opt['href']) ?>" class="btn btn-primary btn-sm" style="display:inline-flex;">
            <?= h($opt['action']) ?> <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- FAQ section -->
      <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:28px;">
        <h3 style="font-size:1rem;font-weight:700;color:var(--white);margin-bottom:20px;font-family:'Playfair Display',serif;">Frequently Asked Questions</h3>
        <?php
        $faqs = [
          ['q'=>'How do I access my course materials?','a'=>'Your course materials are available immediately upon enrollment. Log in to your dashboard and navigate to "My Courses" to access all your enrolled content.'],
          ['q'=>'How quickly can I get help from a tutor?','a'=>'Most students are connected with a qualified tutor within 2–4 hours of submitting a request. Urgent help is often available within 1 hour.'],
          ['q'=>'Is my information kept confidential?','a'=>'Absolutely. We have a strict confidentiality policy. Your personal information is never shared with anyone under any circumstances.'],
          ['q'=>'What if I need to cancel or change my course?','a'=>'You can remove a course from your dashboard anytime. If you have questions about specific packages, contact our support team directly.'],
          ['q'=>'How do I track my progress?','a'=>'Use the "Update Progress" feature in your course dashboard to log your completion percentage. We\'ll show you a clear overview in the Progress panel.'],
        ];
        foreach ($faqs as $i => $faq):
        ?>
        <div style="padding:16px 0;<?= $i < count($faqs)-1 ? 'border-bottom:1px solid rgba(255,255,255,0.05);' : '' ?>">
          <div style="font-size:0.85rem;font-weight:600;color:var(--white);margin-bottom:6px;"><?= h($faq['q']) ?></div>
          <div style="font-size:0.78rem;color:rgba(255,255,255,0.45);line-height:1.6;"><?= h($faq['a']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

<?php endif; /* end panel switch */ ?>

    </div><!-- /student-content -->
  </div><!-- /student-main -->
</div><!-- /student-layout -->

<script>
// Sidebar toggle for mobile
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar       = document.getElementById('student-sidebar');
if (sidebarToggle && sidebar) {
  sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
      sidebar.classList.remove('open');
    }
  });
}

// Show sidebar toggle on mobile
function handleResize() {
  if (window.innerWidth <= 900) {
    sidebarToggle && (sidebarToggle.style.display = 'block');
  } else {
    sidebarToggle && (sidebarToggle.style.display = 'none');
    sidebar && sidebar.classList.remove('open');
  }
}
handleResize();
window.addEventListener('resize', handleResize);

// Page entrance
document.body.style.opacity = '0';
document.body.style.transition = 'opacity 0.3s ease';
requestAnimationFrame(() => requestAnimationFrame(() => { document.body.style.opacity = '1'; }));
</script>
</body>
</html>
