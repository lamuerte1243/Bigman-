<?php
/**
 * Sheila The Writer — Admin Login (Login Only, No Signup)
 */
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

$in_subdir = false;
$page_title = 'Admin Login — ' . SITE_NAME;

// Redirect if already logged in as admin
if (!empty($_SESSION['admin_id'])) {
    redirect('admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Security token mismatch. Please try again.';
    } else {
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            try {
                $pdo  = get_db();
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND active = 1 LIMIT 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Login successful
                    session_regenerate_id(true);
                    $_SESSION['admin_id']    = $admin['id'];
                    $_SESSION['admin_name']  = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_role']  = $admin['role'];

                    // Update last login
                    $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")
                        ->execute([$admin['id']]);

                    flash('success', 'Welcome back, ' . $admin['name'] . '!');
                    redirect('admin/dashboard.php');
                } else {
                    // Add delay to prevent brute force
                    sleep(1);
                    $error = 'Invalid email or password. Please try again.';
                }
            } catch (Exception $e) {
                // If DB not set up, use config credentials for demo
                if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']    = 1;
                    $_SESSION['admin_name']  = 'Site Admin';
                    $_SESSION['admin_email'] = ADMIN_EMAIL;
                    $_SESSION['admin_role']  = 'superadmin';
                    flash('success', 'Welcome back, Admin!');
                    redirect('admin/dashboard.php');
                } else {
                    $error = 'Invalid email or password.';
                }
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
<link rel="icon" href="images/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,800;0,900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/clearstreet.css">
<script>
(function(){
  document.documentElement.setAttribute('data-theme','dark');
})();
</script>
</head>
<body>

<div class="admin-login-page">
  <!-- Animated grid -->
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.015) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse 80% 60% at 50% 0%,black 0%,transparent 100%);pointer-events:none;"></div>

  <div class="admin-login-box">
    <!-- Brand -->
    <div class="admin-login-brand">
      <a href="index.php" style="text-decoration:none;">
        <img src="sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-login">
      </a>
      <div class="admin-badge"><i class="fa-solid fa-shield-halved"></i> Admin Portal</div>
    </div>

    <!-- Login Card -->
    <div class="admin-login-card">
      <h2>Sign In</h2>
      <p class="subtitle">Authorized personnel only. All access is logged.</p>

      <?php if ($error): ?>
      <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= h($error) ?>
      </div>
      <?php endif; ?>

      <?php $flash_success = flash('success'); if ($flash_success): ?>
      <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-check"></i> <?= h($flash_success) ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="email">Email Address</label>
          <div style="position:relative;">
            <i class="fa-solid fa-envelope" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="email" id="email" name="email" placeholder="admin@sheilathewriter.com"
                   value="<?= h($_POST['email'] ?? '') ?>"
                   required autocomplete="email"
                   style="padding-left:40px;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:28px;">
          <label for="password">Password</label>
          <div style="position:relative;">
            <i class="fa-solid fa-lock" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="password" id="password" name="password" placeholder="••••••••"
                   required autocomplete="current-password"
                   style="padding-left:40px;padding-right:46px;">
            <button type="button" id="toggle-pw" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.30);cursor:pointer;font-size:0.8rem;padding:4px;">
              <i class="fa-solid fa-eye" id="pw-icon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:16px;">
          Sign In to Dashboard <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <div style="margin-top:24px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.07);text-align:center;">
        <a href="index.php" style="font-size:0.75rem;color:rgba(255,255,255,0.30);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color 0.2s ease;">
          <i class="fa-solid fa-arrow-left"></i> Back to Website
        </a>
      </div>
    </div>

    <div style="text-align:center;margin-top:20px;">
      <p style="font-size:0.65rem;color:rgba(255,255,255,0.18);letter-spacing:0.04em;">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?>. Admin access only. <a href="privacy.php" style="color:rgba(255,255,255,0.25);text-decoration:none;">Privacy Policy</a>
      </p>
    </div>
  </div>
</div>

<script>
// Password toggle
const togglePw = document.getElementById('toggle-pw');
const pwInput = document.getElementById('password');
const pwIcon = document.getElementById('pw-icon');
if (togglePw) {
  togglePw.addEventListener('click', () => {
    const isText = pwInput.type === 'text';
    pwInput.type = isText ? 'password' : 'text';
    pwIcon.className = isText ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
  });
}

// Page entrance
document.body.style.opacity = '0';
document.body.style.transition = 'opacity 0.4s ease';
requestAnimationFrame(() => requestAnimationFrame(() => { document.body.style.opacity = '1'; }));
</script>
</body>
</html>
