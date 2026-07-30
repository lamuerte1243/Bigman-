<?php
/**
 * Sheila The Writer — Tutoring Booking Flow
 * Full-page multi-step: Type → Subject → Schedule → Details → Confirmation
 * No modals. No popups.
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';
$in_subdir = true;

// ── Route params ───────────────────────────────────────────────
$step    = $_GET['step']    ?? 'select-type';
$type    = trim($_GET['type']    ?? '');   // 'ai' or 'human'
$subject = trim($_GET['subject'] ?? '');
$days    = trim($_GET['days']    ?? '');
$hours   = trim($_GET['hours']   ?? '');

// ── Subjects list ──────────────────────────────────────────────
$subjects = [
  'teas'         => ['label'=>'TEAS',         'icon'=>'fa-stethoscope'],
  'hesi'         => ['label'=>'HESI A²',      'icon'=>'fa-heart-pulse'],
  'nclex'        => ['label'=>'NCLEX',         'icon'=>'fa-hospital'],
  'ged'          => ['label'=>'GED',           'icon'=>'fa-graduation-cap'],
  'gre'          => ['label'=>'GRE',           'icon'=>'fa-university'],
  'accuplacer'   => ['label'=>'ACCUPLACER',    'icon'=>'fa-calculator'],
  'math'         => ['label'=>'Mathematics',   'icon'=>'fa-square-root-variable'],
  'science'      => ['label'=>'Science',       'icon'=>'fa-flask'],
  'reading'      => ['label'=>'Reading & Writing','icon'=>'fa-book-open-reader'],
  'anatomy'      => ['label'=>'Anatomy & Physiology','icon'=>'fa-bone'],
  'chemistry'    => ['label'=>'Chemistry',     'icon'=>'fa-atom'],
  'straighterline'=> ['label'=>'StraighterLine','icon'=>'fa-laptop-code'],
  'sophia'       => ['label'=>'Sophia Learning','icon'=>'fa-school'],
  'other'        => ['label'=>'Other / Multiple','icon'=>'fa-ellipsis'],
];

// ── Pricing ────────────────────────────────────────────────────
$pricing = [
  'ai'    => ['price_label'=>'$49', 'per'=>'/month', 'desc'=>'Unlimited AI support, 24/7'],
  'human' => ['price_label'=>'$79', 'per'=>'/session', 'desc'=>'Live 1-on-1 with a specialist'],
];

// ── POST handler (booking details step) ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'details') {
  $name    = trim($_POST['student_name']  ?? '');
  $email   = trim($_POST['student_email'] ?? '');
  $note    = trim($_POST['student_note']  ?? '');
  $btype   = trim($_POST['booking_type']  ?? 'human');
  $bsub    = trim($_POST['booking_subject'] ?? '');
  $bdays   = trim($_POST['booking_days']  ?? '');
  $bhours  = trim($_POST['booking_hours'] ?? '');
  $errors  = [];

  if (strlen($name) < 2) $errors[] = 'Please enter your full name.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
  if (empty($bsub)) $errors[] = 'Please select a subject.';

  if (empty($errors)) {
    $price_info = $pricing[$btype] ?? $pricing['human'];
    $_SESSION['booking_name']    = $name;
    $_SESSION['booking_email']   = $email;
    $_SESSION['booking_note']    = $note;
    $_SESSION['booking_type']    = $btype;
    $_SESSION['booking_subject'] = $subjects[$bsub]['label'] ?? ucfirst($bsub);
    $_SESSION['booking_days']    = $bdays;
    $_SESSION['booking_hours']   = $bhours;
    $_SESSION['booking_price']   = $price_info['price_label'];
    $_SESSION['booking_per']     = $price_info['per'];

    header('Location: booking.php?step=confirmation&type=' . urlencode($btype) . '&subject=' . urlencode($bsub));
    exit;
  }
}

// ── Page title ────────────────────────────────────────────────
$titles = [
  'select-type'   => 'Book Tutoring — Choose Your Tutor Type',
  'select-subject'=> 'Book Tutoring — Select Subject',
  'select-schedule'=> 'Book Tutoring — Preferred Schedule',
  'details'       => 'Book Tutoring — Your Details',
  'confirmation'  => 'Booking Confirmed!',
];
$page_title = ($titles[$step] ?? 'Book Tutoring') . ' — ' . SITE_NAME;

include '../includes/header.php';
?>

<!-- ── Shared booking page wrapper ──────────────────────────── -->
<div class="order-page booking-page">

  <!-- Hero bar -->
  <div class="order-page-hero">
    <div class="container">
      <div class="breadcrumb" style="margin-bottom:16px;">
        <a href="../index.php">Home</a><span class="sep">›</span>
        <a href="../tutoring.php">Tutoring</a><span class="sep">›</span>
        <span>Book a Session</span>
      </div>
      <h1 style="font-size:clamp(1.6rem,3.5vw,2.5rem);">
        <?php if ($step === 'select-type'): ?>
          Choose Your <span style="color:var(--accent);">Tutor Type</span>
        <?php elseif ($step === 'select-subject'): ?>
          Select Your <span style="color:var(--accent);">Subject</span>
        <?php elseif ($step === 'select-schedule'): ?>
          Your Preferred <span style="color:var(--accent);">Schedule</span>
        <?php elseif ($step === 'details'): ?>
          Your <span style="color:var(--accent);">Details</span>
        <?php else: ?>
          Booking <span style="color:var(--green);">Confirmed!</span>
        <?php endif; ?>
      </h1>
    </div>
  </div>

  <!-- Step progress indicator (steps 1–4 only) -->
  <?php if ($step !== 'confirmation'): ?>
  <div class="container" style="margin-top:40px;">
    <?php
    $step_map = ['select-type'=>1,'select-subject'=>2,'select-schedule'=>3,'details'=>4];
    $active_num = $step_map[$step] ?? 1;
    $step_labels = ['Tutor Type','Subject','Schedule','Your Details'];
    ?>
    <div class="checkout-steps">
      <?php foreach ($step_labels as $i => $label): ?>
      <?php
        $n = $i + 1;
        $cls = $n < $active_num ? 'done' : ($n === $active_num ? 'active' : '');
      ?>
      <div class="checkout-step <?= $cls ?>">
        <div class="checkout-step-num">
          <?php if ($n < $active_num): ?>
          <i class="fa-solid fa-check" style="font-size:.55rem;"></i>
          <?php else: ?>
          <?= $n ?>
          <?php endif; ?>
        </div>
        <span class="checkout-step-label"><?= $label ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="container">

  <!-- ══════════════════════════════════════════════════════
       STEP 1 — SELECT TUTOR TYPE
  ══════════════════════════════════════════════════════ -->
  <?php if ($step === 'select-type'): ?>
  <div class="booking-type-grid">

    <!-- AI Tutor -->
    <a href="booking.php?step=select-subject&type=ai"
       class="booking-type-card"
       onmousemove="this.style.setProperty('--mouse-x',((event.clientX-this.getBoundingClientRect().left)/this.offsetWidth*100)+'%');this.style.setProperty('--mouse-y',((event.clientY-this.getBoundingClientRect().top)/this.offsetHeight*100)+'%')">
      <div class="booking-type-icon">
        <i class="fa-solid fa-robot"></i>
      </div>
      <span style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);display:block;margin-bottom:10px;">24/7 Available</span>
      <h3>AI Tutor</h3>
      <p>Get instant answers to any study question at any hour. Our AI tutor is trained on all major exam content and available around the clock.</p>
      <div class="booking-type-price">$49</div>
      <div class="booking-type-price-sub">per month · unlimited access</div>
      <div style="margin-top:24px;" class="btn btn-primary btn-sm" style="width:100%;">
        Select AI Tutor <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>

    <!-- Human Tutor -->
    <a href="booking.php?step=select-subject&type=human"
       class="booking-type-card gold-card"
       onmousemove="this.style.setProperty('--mouse-x',((event.clientX-this.getBoundingClientRect().left)/this.offsetWidth*100)+'%');this.style.setProperty('--mouse-y',((event.clientY-this.getBoundingClientRect().top)/this.offsetHeight*100)+'%')">
      <div class="booking-type-icon">
        <i class="fa-solid fa-user-graduate"></i>
      </div>
      <span style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);display:block;margin-bottom:10px;">Expert Led</span>
      <h3>Human Tutor</h3>
      <p>Work one-on-one with a credentialed expert in your specific exam. Live sessions via Zoom or Google Meet, plus session recordings.</p>
      <div class="booking-type-price">$79</div>
      <div class="booking-type-price-sub">per session · 60 minutes</div>
      <div style="margin-top:24px;" class="btn btn-gold btn-sm">
        Select Human Tutor <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>

  </div><!-- /booking-type-grid -->

  <!-- Feature comparison -->
  <div class="glass-card" style="max-width:840px;margin:0 auto 80px;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.82rem;">
        <thead>
          <tr style="border-bottom:1px solid var(--border);">
            <th style="text-align:left;padding:12px 16px;color:var(--white-40);font-weight:700;letter-spacing:.1em;text-transform:uppercase;font-size:.68rem;">Feature</th>
            <th style="text-align:center;padding:12px 16px;color:var(--accent);font-weight:700;">AI Tutor</th>
            <th style="text-align:center;padding:12px 16px;color:var(--gold);font-weight:700;">Human Tutor</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $comparisons = [
            ['label'=>'Availability','ai'=>'24/7 instant','human'=>'Scheduled sessions'],
            ['label'=>'Response time','ai'=>'Instant','human'=>'Live — no wait'],
            ['label'=>'Personalization','ai'=>'AI-adapted','human'=>'Fully custom'],
            ['label'=>'Session recordings','ai'=>'Chat history','human'=>'Full video recordings'],
            ['label'=>'Study plan','ai'=>'AI-generated','human'=>'Expert-built, custom'],
            ['label'=>'Exam expertise','ai'=>'All exams','human'=>'Specialist for your exam'],
            ['label'=>'Price','ai'=>'$49/mo','human'=>'$79/session'],
          ];
          foreach ($comparisons as $c): ?>
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:14px 16px;color:var(--white-50);font-weight:600;"><?= h($c['label']) ?></td>
            <td style="padding:14px 16px;text-align:center;color:var(--white-70);"><?= h($c['ai']) ?></td>
            <td style="padding:14px 16px;text-align:center;color:var(--white-70);"><?= h($c['human']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════
       STEP 2 — SELECT SUBJECT
  ══════════════════════════════════════════════════════ -->
  <?php elseif ($step === 'select-subject'): ?>
  <?php $type_label = $type === 'ai' ? 'AI Tutor' : 'Human Tutor'; ?>
  <div style="text-align:center;padding:40px 0 20px;">
    <p style="font-size:.82rem;color:var(--white-50);margin-bottom:8px;">
      <?= $type === 'ai' ? '<i class="fa-solid fa-robot" style="color:var(--accent);"></i>' : '<i class="fa-solid fa-user-graduate" style="color:var(--gold);"></i>' ?>
      <?= h($type_label) ?> &nbsp;·&nbsp; <?= $type === 'ai' ? '$49/month' : '$79/session' ?>
    </p>
    <h2 style="font-size:1.4rem;">What subject do you need help with?</h2>
    <p style="color:var(--white-50);font-size:.88rem;margin-top:8px;">Select the exam or subject area you want to focus on.</p>
  </div>

  <div class="subject-select-grid">
    <?php foreach ($subjects as $slug => $subj): ?>
    <a href="booking.php?step=<?= $type === 'human' ? 'select-schedule' : 'details' ?>&type=<?= urlencode($type) ?>&subject=<?= urlencode($slug) ?>"
       class="subject-option">
      <i class="fa-solid <?= h($subj['icon']) ?>"></i>
      <?= h($subj['label']) ?>
    </a>
    <?php endforeach; ?>
  </div>

  <div style="text-align:center;margin-bottom:60px;">
    <a href="booking.php?step=select-type" style="font-size:.78rem;color:var(--white-40);display:inline-flex;align-items:center;gap:6px;">
      <i class="fa-solid fa-arrow-left"></i> Back to tutor type selection
    </a>
  </div>

  <!-- ══════════════════════════════════════════════════════
       STEP 3 — SELECT SCHEDULE (Human tutor only)
  ══════════════════════════════════════════════════════ -->
  <?php elseif ($step === 'select-schedule'): ?>
  <?php $subject_label = isset($subjects[$subject]) ? $subjects[$subject]['label'] : ucfirst($subject); ?>
  <div style="max-width:720px;margin:40px auto 60px;">
    <div style="text-align:center;margin-bottom:40px;">
      <p style="font-size:.82rem;color:var(--white-50);margin-bottom:8px;">
        <i class="fa-solid fa-user-graduate" style="color:var(--gold);"></i> Human Tutor &nbsp;·&nbsp; <?= h($subject_label) ?>
      </p>
      <h2 style="font-size:1.4rem;">When works best for you?</h2>
      <p style="color:var(--white-50);font-size:.88rem;margin-top:8px;">Select your preferred days and session length. We'll match you with an available specialist.</p>
    </div>

    <form method="GET" action="booking.php">
      <input type="hidden" name="step" value="details">
      <input type="hidden" name="type" value="<?= h($type) ?>">
      <input type="hidden" name="subject" value="<?= h($subject) ?>">

      <div class="glass-card" style="margin-bottom:24px;">
        <h3 style="font-size:.9rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--white-50);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--border);">
          <i class="fa-solid fa-calendar" style="color:var(--accent);"></i> Preferred Days
        </h3>
        <div class="schedule-grid">
          <?php foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
          <button type="button" class="day-btn" data-day="<?= $d ?>"
            onclick="toggleDay(this,'<?= $d ?>')"><?= $d ?></button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="days" id="days-input" value="">
      </div>

      <div class="glass-card" style="margin-bottom:32px;">
        <h3 style="font-size:.9rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--white-50);margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid var(--border);">
          <i class="fa-solid fa-clock" style="color:var(--accent);"></i> Preferred Session Length
        </h3>
        <div class="hours-options">
          <?php foreach(['30 min','60 min','90 min','2 hours'] as $h_opt): ?>
          <button type="button" class="hour-btn" data-val="<?= $h_opt ?>"
            onclick="selectHour(this,'<?= $h_opt ?>')"><?= $h_opt ?></button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="hours" id="hours-input" value="60 min">
      </div>

      <div style="text-align:center;">
        <button type="submit" class="btn btn-primary btn-lg">
          Continue to Your Details <i class="fa-solid fa-arrow-right"></i>
        </button>
        <div style="margin-top:16px;">
          <a href="booking.php?step=select-subject&type=<?= urlencode($type) ?>" style="font-size:.78rem;color:var(--white-40);display:inline-flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to subject selection
          </a>
        </div>
      </div>
    </form>
  </div>

  <script>
  var selectedDays = [];
  function toggleDay(btn, day) {
    var idx = selectedDays.indexOf(day);
    if (idx > -1) { selectedDays.splice(idx,1); btn.classList.remove('selected'); }
    else { selectedDays.push(day); btn.classList.add('selected'); }
    document.getElementById('days-input').value = selectedDays.join(',');
  }
  function selectHour(btn, val) {
    document.querySelectorAll('.hour-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('hours-input').value = val;
  }
  // Default select 60 min
  document.addEventListener('DOMContentLoaded', function() {
    var def = document.querySelector('.hour-btn[data-val="60 min"]');
    if (def) def.classList.add('selected');
  });
  </script>

  <!-- ══════════════════════════════════════════════════════
       STEP 4 — BOOKING DETAILS (your info)
  ══════════════════════════════════════════════════════ -->
  <?php elseif ($step === 'details'): ?>
  <?php
  $subject_label = isset($subjects[$subject]) ? $subjects[$subject]['label'] : ucfirst($subject);
  $price_info    = $pricing[$type] ?? $pricing['human'];
  $type_label    = $type === 'ai' ? 'AI Tutor' : 'Human Tutor';
  ?>
  <div class="checkout-grid" style="padding-top:40px;">

    <!-- Form -->
    <div class="checkout-form-wrap">
      <div class="checkout-form-card">
        <h2>Your Booking Details</h2>
        <p>Tell us a bit about yourself and what you need. We'll confirm your booking and send you the PayPal payment link by email.</p>

        <?php if (!empty($errors)): ?>
        <div style="background:var(--rose-subtle);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;">
          <?php foreach ($errors as $e): ?>
          <p style="color:var(--rose);font-size:.83rem;margin-bottom:4px;"><i class="fa-solid fa-circle-exclamation"></i> <?= h($e) ?></p>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="booking.php?step=details&type=<?= urlencode($type) ?>&subject=<?= urlencode($subject) ?>" novalidate>
          <input type="hidden" name="booking_type"    value="<?= h($type) ?>">
          <input type="hidden" name="booking_subject" value="<?= h($subject) ?>">
          <input type="hidden" name="booking_days"    value="<?= h($days) ?>">
          <input type="hidden" name="booking_hours"   value="<?= h($hours) ?>">

          <div class="form-group">
            <label class="form-label" for="student_name">Full Name <span style="color:var(--rose);">*</span></label>
            <input class="form-input" type="text" id="student_name" name="student_name"
              placeholder="e.g. Jane Smith"
              value="<?= h($_POST['student_name'] ?? '') ?>"
              required autocomplete="name">
          </div>

          <div class="form-group">
            <label class="form-label" for="student_email">Email Address <span style="color:var(--rose);">*</span></label>
            <input class="form-input" type="email" id="student_email" name="student_email"
              placeholder="you@example.com"
              value="<?= h($_POST['student_email'] ?? '') ?>"
              required autocomplete="email">
            <p class="form-hint">Your booking confirmation and PayPal payment link will be sent here.</p>
          </div>

          <div class="form-group">
            <label class="form-label" for="student_note">Additional Notes (optional)</label>
            <textarea class="form-input" id="student_note" name="student_note"
              rows="4"
              placeholder="Tell us your exam date, biggest challenges, specific topics you need help with, or any scheduling preferences..."
              style="resize:vertical;min-height:100px;"><?= h($_POST['student_note'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn <?= $type === 'ai' ? 'btn-primary' : 'btn-gold' ?> btn-lg" style="width:100%;justify-content:center;">
            <?php if ($type === 'ai'): ?>
            <i class="fa-solid fa-robot"></i> Confirm AI Tutor Booking
            <?php else: ?>
            <i class="fa-solid fa-user-graduate"></i> Confirm Session Booking
            <?php endif; ?>
            <i class="fa-solid fa-arrow-right"></i>
          </button>

          <div class="checkout-trust-row" style="margin-top:20px;">
            <div class="checkout-trust-item"><i class="fa-solid fa-envelope"></i> Confirmation by email</div>
            <div class="checkout-trust-item"><i class="fa-brands fa-paypal" style="color:#003087;"></i> PayPal payment</div>
            <div class="checkout-trust-item"><i class="fa-solid fa-shield-halved"></i> Secure</div>
          </div>
        </form>
      </div>

      <div style="margin-top:20px;">
        <a href="booking.php?step=<?= $type === 'human' ? 'select-schedule' : 'select-subject' ?>&type=<?= urlencode($type) ?>&subject=<?= urlencode($subject) ?>"
           style="font-size:.78rem;color:var(--white-40);display:flex;align-items:center;gap:6px;">
          <i class="fa-solid fa-arrow-left"></i> Go back
        </a>
      </div>
    </div><!-- /checkout-form-wrap -->

    <!-- Summary sidebar -->
    <div class="checkout-summary-wrap">
      <div class="checkout-summary">
        <div class="checkout-summary-head">Booking Summary</div>
        <div class="checkout-summary-body">
          <div class="checkout-summary-item">
            <div>
              <div class="checkout-summary-item-name"><?= h($type_label) ?></div>
              <div class="checkout-summary-item-sub">
                <?= $type === 'ai' ? 'Unlimited AI access' : 'Live 1-on-1 expert session' ?>
              </div>
            </div>
            <div class="checkout-summary-item-price"><?= h($price_info['price_label']) ?></div>
          </div>
          <div class="checkout-summary-item">
            <div>
              <div class="checkout-summary-item-name">Subject</div>
              <div class="checkout-summary-item-sub"><?= h($subject_label) ?></div>
            </div>
          </div>
          <?php if ($type === 'human' && $days): ?>
          <div class="checkout-summary-item">
            <div>
              <div class="checkout-summary-item-name">Preferred Days</div>
              <div class="checkout-summary-item-sub"><?= h(str_replace(',', ', ', $days)) ?></div>
            </div>
          </div>
          <?php endif; ?>
          <?php if ($type === 'human' && $hours): ?>
          <div class="checkout-summary-item">
            <div>
              <div class="checkout-summary-item-name">Session Length</div>
              <div class="checkout-summary-item-sub"><?= h($hours) ?></div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <div class="checkout-summary-total">
          <div class="checkout-summary-total-label"><?= $type === 'ai' ? 'Monthly Rate' : 'Per Session' ?></div>
          <div class="checkout-summary-total-price"><?= h($price_info['price_label']) ?></div>
        </div>
      </div>

      <div class="glass-card" style="margin-top:16px;padding:20px 24px;">
        <p style="font-size:.78rem;color:var(--white-50);line-height:1.7;margin:0;">
          <i class="fa-solid fa-envelope-circle-check" style="color:var(--accent);margin-right:6px;"></i>
          After you submit, we'll email you a booking confirmation and a PayPal payment link to complete your <?= h($price_info['price_label']) ?> <?= $type === 'ai' ? 'monthly subscription' : 'session fee' ?>.
        </p>
      </div>
    </div>

  </div><!-- /checkout-grid -->

  <!-- ══════════════════════════════════════════════════════
       STEP 5 — CONFIRMATION
  ══════════════════════════════════════════════════════ -->
  <?php else: ?>
  <?php
  $b_name    = h($_SESSION['booking_name']    ?? 'Student');
  $b_email   = h($_SESSION['booking_email']   ?? '');
  $b_type    = $_SESSION['booking_type']      ?? 'human';
  $b_subject = h($_SESSION['booking_subject'] ?? '');
  $b_days    = h($_SESSION['booking_days']    ?? '');
  $b_hours   = h($_SESSION['booking_hours']   ?? '');
  $b_price   = h($_SESSION['booking_price']   ?? '$79');
  $b_per     = h($_SESSION['booking_per']     ?? '/session');
  $b_note    = h($_SESSION['booking_note']    ?? '');
  $is_ai     = ($b_type === 'ai');
  ?>

  <div class="success-page-center" style="padding-top:60px;padding-bottom:80px;">

    <div class="success-icon-wrap">
      <i class="fa-solid fa-check success-icon"></i>
    </div>

    <h1>You're All Set, <?= $b_name ?>!</h1>
    <p>
      Your <?= $is_ai ? 'AI Tutor' : 'tutoring session' ?> booking for
      <strong style="color:var(--white-90);"><?= $b_subject ?></strong> has been received.
      A confirmation email <?= $is_ai ? 'and account setup instructions have' : 'with your PayPal payment link has' ?>
      been sent to <strong style="color:var(--accent);"><?= $b_email ?></strong>.
    </p>

    <!-- Booking details card -->
    <div class="booking-confirm-card" style="max-width:620px;margin:0 auto 40px;text-align:left;">
      <div class="booking-confirm-row">
        <div class="booking-confirm-label">Tutor Type</div>
        <div class="booking-confirm-value"><?= $is_ai ? 'AI Tutor (24/7)' : 'Human Tutor (Live)' ?></div>
      </div>
      <div class="booking-confirm-row">
        <div class="booking-confirm-label">Subject</div>
        <div class="booking-confirm-value"><?= $b_subject ?></div>
      </div>
      <?php if ($b_days): ?>
      <div class="booking-confirm-row">
        <div class="booking-confirm-label">Preferred Days</div>
        <div class="booking-confirm-value"><?= str_replace(',', ', ', $b_days) ?></div>
      </div>
      <?php endif; ?>
      <?php if ($b_hours): ?>
      <div class="booking-confirm-row">
        <div class="booking-confirm-label">Session Length</div>
        <div class="booking-confirm-value"><?= $b_hours ?></div>
      </div>
      <?php endif; ?>
      <div class="booking-confirm-row">
        <div class="booking-confirm-label"><?= $is_ai ? 'Monthly Rate' : 'Session Rate' ?></div>
        <div class="booking-confirm-value" style="color:var(--<?= $is_ai ? 'accent' : 'gold' ?>);"><?= $b_price ?><?= $b_per ?></div>
      </div>
      <div class="booking-confirm-row">
        <div class="booking-confirm-label">Confirmation Email</div>
        <div class="booking-confirm-value"><?= $b_email ?></div>
      </div>
    </div>

    <!-- Next steps -->
    <div class="success-steps-grid" style="max-width:820px;margin:0 auto 48px;">
      <?php if ($is_ai): ?>
      <div class="success-step-card">
        <div class="success-step-num">1</div>
        <h4>Check Your Email</h4>
        <p>Your AI Tutor account setup instructions and login link have been sent to your inbox.</p>
      </div>
      <div class="success-step-card">
        <div class="success-step-num">2</div>
        <h4>Complete Payment</h4>
        <p>Click the PayPal link in your email to activate your $49/month AI Tutor subscription.</p>
      </div>
      <div class="success-step-card">
        <div class="success-step-num">3</div>
        <h4>Start Immediately</h4>
        <p>Access your AI tutor 24/7 — ask anything about <?= $b_subject ?> right now, even at 2AM.</p>
      </div>
      <?php else: ?>
      <div class="success-step-card">
        <div class="success-step-num">1</div>
        <h4>Check Your Email</h4>
        <p>Your booking confirmation and PayPal payment link for <?= $b_price ?> are in your inbox now.</p>
      </div>
      <div class="success-step-card">
        <div class="success-step-num">2</div>
        <h4>Complete Payment</h4>
        <p>Click the PayPal link to pay your session fee. We'll match you with a specialist within 24 hours.</p>
      </div>
      <div class="success-step-card">
        <div class="success-step-num">3</div>
        <h4>Meet Your Tutor</h4>
        <p>Your specialist will email your Zoom/Meet link and confirm your session schedule based on your preferences.</p>
      </div>
      <?php endif; ?>
    </div>

    <div class="success-actions">
      <a href="../student/login.php" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-right-to-bracket"></i> Log In to Dashboard
      </a>
      <a href="../tutoring.php" class="btn btn-outline btn-lg">Back to Tutoring</a>
    </div>

    <div style="margin-top:48px;padding:24px;background:var(--surface-glass);border:1px solid var(--border);border-radius:var(--radius-lg);backdrop-filter:blur(16px);max-width:620px;margin-left:auto;margin-right:auto;">
      <p style="font-size:.8rem;color:var(--white-40);margin:0;">
        <i class="fa-solid fa-envelope" style="color:var(--accent);margin-right:8px;"></i>
        Questions about your booking? Email us at <a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--accent);"><?= SITE_EMAIL ?></a>
        or call <a href="tel:<?= SITE_PHONE ?>" style="color:var(--accent);"><?= SITE_PHONE ?></a>
      </p>
    </div>

  </div><!-- /success-page-center -->

  <?php endif; ?>

  </div><!-- /container -->
</div><!-- /order-page booking-page -->

<?php include '../includes/footer.php'; ?>
