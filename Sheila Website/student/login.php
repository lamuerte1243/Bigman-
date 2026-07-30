<?php
/**
 * Sheila The Writer — Student Login
 */
$in_subdir = true;
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

$page_title = 'Student Login — ' . SITE_NAME;

// Redirect if already logged in
if (student_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
$success = flash('success') ?? '';
$info = flash('info') ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Security token mismatch. Please try again.';
    } else {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter your email and password.';
        } else {
            try {
                $pdo  = get_db();
                $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $student = $stmt->fetch();

                if ($student && password_verify($password, $student['password_hash'])) {
                    if ($student['status'] === 'inactive') {
                        $error = 'Your account has been deactivated. Please contact support.';
                    } else {
                        session_regenerate_id(true);
                        $_SESSION['student_id']    = $student['id'];
                        $_SESSION['student_name']  = $student['name'];
                        $_SESSION['student_email'] = $student['email'];

                        // Update last login
                        $pdo->prepare("UPDATE students SET last_login = NOW() WHERE id = ?")
                            ->execute([$student['id']]);

                        flash('success', 'Welcome back, ' . $student['name'] . '!');
                        redirect('dashboard.php');
                    }
                } else {
                    sleep(1); // Brute-force prevention
                    $error = 'Invalid email or password. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'Unable to connect to the database. Please try again later.';
            }
        }
    }
}
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
<script>
(function(){
  var t = localStorage.getItem('sheila-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', t);
})();
</script>
</head>
<body>

<div class="student-auth-page">
  <!-- Background grid -->
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.015) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse 80% 60% at 50% 0%,black 0%,transparent 100%);pointer-events:none;"></div>
  <!-- Accent glow -->
  <div style="position:absolute;top:-200px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(35,70,255,0.12) 0%,transparent 70%);pointer-events:none;"></div>

  <div class="student-auth-box">
    <!-- Brand -->
    <div class="admin-login-brand">
      <a href="../index.php" style="text-decoration:none;">
        <img src="../sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-login">
      </a>
      <div class="admin-badge" style="margin-top:12px;display:inline-flex;align-items:center;gap:6px;background:rgba(35,70,255,0.12);border:1px solid rgba(35,70,255,0.25);padding:5px 14px;border-radius:20px;font-size:0.65rem;letter-spacing:0.1em;text-transform:uppercase;font-weight:600;color:rgba(255,255,255,0.6);">
        <i class="fa-solid fa-graduation-cap" style="color:var(--accent);"></i> Student Portal
      </div>
    </div>

    <!-- Auth tabs -->
    <div class="auth-tabs">
      <a href="login.php" class="auth-tab active">Sign In</a>
      <a href="register.php" class="auth-tab">Create Account</a>
    </div>

    <!-- Login Card -->
    <div class="student-auth-card">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--white);margin-bottom:6px;">Welcome Back</h2>
      <p style="font-size:0.8rem;color:rgba(255,255,255,0.40);margin-bottom:28px;">Sign in to access your courses and progress</p>

      <?php if ($error): ?>
      <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?>
      </div>
      <?php endif; ?>

      <?php if ($success): ?>
      <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> <?= h($success) ?>
      </div>
      <?php endif; ?>

      <?php if ($info): ?>
      <div class="alert alert-info" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-info"></i> <?= h($info) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="login-form" novalidate>
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="email">Email Address</label>
          <div style="position:relative;">
            <i class="fa-solid fa-envelope" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="email" id="email" name="email"
                   placeholder="your@email.com"
                   value="<?= h($_POST['email'] ?? '') ?>"
                   required autocomplete="email"
                   style="padding-left:40px;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:28px;">
          <label for="password">Password</label>
          <div style="position:relative;">
            <i class="fa-solid fa-lock" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="password" id="password" name="password"
                   placeholder="••••••••"
                   required autocomplete="current-password"
                   style="padding-left:40px;padding-right:46px;">
            <button type="button" id="toggle-pw" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.30);cursor:pointer;font-size:0.8rem;padding:4px;">
              <i class="fa-solid fa-eye" id="pw-icon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:0.9rem;">
          Sign In <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div style="margin-top:24px;text-align:center;">
        <p style="font-size:0.8rem;color:rgba(255,255,255,0.35);">
          Don't have an account?
          <a href="register.php" style="color:var(--accent);font-weight:600;text-decoration:none;margin-left:4px;">Create one free</a>
        </p>
      </div>
    </div>

    <div style="text-align:center;margin-top:20px;">
      <a href="../index.php" style="font-size:0.72rem;color:rgba(255,255,255,0.25);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color 0.2s;">
        <i class="fa-solid fa-arrow-left"></i> Back to Website
      </a>
    </div>

    <div style="text-align:center;margin-top:12px;">
      <p style="font-size:0.62rem;color:rgba(255,255,255,0.15);letter-spacing:0.04em;">
        &copy; <?= date('Y') ?> <?= h(SITE_NAME) ?>. All rights reserved.
      </p>
    </div>
  </div>
</div>

<script>
// Password toggle
const togglePw = document.getElementById('toggle-pw');
const pwInput  = document.getElementById('password');
const pwIcon   = document.getElementById('pw-icon');
if (togglePw) {
  togglePw.addEventListener('click', () => {
    const isText = pwInput.type === 'text';
    pwInput.type = isText ? 'password' : 'text';
    pwIcon.className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
  });
}

// Page entrance animation
document.body.style.opacity = '0';
document.body.style.transition = 'opacity 0.4s ease';
requestAnimationFrame(() => requestAnimationFrame(() => { document.body.style.opacity = '1'; }));
</script>
</body>
</html>
