<?php
/**
 * Sheila The Writer — Student Registration
 */
$in_subdir = true;
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

$page_title = 'Create Student Account — ' . SITE_NAME;

// Redirect if already logged in
if (student_logged_in()) {
    redirect('dashboard.php');
}

$error   = '';
$success = '';
$posted  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $error = 'Security token mismatch. Please try again.';
    } else {
        $posted['name']     = sanitize($_POST['name'] ?? '');
        $posted['email']    = sanitize($_POST['email'] ?? '');
        $posted['phone']    = sanitize($_POST['phone'] ?? '');
        $password           = $_POST['password'] ?? '';
        $confirm_password   = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($posted['name'])) {
            $error = 'Full name is required.';
        } elseif (empty($posted['email']) || !filter_var($posted['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $pdo = get_db();

                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ? LIMIT 1");
                $stmt->execute([$posted['email']]);
                if ($stmt->fetch()) {
                    $error = 'An account with this email already exists. Please <a href="login.php">sign in</a> instead.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("INSERT INTO students (name, email, phone, password_hash, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())")
                        ->execute([$posted['name'], $posted['email'], $posted['phone'], $hash]);

                    $student_id = (int) $pdo->lastInsertId();

                    // Auto-login after registration
                    session_regenerate_id(true);
                    $_SESSION['student_id']    = $student_id;
                    $_SESSION['student_name']  = $posted['name'];
                    $_SESSION['student_email'] = $posted['email'];

                    flash('success', 'Welcome to Sheila The Writer, ' . $posted['name'] . '! Your account has been created.');
                    redirect('dashboard.php');
                }
            } catch (Exception $e) {
                $error = 'Unable to create your account right now. Please try again later.';
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
      <a href="login.php" class="auth-tab">Sign In</a>
      <a href="register.php" class="auth-tab active">Create Account</a>
    </div>

    <!-- Register Card -->
    <div class="student-auth-card">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:700;color:var(--white);margin-bottom:6px;">Start Learning Today</h2>
      <p style="font-size:0.8rem;color:rgba(255,255,255,0.40);margin-bottom:28px;">Create your free student account and access premium exam prep</p>

      <?php if ($error): ?>
      <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= $error /* may contain HTML link */ ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="register-form" novalidate>
        <?= csrf_field() ?>

        <!-- Full Name -->
        <div class="form-group">
          <label for="name">Full Name</label>
          <div style="position:relative;">
            <i class="fa-solid fa-user" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="text" id="name" name="name"
                   placeholder="Your full name"
                   value="<?= h($posted['name'] ?? '') ?>"
                   required autocomplete="name"
                   style="padding-left:40px;">
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address</label>
          <div style="position:relative;">
            <i class="fa-solid fa-envelope" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="email" id="email" name="email"
                   placeholder="your@email.com"
                   value="<?= h($posted['email'] ?? '') ?>"
                   required autocomplete="email"
                   style="padding-left:40px;">
          </div>
        </div>

        <!-- Phone (optional) -->
        <div class="form-group">
          <label for="phone">Phone Number <span style="color:rgba(255,255,255,0.25);font-weight:400;">(Optional)</span></label>
          <div style="position:relative;">
            <i class="fa-solid fa-phone" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="tel" id="phone" name="phone"
                   placeholder="+1 (302) 555-0100"
                   value="<?= h($posted['phone'] ?? '') ?>"
                   autocomplete="tel"
                   style="padding-left:40px;">
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <div style="position:relative;">
            <i class="fa-solid fa-lock" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="password" id="password" name="password"
                   placeholder="Min. 8 characters"
                   required autocomplete="new-password"
                   style="padding-left:40px;padding-right:46px;"
                   oninput="checkStrength(this.value)">
            <button type="button" id="toggle-pw" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,0.30);cursor:pointer;font-size:0.8rem;padding:4px;">
              <i class="fa-solid fa-eye" id="pw-icon"></i>
            </button>
          </div>
          <!-- Password strength meter -->
          <div class="password-strength" id="strength-container" style="display:none;">
            <div class="strength-bar">
              <div class="strength-fill" id="strength-fill"></div>
            </div>
            <span class="strength-text" id="strength-text">Weak</span>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group" style="margin-bottom:28px;">
          <label for="confirm_password">Confirm Password</label>
          <div style="position:relative;">
            <i class="fa-solid fa-lock-open" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.25);font-size:0.8rem;pointer-events:none;"></i>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat your password"
                   required autocomplete="new-password"
                   style="padding-left:40px;"
                   oninput="checkMatch()">
          </div>
          <div id="match-msg" style="font-size:0.65rem;margin-top:5px;display:none;"></div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:0.9rem;" id="submit-btn">
          Create Free Account <i class="fa-solid fa-arrow-right"></i>
        </button>

        <p style="font-size:0.68rem;color:rgba(255,255,255,0.25);text-align:center;margin-top:16px;line-height:1.5;">
          By creating an account you agree to our
          <a href="../privacy.php" style="color:rgba(255,255,255,0.40);text-decoration:underline;">Privacy Policy</a>
          and
          <a href="../terms.php" style="color:rgba(255,255,255,0.40);text-decoration:underline;">Terms of Service</a>.
        </p>
      </form>

      <div style="margin-top:20px;text-align:center;">
        <p style="font-size:0.8rem;color:rgba(255,255,255,0.35);">
          Already have an account?
          <a href="login.php" style="color:var(--accent);font-weight:600;text-decoration:none;margin-left:4px;">Sign in</a>
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

// Password strength checker
function checkStrength(val) {
  const container = document.getElementById('strength-container');
  const fill      = document.getElementById('strength-fill');
  const text      = document.getElementById('strength-text');

  if (!val) { container.style.display = 'none'; return; }
  container.style.display = 'block';

  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w: '20%', color: '#F43F5E', label: 'Too weak' },
    { w: '40%', color: '#F97316', label: 'Weak' },
    { w: '60%', color: '#EAB308', label: 'Fair' },
    { w: '80%', color: '#22C55E', label: 'Strong' },
    { w: '100%', color: '#10B981', label: 'Very strong' },
  ];
  const level = levels[Math.min(score - 1, 4)] || levels[0];
  fill.style.width    = level.w;
  fill.style.background = level.color;
  text.textContent    = level.label;
  text.style.color    = level.color;
}

// Password match checker
function checkMatch() {
  const pw   = document.getElementById('password').value;
  const cpw  = document.getElementById('confirm_password').value;
  const msg  = document.getElementById('match-msg');
  if (!cpw) { msg.style.display = 'none'; return; }
  msg.style.display = 'block';
  if (pw === cpw) {
    msg.textContent  = '✓ Passwords match';
    msg.style.color  = '#22C55E';
  } else {
    msg.textContent  = '✗ Passwords do not match';
    msg.style.color  = '#F43F5E';
  }
}

// Page entrance animation
document.body.style.opacity = '0';
document.body.style.transition = 'opacity 0.4s ease';
requestAnimationFrame(() => requestAnimationFrame(() => { document.body.style.opacity = '1'; }));
</script>
</body>
</html>
