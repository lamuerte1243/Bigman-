<?php
/**
 * Sheila The Writer — Course Order Flow
 * Full-page: Detail → Checkout → Success
 * No modals. No popups. Pure full-page steps.
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';
$in_subdir = true;

// ── Route params ───────────────────────────────────────────────
$step   = $_GET['step']   ?? 'detail';
$slug   = trim($_GET['course'] ?? 'teas');

// ── Course definitions ─────────────────────────────────────────
$courses = [
  'teas' => [
    'name'       => 'TEAS Complete Prep Course',
    'cat'        => 'Healthcare · Nursing Entrance',
    'badge'      => 'Most Popular',
    'badge_c'    => 'popular',
    'price'      => 149,
    'price_label'=> '$149',
    'hours'      => '40+',
    'modules'    => 6,
    'rating'     => '4.9',
    'img'        => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Comprehensive preparation covering all four TEAS subject areas: Reading, Math, Science, and English Language Usage. ATI TEAS 7th Edition content with full-length practice exams and detailed score analysis.',
    'includes'   => [
      '3 full-length practice exams with detailed answer rationales',
      '500+ subject-specific drill questions across all four sections',
      '6-week structured study schedule with daily targets',
      'Video explanations for every major concept',
      'Score analysis dashboard to track weak areas',
      'Live Q&A support with certified tutors',
    ],
    'curriculum' => [
      ['title'=>'Reading Comprehension','sub'=>'8 lessons · ~6 hrs','num'=>'01'],
      ['title'=>'Mathematics','sub'=>'10 lessons · ~9 hrs','num'=>'02'],
      ['title'=>'Science','sub'=>'12 lessons · ~10 hrs','num'=>'03'],
      ['title'=>'English & Language Usage','sub'=>'7 lessons · ~6 hrs','num'=>'04'],
      ['title'=>'Full Practice Exams (×3)','sub'=>'Timed · ATI-aligned format','num'=>'05'],
      ['title'=>'Score Analysis & Review','sub'=>'Personalized weak-area reports','num'=>'06'],
    ],
  ],
  'hesi' => [
    'name'       => 'HESI A² Complete Prep',
    'cat'        => 'Healthcare · Nursing Entrance',
    'badge'      => 'High Demand',
    'badge_c'    => 'hot',
    'price'      => 159,
    'price_label'=> '$159',
    'hours'      => '45+',
    'modules'    => 8,
    'rating'     => '4.8',
    'img'        => 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Full preparation for the HESI Admission Assessment Exam covering all 8 tested subjects: Math, Reading Comprehension, Vocabulary, Grammar, Biology, Chemistry, Anatomy & Physiology, and Physics.',
    'includes'   => [
      'All 8 HESI subject areas fully covered',
      '2 full-length practice exams + section-specific tests',
      'Vocabulary flashcard system (600+ terms)',
      'Grammar rule reference sheets',
      'Biology and Chemistry quick-review modules',
      'Anatomy & Physiology visual guides',
    ],
    'curriculum' => [
      ['title'=>'Mathematics','sub'=>'8 lessons · ~7 hrs','num'=>'01'],
      ['title'=>'Reading Comprehension','sub'=>'6 lessons · ~5 hrs','num'=>'02'],
      ['title'=>'Vocabulary & Grammar','sub'=>'7 lessons · ~6 hrs','num'=>'03'],
      ['title'=>'Biology & Chemistry','sub'=>'10 lessons · ~9 hrs','num'=>'04'],
      ['title'=>'Anatomy & Physiology','sub'=>'8 lessons · ~8 hrs','num'=>'05'],
      ['title'=>'Physics (if required)','sub'=>'5 lessons · ~4 hrs','num'=>'06'],
      ['title'=>'Full Practice Exams (×2)','sub'=>'Timed · HESI-aligned','num'=>'07'],
      ['title'=>'Score Review & Strategy','sub'=>'Personalized feedback','num'=>'08'],
    ],
  ],
  'nclex' => [
    'name'       => 'NCLEX-RN & PN Complete Prep',
    'cat'        => 'Nursing · Licensure Exam',
    'badge'      => 'NGN Updated',
    'badge_c'    => 'new',
    'price'      => 199,
    'price_label'=> '$199',
    'hours'      => '60+',
    'modules'    => 7,
    'rating'     => '4.9',
    'img'        => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Comprehensive Next-Generation NCLEX preparation covering the updated Clinical Judgment Measurement Model, all client need categories, and the new NGN item types including bowtie, cloze, and matrix questions.',
    'includes'   => [
      'Full NGN item type practice (bowtie, cloze, matrix)',
      '2,000+ practice questions with detailed rationales',
      'Clinical Judgment framework deep-dive',
      'All client need categories — Priority & Delegation',
      'Pharmacology high-yield review module',
      'Weekly live review sessions',
    ],
    'curriculum' => [
      ['title'=>'Clinical Judgment Framework','sub'=>'10 lessons · ~10 hrs','num'=>'01'],
      ['title'=>'Safe & Effective Care','sub'=>'12 lessons · ~12 hrs','num'=>'02'],
      ['title'=>'Health Promotion','sub'=>'6 lessons · ~6 hrs','num'=>'03'],
      ['title'=>'Psychosocial Integrity','sub'=>'7 lessons · ~7 hrs','num'=>'04'],
      ['title'=>'Physiological Integrity','sub'=>'14 lessons · ~14 hrs','num'=>'05'],
      ['title'=>'Pharmacology Review','sub'=>'8 lessons · ~8 hrs','num'=>'06'],
      ['title'=>'NGN Practice + Debrief','sub'=>'Full test simulations','num'=>'07'],
    ],
  ],
  'ged' => [
    'name'       => 'GED Complete Prep — All 4 Subjects',
    'cat'        => 'Academic · High School Equivalency',
    'badge'      => 'Adult Learners',
    'badge_c'    => 'popular',
    'price'      => 129,
    'price_label'=> '$129',
    'hours'      => '35+',
    'modules'    => 4,
    'rating'     => '4.8',
    'img'        => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Complete preparation for all four GED subjects: Mathematical Reasoning, Science, Social Studies, and Reasoning Through Language Arts. Designed for adult learners returning to education.',
    'includes'   => [
      'All 4 GED subject areas fully covered',
      '2 full-length GED practice exams',
      'Math formula sheet and calculator guide',
      'Extended response writing template',
      'Science and Social Studies passages',
      'Flexible self-paced schedule',
    ],
    'curriculum' => [
      ['title'=>'Mathematical Reasoning','sub'=>'10 lessons · ~9 hrs','num'=>'01'],
      ['title'=>'Science','sub'=>'8 lessons · ~7 hrs','num'=>'02'],
      ['title'=>'Social Studies','sub'=>'7 lessons · ~6 hrs','num'=>'03'],
      ['title'=>'Reasoning Through Language Arts','sub'=>'9 lessons · ~8 hrs','num'=>'04'],
    ],
  ],
  'gre' => [
    'name'       => 'GRE Complete Prep',
    'cat'        => 'Grad School · Graduate Admission',
    'badge'      => 'Grad School',
    'badge_c'    => 'popular',
    'price'      => 179,
    'price_label'=> '$179',
    'hours'      => '50+',
    'modules'    => 5,
    'rating'     => '4.7',
    'img'        => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Comprehensive GRE preparation covering Verbal Reasoning, Quantitative Reasoning, and Analytical Writing. Includes our vocabulary mastery system, essay templates, and 3 full-length practice tests.',
    'includes'   => [
      '3 full-length GRE practice tests (ETS-aligned)',
      'Vocabulary system with 500+ high-frequency words',
      'Quant strategy guides for all question types',
      'Analytical Writing essay templates',
      'Argument and Issue essay scoring rubric',
      'Score-improvement guarantee support',
    ],
    'curriculum' => [
      ['title'=>'Verbal Reasoning','sub'=>'10 lessons · ~9 hrs','num'=>'01'],
      ['title'=>'Vocabulary Mastery','sub'=>'8 lessons · ~7 hrs','num'=>'02'],
      ['title'=>'Quantitative Reasoning','sub'=>'12 lessons · ~11 hrs','num'=>'03'],
      ['title'=>'Analytical Writing','sub'=>'6 lessons · ~6 hrs','num'=>'04'],
      ['title'=>'Full Practice Tests (×3)','sub'=>'ETS-format timed tests','num'=>'05'],
    ],
  ],
  'accuplacer' => [
    'name'       => 'ACCUPLACER Complete Prep',
    'cat'        => 'Academic · College Placement',
    'badge'      => 'Quick Results',
    'badge_c'    => 'new',
    'price'      => 99,
    'price_label'=> '$99',
    'hours'      => '25+',
    'modules'    => 4,
    'rating'     => '4.8',
    'img'        => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Targeted preparation for all ACCUPLACER test versions. Place into college-level courses, skip remedial classes, and save thousands in tuition with our focused strategy-first approach.',
    'includes'   => [
      'All ACCUPLACER test versions covered',
      'Math: Arithmetic through Advanced Algebra',
      'Reading and Writing modules',
      '200+ practice questions per subject',
      'Adaptive study path based on your baseline',
      '7-day intensive option for fast placement',
    ],
    'curriculum' => [
      ['title'=>'Arithmetic & Quantitative Reasoning','sub'=>'6 lessons · ~5 hrs','num'=>'01'],
      ['title'=>'Algebra & Advanced Math','sub'=>'8 lessons · ~7 hrs','num'=>'02'],
      ['title'=>'Reading Comprehension','sub'=>'5 lessons · ~4 hrs','num'=>'03'],
      ['title'=>'Writing & WritePlacer','sub'=>'5 lessons · ~4 hrs','num'=>'04'],
    ],
  ],
  'straighterline' => [
    'name'       => 'StraighterLine Course Help',
    'cat'        => 'Online Platform · College Courses',
    'badge'      => 'Expert Help',
    'badge_c'    => 'popular',
    'price'      => 249,
    'price_label'=> '$249',
    'hours'      => 'Ongoing',
    'modules'    => 0,
    'rating'     => '4.9',
    'img'        => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Expert assistance with StraighterLine online courses. Our certified tutors provide personalized guidance, assignment help, and study support so you can pass your StraighterLine courses with confidence.',
    'includes'   => [
      'Dedicated tutor for your specific course',
      'Assignment review and feedback',
      'Concept explanations and worked examples',
      'Discussion board support',
      'Exam preparation sessions',
      'Progress check-ins throughout your course',
    ],
    'curriculum' => [
      ['title'=>'Initial Assessment & Plan','sub'=>'1 session · tailored to your course','num'=>'01'],
      ['title'=>'Weekly Tutoring Sessions','sub'=>'Live or async — your schedule','num'=>'02'],
      ['title'=>'Assignment Support','sub'=>'Review and guidance on all work','num'=>'03'],
      ['title'=>'Exam Prep Sessions','sub'=>'Before each proctored exam','num'=>'04'],
    ],
  ],
  'sophia' => [
    'name'       => 'Sophia Learning Course Help',
    'cat'        => 'Online Platform · College Courses',
    'badge'      => 'Expert Help',
    'badge_c'    => 'popular',
    'price'      => 229,
    'price_label'=> '$229',
    'hours'      => 'Ongoing',
    'modules'    => 0,
    'rating'     => '4.8',
    'img'        => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=900&h=400&fit=crop&auto=format&q=80',
    'desc'       => 'Expert support for Sophia Learning online courses. Work with a tutor who knows the Sophia platform, understands the unit structure, and will guide you through milestones efficiently.',
    'includes'   => [
      'Tutor familiar with Sophia course formats',
      'Milestone-by-milestone guidance',
      'Portfolio/challenge exam preparation',
      'Discussion prompt assistance',
      'Study resources for each unit topic',
      'Email and chat support throughout',
    ],
    'curriculum' => [
      ['title'=>'Course Overview & Milestone Plan','sub'=>'Customized to your course','num'=>'01'],
      ['title'=>'Unit-by-Unit Tutoring','sub'=>'Weekly or as-needed support','num'=>'02'],
      ['title'=>'Portfolio & Challenge Prep','sub'=>'Exam-type practice sessions','num'=>'03'],
      ['title'=>'Final Review','sub'=>'End-of-course preparation','num'=>'04'],
    ],
  ],
];

// Fallback to teas if slug not found
if (!isset($courses[$slug])) { $slug = 'teas'; }
$course = $courses[$slug];

// ── POST handler (checkout submission) ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'checkout') {
  $name  = trim($_POST['student_name'] ?? '');
  $email = trim($_POST['student_email'] ?? '');
  $errors = [];

  if (strlen($name) < 2)  $errors[] = 'Please enter your full name.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

  if (empty($errors)) {
    // Check if student already exists
    $is_new = true;
    try {
      require_once '../includes/db.php';
      $pdo = get_db();
      $stmt = $pdo->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      if ($stmt->fetchColumn()) { $is_new = false; }
    } catch (Exception $e) {
      // DB unavailable — proceed, flag as unknown
      $is_new = true;
    }

    $_SESSION['order_name']    = $name;
    $_SESSION['order_email']   = $email;
    $_SESSION['order_course']  = $course['name'];
    $_SESSION['order_slug']    = $slug;
    $_SESSION['order_price']   = $course['price_label'];
    $_SESSION['order_is_new']  = $is_new;

    header('Location: order.php?step=success&course=' . urlencode($slug));
    exit;
  }
}

// ── Step-level page metadata ──────────────────────────────────
if ($step === 'detail') {
  $page_title = $course['name'] . ' — ' . SITE_NAME;
} elseif ($step === 'checkout') {
  $page_title = 'Checkout: ' . $course['name'] . ' — ' . SITE_NAME;
} else {
  $page_title = 'Enrollment Confirmed — ' . SITE_NAME;
}

include '../includes/header.php';
?>

<?php if ($step === 'detail'): ?>
<!-- ════════════════════════════════════════════════════════════
     STEP 1 — COURSE DETAIL
  ════════════════════════════════════════════════════════════ -->
<div class="order-page">

  <!-- Hero breadcrumb bar -->
  <div class="order-page-hero">
    <div class="container">
      <div class="breadcrumb" style="margin-bottom:20px;">
        <a href="../index.php">Home</a><span class="sep">›</span>
        <a href="../courses.php">Courses</a><span class="sep">›</span>
        <span><?= h($course['name']) ?></span>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <div class="course-detail-cat"><?= h($course['cat']) ?></div>
        <?php if ($course['badge']): ?>
        <span class="course-badge <?= h($course['badge_c']) ?>"><?= h($course['badge']) ?></span>
        <?php endif; ?>
      </div>
      <h1 class="course-detail-head" style="margin-top:12px;font-size:clamp(1.8rem,4vw,3rem);"><?= h($course['name']) ?></h1>
    </div>
  </div>

  <!-- Main content grid -->
  <div class="container">
    <div class="order-flow-grid">

      <!-- Main column -->
      <div class="order-flow-main">

        <!-- Hero image -->
        <div class="course-detail-img">
          <img src="<?= h($course['img']) ?>" alt="<?= h($course['name']) ?>">
        </div>

        <!-- Meta bar -->
        <div class="course-detail-meta">
          <?php if ($course['hours'] !== 'Ongoing'): ?>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-clock"></i> <?= h($course['hours']) ?> hours of content
          </div>
          <?php else: ?>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-calendar-check"></i> Ongoing support — your schedule
          </div>
          <?php endif; ?>
          <?php if ($course['modules'] > 0): ?>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-layer-group"></i> <?= (int)$course['modules'] ?> modules
          </div>
          <?php endif; ?>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-star" style="color:var(--gold);"></i> <?= h($course['rating']) ?> rating
          </div>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-shield-halved"></i> Expert-written content
          </div>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-bolt"></i> Instant access upon enrollment
          </div>
        </div>

        <!-- Description -->
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-circle-info"></i> About This Course</h3>
          <p style="font-size:.93rem;color:var(--white-60);line-height:1.85;"><?= h($course['desc']) ?></p>
        </div>

        <!-- What's included -->
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-box-open"></i> What's Included</h3>
          <ul class="course-includes-list">
            <?php foreach ($course['includes'] as $inc): ?>
            <li><i class="fa-solid fa-check-circle"></i><?= h($inc) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Curriculum -->
        <?php if (!empty($course['curriculum'])): ?>
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-list-check"></i> Course Curriculum</h3>
          <div class="curriculum-list">
            <?php foreach ($course['curriculum'] as $mod): ?>
            <div class="curriculum-item glass-card" style="padding:0;border-radius:var(--radius);">
              <div class="curriculum-item-head">
                <div class="curriculum-num"><?= h($mod['num']) ?></div>
                <div class="curriculum-body">
                  <div class="curriculum-title"><?= h($mod['title']) ?></div>
                  <div class="curriculum-sub"><?= h($mod['sub']) ?></div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color:var(--white-30);font-size:.65rem;flex-shrink:0;"></i>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div><!-- /order-flow-main -->

      <!-- Sidebar -->
      <div class="order-flow-sidebar">
        <div class="pricing-card">
          <div class="pricing-price"><?= h($course['price_label']) ?></div>
          <div class="pricing-per">one-time · full course access</div>

          <a href="order.php?step=checkout&course=<?= urlencode($slug) ?>" class="pricing-btn">
            Enroll Now <i class="fa-solid fa-arrow-right"></i>
          </a>

          <ul class="pricing-features">
            <?php foreach ($course['includes'] as $inc): ?>
            <li><i class="fa-solid fa-check"></i><?= h($inc) ?></li>
            <?php endforeach; ?>
          </ul>

          <div class="pricing-guarantee">
            <i class="fa-solid fa-shield-halved"></i>
            Satisfaction guarantee · Expert support included
          </div>

          <div class="pricing-paypal-note">
            <i class="fa-brands fa-paypal" style="color:#003087;"></i>
            Secure payment via PayPal
          </div>
        </div>

        <!-- Trust badges -->
        <div class="glass-card" style="margin-top:16px;padding:20px 24px;">
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-lock" style="color:var(--green);width:16px;text-align:center;"></i>
              Secure encrypted checkout
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-envelope-circle-check" style="color:var(--accent);width:16px;text-align:center;"></i>
              Credentials sent within 24 hours
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-headset" style="color:var(--gold);width:16px;text-align:center;"></i>
              Expert support included
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-star" style="color:var(--gold);width:16px;text-align:center;"></i>
              <?= h($course['rating']) ?> avg. student rating
            </div>
          </div>
        </div>
      </div><!-- /order-flow-sidebar -->

    </div><!-- /order-flow-grid -->
  </div><!-- /container -->

</div><!-- /order-page -->

<?php elseif ($step === 'checkout'): ?>
<!-- ════════════════════════════════════════════════════════════
     STEP 2 — CHECKOUT
  ════════════════════════════════════════════════════════════ -->
<div class="order-page">

  <div class="order-page-hero">
    <div class="container">
      <div class="breadcrumb" style="margin-bottom:20px;">
        <a href="../index.php">Home</a><span class="sep">›</span>
        <a href="../courses.php">Courses</a><span class="sep">›</span>
        <a href="order.php?step=detail&course=<?= urlencode($slug) ?>"><?= h($course['name']) ?></a><span class="sep">›</span>
        <span>Checkout</span>
      </div>
      <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);margin-bottom:0;">Complete Your Enrollment</h1>
    </div>
  </div>

  <div class="container">

    <!-- Step indicator -->
    <div class="checkout-steps" style="margin-top:40px;">
      <div class="checkout-step done">
        <div class="checkout-step-num"><i class="fa-solid fa-check" style="font-size:.55rem;"></i></div>
        <span class="checkout-step-label">Course Details</span>
      </div>
      <div class="checkout-step active">
        <div class="checkout-step-num">2</div>
        <span class="checkout-step-label">Checkout</span>
      </div>
      <div class="checkout-step">
        <div class="checkout-step-num">3</div>
        <span class="checkout-step-label">Confirmation</span>
      </div>
    </div>

    <!-- Checkout grid -->
    <div class="checkout-grid">

      <!-- Left: form -->
      <div class="checkout-form-wrap">
        <div class="checkout-form-card">
          <h2>Your Information</h2>
          <p>We'll create your student account and send your course access credentials to the email you provide below.</p>

          <?php if (!empty($errors)): ?>
          <div style="background:var(--rose-subtle);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;">
            <?php foreach ($errors as $e): ?>
            <p style="color:var(--rose);font-size:.83rem;margin-bottom:4px;"><i class="fa-solid fa-circle-exclamation"></i> <?= h($e) ?></p>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="order.php?step=checkout&course=<?= urlencode($slug) ?>" novalidate>

            <div class="form-group">
              <label class="form-label" for="student_name">Full Name <span style="color:var(--rose);">*</span></label>
              <input
                class="form-input"
                type="text"
                id="student_name"
                name="student_name"
                placeholder="e.g. Jane Smith"
                value="<?= h($_POST['student_name'] ?? '') ?>"
                required
                autocomplete="name"
              >
              <p class="form-hint">This will appear on your student account and course certificate.</p>
            </div>

            <div class="form-group">
              <label class="form-label" for="student_email">Email Address <span style="color:var(--rose);">*</span></label>
              <input
                class="form-input"
                type="email"
                id="student_email"
                name="student_email"
                placeholder="you@example.com"
                value="<?= h($_POST['student_email'] ?? '') ?>"
                required
                autocomplete="email"
              >
              <p class="form-hint">Your login credentials and course access link will be sent to this email.</p>
            </div>

            <!-- PayPal section -->
            <div class="paypal-section">
              <p class="paypal-section-label">Complete Payment via PayPal</p>

              <button type="submit" class="btn-paypal">
                <i class="fa-brands fa-paypal" style="font-size:1.3rem;"></i>
                <span>Pay with PayPal &mdash; <?= h($course['price_label']) ?></span>
              </button>

              <div class="checkout-trust-row">
                <div class="checkout-trust-item"><i class="fa-solid fa-lock"></i> Secure</div>
                <div class="checkout-trust-item"><i class="fa-solid fa-shield-halved"></i> Buyer Protection</div>
                <div class="checkout-trust-item"><i class="fa-solid fa-bolt"></i> Instant Processing</div>
              </div>
            </div>

          </form>
        </div>

        <!-- Back link -->
        <div style="margin-top:20px;">
          <a href="order.php?step=detail&course=<?= urlencode($slug) ?>" style="font-size:.78rem;color:var(--white-40);display:flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to course details
          </a>
        </div>
      </div><!-- /checkout-form-wrap -->

      <!-- Right: order summary -->
      <div class="checkout-summary-wrap">
        <div class="checkout-summary">
          <div class="checkout-summary-head">Order Summary</div>
          <div class="checkout-summary-body">
            <div class="checkout-summary-item">
              <div>
                <div class="checkout-summary-item-name"><?= h($course['name']) ?></div>
                <div class="checkout-summary-item-sub"><?= h($course['cat']) ?></div>
              </div>
              <div class="checkout-summary-item-price"><?= h($course['price_label']) ?></div>
            </div>
            <div class="checkout-summary-item">
              <div>
                <div class="checkout-summary-item-name">Expert Support</div>
                <div class="checkout-summary-item-sub">Included with enrollment</div>
              </div>
              <div class="checkout-summary-item-price" style="color:var(--green);">Free</div>
            </div>
          </div>
          <div class="checkout-summary-total">
            <div class="checkout-summary-total-label">Total Due</div>
            <div class="checkout-summary-total-price"><?= h($course['price_label']) ?></div>
          </div>
        </div>

        <div class="glass-card" style="margin-top:16px;padding:20px 24px;">
          <p style="font-size:.78rem;color:var(--white-50);line-height:1.7;margin:0;">
            <i class="fa-solid fa-envelope-circle-check" style="color:var(--accent);margin-right:6px;"></i>
            After payment, your course access and login credentials will be emailed to you within <strong style="color:var(--white-80);">24 hours</strong>. Check your spam folder if you don't see it.
          </p>
        </div>
      </div><!-- /checkout-summary-wrap -->

    </div><!-- /checkout-grid -->
  </div><!-- /container -->

</div><!-- /order-page -->

<?php else: ?>
<!-- ════════════════════════════════════════════════════════════
     STEP 3 — SUCCESS
  ════════════════════════════════════════════════════════════ -->
<?php
$s_name    = h($_SESSION['order_name']   ?? 'Student');
$s_email   = h($_SESSION['order_email']  ?? '');
$s_course  = h($_SESSION['order_course'] ?? $course['name']);
$s_price   = h($_SESSION['order_price']  ?? $course['price_label']);
$s_is_new  = $_SESSION['order_is_new']   ?? true;
?>
<div class="success-page">
  <div class="container">
    <div class="success-page-center">

      <!-- Success icon -->
      <div class="success-icon-wrap">
        <i class="fa-solid fa-check success-icon"></i>
      </div>

      <?php if ($s_is_new): ?>
      <!-- NEW STUDENT message -->
      <h1>You're Enrolled, <?= $s_name ?>!</h1>
      <p>
        Your enrollment in <strong style="color:var(--white-90);"><?= $s_course ?></strong> is confirmed.
        We're setting up your student account right now. Your login credentials and course access link will be sent to
        <strong style="color:var(--accent);"><?= $s_email ?></strong> within 24 hours.
      </p>

      <div class="success-steps-grid">
        <div class="success-step-card">
          <div class="success-step-num">1</div>
          <h4>Check Your Email</h4>
          <p>Look for an email from Sheila The Writer with your student account username and temporary password.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">2</div>
          <h4>Log In & Set Password</h4>
          <p>Use your temporary credentials to log in and set a secure password of your choice in your dashboard.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">3</div>
          <h4>Start Learning</h4>
          <p>Your course is fully unlocked. Begin with Module 1 and follow the recommended study schedule.</p>
        </div>
      </div>

      <?php else: ?>
      <!-- EXISTING STUDENT message -->
      <h1>Welcome Back, <?= $s_name ?>!</h1>
      <p>
        Your enrollment in <strong style="color:var(--white-90);"><?= $s_course ?></strong> has been added to your account.
        Head to your student dashboard to access your new course — it's already waiting for you.
      </p>

      <div class="success-steps-grid">
        <div class="success-step-card">
          <div class="success-step-num">1</div>
          <h4>Go to Your Dashboard</h4>
          <p>Log in to your existing student account to find your newly added course ready to start immediately.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">2</div>
          <h4>Find Your New Course</h4>
          <p>Your <?= $s_course ?> course is fully unlocked in the My Courses section of your dashboard.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">3</div>
          <h4>Pick Up Where You Excel</h4>
          <p>Follow the recommended study plan and reach out to our expert support team any time you need help.</p>
        </div>
      </div>

      <?php endif; ?>

      <!-- Action buttons -->
      <div class="success-actions">
        <?php if ($s_is_new): ?>
        <a href="../student/login.php" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-right-to-bracket"></i> Log In to Dashboard
        </a>
        <a href="../courses.php" class="btn btn-outline btn-lg">Browse More Courses</a>
        <?php else: ?>
        <a href="../student/dashboard.php" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-gauge"></i> Go to My Dashboard
        </a>
        <a href="../courses.php" class="btn btn-outline btn-lg">Browse More Courses</a>
        <?php endif; ?>
      </div>

      <!-- Receipt note -->
      <div style="margin-top:48px;padding:24px;background:var(--surface-glass);border:1px solid var(--border);border-radius:var(--radius-lg);backdrop-filter:blur(16px);">
        <p style="font-size:.8rem;color:var(--white-40);margin:0;">
          <i class="fa-solid fa-receipt" style="color:var(--accent);margin-right:8px;"></i>
          A payment receipt has been sent by PayPal to <strong style="color:var(--white-70);"><?= $s_email ?></strong>.
          If you have any questions, contact us at <a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--accent);"><?= SITE_EMAIL ?></a>
        </p>
      </div>

    </div><!-- /success-page-center -->
  </div><!-- /container -->
</div><!-- /success-page -->

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
