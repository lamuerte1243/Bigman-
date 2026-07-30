<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
$_user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title ?? SITE_NAME . ' — ' . SITE_TAGLINE) ?></title>
<meta name="description" content="<?= h($meta_desc ?? 'Expert academic help for TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER and more. Online tutoring, course assistance, and personalized academic support.') ?>">
<link rel="canonical" href="<?= SITE_URL ?>/<?= basename($_SERVER['PHP_SELF']) ?>">
<link rel="icon" href="<?= isset($in_subdir) ? '../' : '' ?>images/favicon.svg" type="image/svg+xml">
<!-- Preconnect -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Fonts: Playfair Display + Plus Jakarta Sans + Space Grotesk + Inter (premium corporate stack) -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Site CSS -->
<link rel="stylesheet" href="<?= isset($in_subdir) ? '../' : '' ?>css/style.css">
<link rel="stylesheet" href="<?= isset($in_subdir) ? '../' : '' ?>css/clearstreet.css">
<?= $extra_head ?? '' ?>
<!-- Prevent theme flash: apply saved preference before paint, no transition suppression needed -->
<script>
(function(){
  var t=localStorage.getItem('sheila-theme')||'dark';
  document.documentElement.setAttribute('data-theme',t);
})();
</script>
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────────── -->
<header class="site-header" id="site-header">
  <nav class="navbar">
    <div class="container">
      <!-- Brand -->
      <a href="<?= isset($in_subdir) ? '../' : '' ?>index.php" class="brand">
        <img src="<?= isset($in_subdir) ? '../' : '' ?>sheila-white.png" alt="Sheila The Writer" class="brand-logo-img">
      </a>

      <!-- Nav Links -->
      <ul class="nav-links" id="nav-links">
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>index.php" class="<?= is_active('index') ?>">Home</a></li>
        <li class="has-dropdown">
          <a href="#">Services <i class="fa-solid fa-chevron-down" style="font-size:.55rem;opacity:.5;"></i></a>
          <ul class="dropdown">
            <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php"><i class="fa-solid fa-graduation-cap"></i> Exam Prep Courses</a></li>
            <li><a href="<?= isset($in_subdir) ? '../' : '' ?>tutoring.php"><i class="fa-solid fa-chalkboard-user"></i> Tutoring</a></li>
            <li><a href="<?= isset($in_subdir) ? '../' : '' ?>study-materials.php"><i class="fa-solid fa-book-open"></i> Study Materials</a></li>
          </ul>
        </li>
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php" class="<?= is_active('courses') ?>">Courses</a></li>
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>tutoring.php" class="<?= is_active('tutoring') ?>">Tutoring</a></li>
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>study-materials.php" class="<?= is_active('study-materials') ?>">Study Materials</a></li>
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>about.php" class="<?= is_active('about') ?>">About</a></li>
        <li><a href="<?= isset($in_subdir) ? '../' : '' ?>blog.php" class="<?= is_active('blog') ?>">Blog</a></li>
      </ul>

      <!-- CTA / Nav Actions -->
      <div class="nav-actions">

        <?php if (!empty($_SESSION['student_id'])): ?>
          <!-- Student is logged in — show avatar pill + logout -->
          <a href="<?= isset($in_subdir) ? '../' : '' ?>student/dashboard.php" class="nav-user-pill">
            <span class="nav-user-avatar"><?= strtoupper(substr($_SESSION['student_name'] ?? 'S', 0, 1)) ?></span>
            <span><?= h(explode(' ', $_SESSION['student_name'] ?? 'Student')[0]) ?></span>
          </a>
          <a href="<?= isset($in_subdir) ? '../' : '' ?>student/logout.php" class="nav-login" title="Sign out">
            <i class="fa-solid fa-right-from-bracket"></i>
          </a>
        <?php else: ?>
          <!-- Guest — show Login + Sign Up -->
          <a href="<?= isset($in_subdir) ? '../' : '' ?>student/login.php" class="nav-login">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span class="nav-auth-label">Log In</span>
          </a>
          <a href="<?= isset($in_subdir) ? '../' : '' ?>student/register.php" class="nav-signup">
            <i class="fa-solid fa-user-plus"></i>
            <span class="nav-auth-label">Sign Up</span>
          </a>
        <?php endif; ?>

        <!-- Premium pill theme toggle -->
        <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark/light mode" title="Toggle dark/light mode">
          <!-- JS will inject .tt-track with pill options -->
        </button>

      </div>

      <!-- Mobile Toggle -->
      <button class="menu-toggle" id="menu-toggle" aria-label="Toggle menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>
</header>
