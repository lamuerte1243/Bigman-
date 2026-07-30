<?php
/**
 * Sheila The Writer — Study Materials Order Flow
 * Full-page: Detail → Checkout → Success
 * No modals. No popups. Pure full-page steps.
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';
$in_subdir = true;

// ── Route params ───────────────────────────────────────────────
$step = $_GET['step']     ?? 'detail';
$slug = trim($_GET['material'] ?? 'teas-guide');

// ── Material definitions ───────────────────────────────────────
$materials = [
  'teas-guide' => [
    'title'      => 'TEAS 7 Complete Study Guide',
    'type'       => 'Study Guide',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => 'Bestseller',
    'badge_c'    => 'popular',
    'price'      => 47,
    'price_label'=> '$47',
    'format'     => 'PDF · 480 pages',
    'icon'       => 'fa-book-open',
    'color'      => 'var(--accent)',
    'desc'       => 'The most comprehensive TEAS 7 prep guide available. Covers all four sections — Reading, Mathematics, Science, and English & Language Usage — with in-depth content review, proven test strategies, and full answer rationales.',
    'includes'   => [
      'Complete coverage of all 4 TEAS 7 subject areas',
      '480 pages of expert-written, exam-aligned content',
      'Test strategies for every question type',
      'Practice questions at the end of each chapter',
      'Quick-reference summary sheets per section',
      'Instant PDF download — access anywhere',
    ],
    'previews'   => [
      'Chapter 1: Reading Comprehension Strategies',
      'Chapter 2: Mathematics — Numbers & Algebra',
      'Chapter 3: Science — Human Body Systems',
      'Chapter 4: English Language Usage Rules',
      'Appendix: Formula Sheet & Quick References',
    ],
  ],
  'teas-practice' => [
    'title'      => 'TEAS 7 Practice Tests (3-Pack)',
    'type'       => 'Practice Tests',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => 'Most Popular',
    'badge_c'    => 'hot',
    'price'      => 39,
    'price_label'=> '$39',
    'format'     => 'PDF · 3 Full Exams',
    'icon'       => 'fa-list-check',
    'color'      => 'var(--accent)',
    'desc'       => 'Three full-length TEAS 7 practice exams with complete answer rationales for every question. ATI-aligned format with the same question types, difficulty level, and section structure as the real exam.',
    'includes'   => [
      '3 complete TEAS 7 practice exams',
      'Detailed rationales for every answer choice',
      'ATI-aligned format and difficulty',
      'Answer tracking sheet per exam',
      'Score analysis guide to identify weak areas',
      'Instant PDF download',
    ],
    'previews'   => [
      'Practice Exam 1 — Full 170-question test',
      'Practice Exam 2 — Full 170-question test',
      'Practice Exam 3 — Full 170-question test',
      'Answer Rationale Guide (all 3 exams)',
      'Score Analysis Worksheet',
    ],
  ],
  'teas-bundle' => [
    'title'      => 'TEAS Complete Bundle',
    'type'       => 'Full Bundle',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => 'Best Value',
    'badge_c'    => 'new',
    'price'      => 97,
    'price_label'=> '$97',
    'format'     => 'Digital · All Formats',
    'icon'       => 'fa-layer-group',
    'color'      => 'var(--gold)',
    'desc'       => 'Everything you need to pass the TEAS 7 in one comprehensive package. Study guide + 3 practice tests + flashcard deck + quick-reference cheat sheets — the complete TEAS prep system at a bundled price.',
    'includes'   => [
      'TEAS 7 Complete Study Guide (480 pages)',
      '3 Full-Length Practice Exams with rationales',
      '800+ TEAS Flashcard Deck (Anki + PDF)',
      'Quick-reference cheat sheets for all sections',
      'Study schedule template (4-week & 8-week)',
      'Instant access to all formats',
    ],
    'previews'   => [
      'Study Guide — 480-page PDF',
      'Practice Exams ×3 — ATI-aligned',
      'Flashcard Deck — 800+ cards',
      'Cheat Sheets — Science, Math, Grammar',
      'Study Schedules — 4-week and 8-week plans',
    ],
  ],
  'hesi-guide' => [
    'title'      => 'HESI A² Complete Study Guide',
    'type'       => 'Study Guide',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => null,
    'badge_c'    => null,
    'price'      => 47,
    'price_label'=> '$47',
    'format'     => 'PDF · 520 pages',
    'icon'       => 'fa-book-open',
    'color'      => 'var(--accent)',
    'desc'       => 'Full content review for all 8 HESI A² sections: Math, Reading Comprehension, Vocabulary, Grammar, Biology, Chemistry, Anatomy & Physiology, and Physics. Written by certified HESI specialists.',
    'includes'   => [
      'All 8 HESI A² subject areas covered',
      '520 pages of expert-written content',
      'Vocabulary list with 400+ tested terms',
      'Grammar rules and usage guide',
      'Biology and Chemistry quick-review diagrams',
      'Instant PDF download',
    ],
    'previews'   => [
      'Chapter 1: Mathematics & Conversions',
      'Chapter 2: Reading Comprehension',
      'Chapter 3: Vocabulary & Grammar',
      'Chapter 4: Biology & Chemistry',
      'Chapter 5: Anatomy & Physiology',
    ],
  ],
  'hesi-flashcards' => [
    'title'      => 'HESI A² Flashcard Deck',
    'type'       => 'Flashcards',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => null,
    'badge_c'    => null,
    'price'      => 29,
    'price_label'=> '$29',
    'format'     => 'PDF · 1,800 Cards',
    'icon'       => 'fa-clone',
    'color'      => 'var(--purple)',
    'desc'       => '1,800+ flashcards covering all HESI A² subjects, optimized for spaced repetition study. Compatible with Anki, Quizlet, or use the printable PDF. Perfect for vocabulary, anatomy terms, and science facts.',
    'includes'   => [
      '1,800+ expert-curated flashcards',
      'Anki-compatible deck (.apkg file)',
      'Printable PDF version included',
      'Organized by subject area',
      'Includes diagrams and memory aids',
      'Instant download access',
    ],
    'previews'   => [
      'Vocabulary Set — 400+ tested terms',
      'Anatomy & Physiology — 500+ cards',
      'Biology & Chemistry — 350+ cards',
      'Math Formulas — 200+ cards',
      'Grammar Rules — 150+ cards',
    ],
  ],
  'hesi-bundle' => [
    'title'      => 'HESI Complete Bundle',
    'type'       => 'Full Bundle',
    'cat'        => 'Nursing · Healthcare',
    'badge'      => 'Best Value',
    'badge_c'    => 'new',
    'price'      => 89,
    'price_label'=> '$89',
    'format'     => 'Digital · All Formats',
    'icon'       => 'fa-layer-group',
    'color'      => 'var(--gold)',
    'desc'       => 'Study guide + 2 practice tests + full flashcard deck. The complete HESI A² prep system at a discounted bundle price. Everything a nursing school applicant needs in one download.',
    'includes'   => [
      'HESI A² Complete Study Guide (520 pages)',
      '2 Full-Length HESI Practice Exams',
      '1,800+ HESI Flashcard Deck',
      'All 8 subject areas covered',
      'Printable quick-reference sheets',
      'Instant access — all formats',
    ],
    'previews'   => [
      'Study Guide PDF — 520 pages',
      'Practice Exam 1 — HESI-aligned',
      'Practice Exam 2 — HESI-aligned',
      'Flashcard Deck — 1,800+ cards',
      'Quick Reference Sheets',
    ],
  ],
  'nclex-guide' => [
    'title'      => 'NCLEX-RN & PN Study Guide (NGN)',
    'type'       => 'Study Guide',
    'cat'        => 'Nursing · Licensure',
    'badge'      => 'Updated 2025',
    'badge_c'    => 'new',
    'price'      => 57,
    'price_label'=> '$57',
    'format'     => 'PDF · 640 pages',
    'icon'       => 'fa-book-medical',
    'color'      => 'var(--accent)',
    'desc'       => 'Comprehensive Next-Generation NCLEX guide covering the updated Clinical Judgment Measurement Model, all client need categories, and the new NGN item types including bowtie, cloze, and matrix questions.',
    'includes'   => [
      'Full NGN item type coverage',
      'Clinical Judgment framework deep-dive',
      'All client need categories explained',
      'Priority, delegation, and SATA strategies',
      'High-yield pharmacology module',
      'Instant PDF download',
    ],
    'previews'   => [
      'Unit 1: Clinical Judgment Framework',
      'Unit 2: Safe & Effective Care',
      'Unit 3: Physiological Integrity',
      'Unit 4: NGN New Item Types',
      'Unit 5: Pharmacology High-Yield',
    ],
  ],
  'nclex-questions' => [
    'title'      => 'NCLEX Practice Questions (2,000+)',
    'type'       => 'Practice Tests',
    'cat'        => 'Nursing · Licensure',
    'badge'      => 'High Demand',
    'badge_c'    => 'hot',
    'price'      => 49,
    'price_label'=> '$49',
    'format'     => 'PDF · 2,000+ Questions',
    'icon'       => 'fa-list-check',
    'color'      => 'var(--accent)',
    'desc'       => '2,000+ practice questions in the NGN format with full rationales for every answer. Covers all NCLEX client need categories with emphasis on Clinical Judgment and the new bowtie/matrix question types.',
    'includes'   => [
      '2,000+ NGN-format practice questions',
      'Detailed rationale for every answer choice',
      'Organized by client need category',
      'NGN item types included',
      'Difficulty progression from basic to advanced',
      'Instant PDF download',
    ],
    'previews'   => [
      'Section 1: Safe & Effective Care (500 Qs)',
      'Section 2: Health Promotion (300 Qs)',
      'Section 3: Psychosocial Integrity (300 Qs)',
      'Section 4: Physiological Integrity (700 Qs)',
      'Section 5: NGN Specialty Items (200 Qs)',
    ],
  ],
  'ged-guide' => [
    'title'      => 'GED Complete Study Guide — All 4 Subjects',
    'type'       => 'Study Guide',
    'cat'        => 'Academic · GED',
    'badge'      => null,
    'badge_c'    => null,
    'price'      => 45,
    'price_label'=> '$45',
    'format'     => 'PDF · 580 pages',
    'icon'       => 'fa-book-open',
    'color'      => 'var(--accent)',
    'desc'       => 'Full content review for all four GED subjects: Mathematical Reasoning, Science, Social Studies, and Reasoning Through Language Arts. Designed specifically for adult learners returning to education.',
    'includes'   => [
      'All 4 GED subject areas fully covered',
      'Mathematics formula reference sheet',
      'Extended Response essay templates',
      'Science and Social Studies passage strategies',
      'Practice questions per chapter',
      'Instant PDF download',
    ],
    'previews'   => [
      'Part 1: Mathematical Reasoning',
      'Part 2: Science',
      'Part 3: Social Studies',
      'Part 4: Reasoning Through Language Arts',
      'Appendix: Formulas, Templates & References',
    ],
  ],
  'gre-package' => [
    'title'      => 'GRE Complete Prep Package',
    'type'       => 'Full Bundle',
    'cat'        => 'Grad School · GRE',
    'badge'      => 'Grad School',
    'badge_c'    => 'popular',
    'price'      => 79,
    'price_label'=> '$79',
    'format'     => 'Digital · All Formats',
    'icon'       => 'fa-layer-group',
    'color'      => 'var(--gold)',
    'desc'       => 'Verbal Reasoning, Quantitative Reasoning, and Analytical Writing prep in one package. Includes vocabulary system, essay templates, math strategy guides, and 3 full practice tests aligned to the current GRE format.',
    'includes'   => [
      '3 full GRE practice tests (ETS-aligned)',
      'Vocabulary mastery system — 500+ words',
      'Quant strategy guide for all question types',
      'AW essay templates — Argument & Issue',
      'Scoring rubrics and sample essays',
      'Instant access — all formats',
    ],
    'previews'   => [
      'Verbal Reasoning Strategy Guide',
      'Vocabulary System — 500+ Words',
      'Quantitative Reasoning Guide',
      'Analytical Writing Templates',
      'Practice Tests ×3',
    ],
  ],
  'accuplacer-bundle' => [
    'title'      => 'ACCUPLACER Complete Bundle',
    'type'       => 'Full Bundle',
    'cat'        => 'Academic · Placement',
    'badge'      => 'Quick Results',
    'badge_c'    => 'new',
    'price'      => 59,
    'price_label'=> '$59',
    'format'     => 'Digital · All Formats',
    'icon'       => 'fa-layer-group',
    'color'      => 'var(--gold)',
    'desc'       => 'Study guide + practice test bundle covering all ACCUPLACER test versions. Place into college-level courses and skip costly remedial classes with our focused strategy-first approach.',
    'includes'   => [
      'All ACCUPLACER test versions covered',
      'Math guide: Arithmetic through Advanced Algebra',
      'Reading and Writing prep modules',
      '200+ practice questions per subject',
      'WritePlacer essay strategy guide',
      'Instant download access',
    ],
    'previews'   => [
      'Math Study Guide (Arithmetic to Advanced)',
      'Reading Comprehension Guide',
      'Writing & WritePlacer Guide',
      'Practice Questions — All Subjects',
      'Essay Strategy Template',
    ],
  ],
];

// Fallback
if (!isset($materials[$slug])) { $slug = 'teas-guide'; }
$material = $materials[$slug];

// ── POST handler (checkout) ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'checkout') {
  $name  = trim($_POST['student_name'] ?? '');
  $email = trim($_POST['student_email'] ?? '');
  $errors = [];

  if (strlen($name) < 2) $errors[] = 'Please enter your full name.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

  if (empty($errors)) {
    $is_new = true;
    try {
      require_once '../includes/db.php';
      $pdo = get_db();
      $stmt = $pdo->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      if ($stmt->fetchColumn()) { $is_new = false; }
    } catch (Exception $e) { $is_new = true; }

    $_SESSION['mat_order_name']     = $name;
    $_SESSION['mat_order_email']    = $email;
    $_SESSION['mat_order_material'] = $material['title'];
    $_SESSION['mat_order_slug']     = $slug;
    $_SESSION['mat_order_price']    = $material['price_label'];
    $_SESSION['mat_order_is_new']   = $is_new;

    header('Location: order.php?step=success&material=' . urlencode($slug));
    exit;
  }
}

// ── Page title ────────────────────────────────────────────────
if ($step === 'detail')         $page_title = $material['title'] . ' — ' . SITE_NAME;
elseif ($step === 'checkout')   $page_title = 'Checkout: ' . $material['title'] . ' — ' . SITE_NAME;
else                            $page_title = 'Purchase Confirmed — ' . SITE_NAME;

include '../includes/header.php';
?>

<?php if ($step === 'detail'): ?>
<!-- ════════════════════════════════════════════════════════════
     STEP 1 — MATERIAL DETAIL
  ════════════════════════════════════════════════════════════ -->
<div class="order-page">

  <div class="order-page-hero">
    <div class="container">
      <div class="breadcrumb" style="margin-bottom:20px;">
        <a href="../index.php">Home</a><span class="sep">›</span>
        <a href="../study-materials.php">Study Materials</a><span class="sep">›</span>
        <span><?= h($material['title']) ?></span>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <span style="font-size:.68rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);"><?= h($material['type']) ?> · <?= h($material['cat']) ?></span>
        <?php if ($material['badge']): ?>
        <span class="course-badge <?= h($material['badge_c']) ?>"><?= h($material['badge']) ?></span>
        <?php endif; ?>
      </div>
      <h1 style="font-size:clamp(1.8rem,4vw,2.8rem);margin-top:12px;"><?= h($material['title']) ?></h1>
    </div>
  </div>

  <div class="container">
    <div class="order-flow-grid">

      <!-- Main column -->
      <div class="order-flow-main">

        <!-- Material visual -->
        <div class="material-detail-img">
          <div class="material-detail-badge">
            <?php if ($material['badge']): ?>
            <span class="course-badge <?= h($material['badge_c']) ?>"><?= h($material['badge']) ?></span>
            <?php endif; ?>
          </div>
          <i class="fa-solid <?= h($material['icon']) ?> material-detail-icon" style="color:<?= $material['color'] ?>;"></i>
          <div style="position:absolute;inset:0;background:radial-gradient(circle at 60% 40%,<?= $material['color'] == 'var(--gold)' ? 'var(--gold-glow)' : 'var(--accent-glow)' ?>,transparent 65%);pointer-events:none;"></div>
        </div>

        <!-- Meta -->
        <div class="course-detail-meta">
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-file"></i> <?= h($material['format']) ?>
          </div>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-bolt"></i> Instant digital download
          </div>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-shield-halved"></i> Expert-written content
          </div>
          <div class="course-detail-meta-item">
            <i class="fa-solid fa-star" style="color:var(--gold);"></i> 4.9 avg. rating
          </div>
        </div>

        <!-- Description -->
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-circle-info"></i> About This Material</h3>
          <p style="font-size:.93rem;color:var(--white-60);line-height:1.85;"><?= h($material['desc']) ?></p>
        </div>

        <!-- What's included -->
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-box-open"></i> What's Included</h3>
          <ul class="course-includes-list">
            <?php foreach ($material['includes'] as $inc): ?>
            <li><i class="fa-solid fa-check-circle"></i><?= h($inc) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Table of contents preview -->
        <div class="course-detail-section">
          <h3><i class="fa-solid fa-table-of-contents fa-list"></i> Contents Preview</h3>
          <div class="curriculum-list">
            <?php foreach ($material['previews'] as $i => $preview): ?>
            <div class="curriculum-item glass-card" style="padding:0;border-radius:var(--radius);">
              <div class="curriculum-item-head">
                <div class="curriculum-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></div>
                <div class="curriculum-body">
                  <div class="curriculum-title"><?= h($preview) ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div><!-- /order-flow-main -->

      <!-- Sidebar -->
      <div class="order-flow-sidebar">
        <div class="pricing-card" style="<?= $material['color'] === 'var(--gold)' ? 'border-color:var(--gold-subtle);' : '' ?>">
          <?php if ($material['color'] === 'var(--gold)'): ?>
          <div style="position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,var(--gold-glow),transparent 70%);pointer-events:none;"></div>
          <?php endif; ?>
          <div class="pricing-price"><?= h($material['price_label']) ?></div>
          <div class="pricing-per">one-time · instant digital access</div>

          <a href="order.php?step=checkout&material=<?= urlencode($slug) ?>" class="pricing-btn <?= $material['color'] === 'var(--gold)' ? 'pricing-btn-gold' : '' ?>">
            Buy Now — <?= h($material['price_label']) ?> <i class="fa-solid fa-arrow-right"></i>
          </a>

          <ul class="pricing-features">
            <?php foreach ($material['includes'] as $inc): ?>
            <li><i class="fa-solid fa-check"></i><?= h($inc) ?></li>
            <?php endforeach; ?>
          </ul>

          <div class="pricing-guarantee">
            <i class="fa-solid fa-download"></i>
            Instant download after payment
          </div>

          <div class="pricing-paypal-note">
            <i class="fa-brands fa-paypal" style="color:#003087;"></i>
            Secure payment via PayPal
          </div>
        </div>

        <!-- Trust -->
        <div class="glass-card" style="margin-top:16px;padding:20px 24px;">
          <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-lock" style="color:var(--green);width:16px;text-align:center;"></i> Secure checkout
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-bolt" style="color:var(--accent);width:16px;text-align:center;"></i> Instant download link via email
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-mobile-screen" style="color:var(--gold);width:16px;text-align:center;"></i> Works on phone, tablet, laptop
            </div>
            <div style="display:flex;align-items:center;gap:10px;font-size:.78rem;color:var(--white-60);">
              <i class="fa-solid fa-infinity" style="color:var(--purple);width:16px;text-align:center;"></i> Yours forever — re-download anytime
            </div>
          </div>
        </div>
      </div>

    </div><!-- /order-flow-grid -->
  </div>

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
        <a href="../study-materials.php">Study Materials</a><span class="sep">›</span>
        <a href="order.php?step=detail&material=<?= urlencode($slug) ?>"><?= h($material['title']) ?></a><span class="sep">›</span>
        <span>Checkout</span>
      </div>
      <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);margin-bottom:0;">Complete Your Purchase</h1>
    </div>
  </div>

  <div class="container">

    <div class="checkout-steps" style="margin-top:40px;">
      <div class="checkout-step done">
        <div class="checkout-step-num"><i class="fa-solid fa-check" style="font-size:.55rem;"></i></div>
        <span class="checkout-step-label">Material Details</span>
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

    <div class="checkout-grid">

      <!-- Form -->
      <div class="checkout-form-wrap">
        <div class="checkout-form-card">
          <h2>Your Information</h2>
          <p>We'll email your instant download link and receipt to the address you provide below.</p>

          <?php if (!empty($errors)): ?>
          <div style="background:var(--rose-subtle);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;">
            <?php foreach ($errors as $e): ?>
            <p style="color:var(--rose);font-size:.83rem;margin-bottom:4px;"><i class="fa-solid fa-circle-exclamation"></i> <?= h($e) ?></p>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="order.php?step=checkout&material=<?= urlencode($slug) ?>" novalidate>

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
              <p class="form-hint">Your download link will be sent here immediately after payment.</p>
            </div>

            <div class="paypal-section">
              <p class="paypal-section-label">Complete Payment via PayPal</p>

              <button type="submit" class="btn-paypal">
                <i class="fa-brands fa-paypal" style="font-size:1.3rem;"></i>
                <span>Pay with PayPal &mdash; <?= h($material['price_label']) ?></span>
              </button>

              <div class="checkout-trust-row">
                <div class="checkout-trust-item"><i class="fa-solid fa-lock"></i> Secure</div>
                <div class="checkout-trust-item"><i class="fa-solid fa-bolt"></i> Instant</div>
                <div class="checkout-trust-item"><i class="fa-solid fa-shield-halved"></i> Protected</div>
              </div>
            </div>
          </form>
        </div>

        <div style="margin-top:20px;">
          <a href="order.php?step=detail&material=<?= urlencode($slug) ?>" style="font-size:.78rem;color:var(--white-40);display:flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Back to material details
          </a>
        </div>
      </div><!-- /checkout-form-wrap -->

      <!-- Summary -->
      <div class="checkout-summary-wrap">
        <div class="checkout-summary">
          <div class="checkout-summary-head">Order Summary</div>
          <div class="checkout-summary-body">
            <div class="checkout-summary-item">
              <div>
                <div class="checkout-summary-item-name"><?= h($material['title']) ?></div>
                <div class="checkout-summary-item-sub"><?= h($material['format']) ?></div>
              </div>
              <div class="checkout-summary-item-price"><?= h($material['price_label']) ?></div>
            </div>
            <div class="checkout-summary-item">
              <div>
                <div class="checkout-summary-item-name">Instant Download Access</div>
                <div class="checkout-summary-item-sub">Lifetime access included</div>
              </div>
              <div class="checkout-summary-item-price" style="color:var(--green);">Free</div>
            </div>
          </div>
          <div class="checkout-summary-total">
            <div class="checkout-summary-total-label">Total Due</div>
            <div class="checkout-summary-total-price"><?= h($material['price_label']) ?></div>
          </div>
        </div>

        <div class="glass-card" style="margin-top:16px;padding:20px 24px;">
          <p style="font-size:.78rem;color:var(--white-50);line-height:1.7;margin:0;">
            <i class="fa-solid fa-bolt" style="color:var(--accent);margin-right:6px;"></i>
            Your download link will be emailed instantly after payment. Files are high-resolution PDFs compatible with all devices.
          </p>
        </div>
      </div>

    </div><!-- /checkout-grid -->
  </div>

</div><!-- /order-page -->

<?php else: ?>
<!-- ════════════════════════════════════════════════════════════
     STEP 3 — SUCCESS
  ════════════════════════════════════════════════════════════ -->
<?php
$s_name     = h($_SESSION['mat_order_name']     ?? 'there');
$s_email    = h($_SESSION['mat_order_email']    ?? '');
$s_material = h($_SESSION['mat_order_material'] ?? $material['title']);
$s_price    = h($_SESSION['mat_order_price']    ?? $material['price_label']);
$s_is_new   = $_SESSION['mat_order_is_new']     ?? true;
?>
<div class="success-page">
  <div class="container">
    <div class="success-page-center">

      <div class="success-icon-wrap">
        <i class="fa-solid fa-check success-icon"></i>
      </div>

      <?php if ($s_is_new): ?>
      <h1>Purchase Complete, <?= $s_name ?>!</h1>
      <p>
        Thank you for purchasing <strong style="color:var(--white-90);"><?= $s_material ?></strong>.
        Your download link and a welcome email have been sent to
        <strong style="color:var(--accent);"><?= $s_email ?></strong>.
        We've also created a free student account for you so you can re-download any time.
      </p>

      <div class="success-steps-grid">
        <div class="success-step-card">
          <div class="success-step-num">1</div>
          <h4>Check Your Email</h4>
          <p>Your download link and student account credentials are on their way to your inbox right now.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">2</div>
          <h4>Download Your Material</h4>
          <p>Click the link in your email to instantly download your <?= $s_material ?> to any device.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">3</div>
          <h4>Start Studying</h4>
          <p>Open your PDF and follow the recommended study plan included inside. Good luck on your exam!</p>
        </div>
      </div>

      <?php else: ?>
      <h1>Download Ready, <?= $s_name ?>!</h1>
      <p>
        Your purchase of <strong style="color:var(--white-90);"><?= $s_material ?></strong> is confirmed.
        The download link has been sent to <strong style="color:var(--accent);"><?= $s_email ?></strong>.
        You can also access all your materials from your student dashboard.
      </p>

      <div class="success-steps-grid">
        <div class="success-step-card">
          <div class="success-step-num">1</div>
          <h4>Check Your Email</h4>
          <p>Your fresh download link for <?= $s_material ?> is in your inbox.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">2</div>
          <h4>Or Visit Dashboard</h4>
          <p>Log in to your student account to access and re-download all your purchased materials.</p>
        </div>
        <div class="success-step-card">
          <div class="success-step-num">3</div>
          <h4>Keep Up the Great Work</h4>
          <p>Use the included study plan and reach out to our expert support team whenever you need help.</p>
        </div>
      </div>

      <?php endif; ?>

      <div class="success-actions">
        <?php if ($s_is_new): ?>
        <a href="../student/login.php" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-right-to-bracket"></i> Log In to Dashboard
        </a>
        <?php else: ?>
        <a href="../student/dashboard.php" class="btn btn-primary btn-lg">
          <i class="fa-solid fa-gauge"></i> Go to My Dashboard
        </a>
        <?php endif; ?>
        <a href="../study-materials.php" class="btn btn-outline btn-lg">Browse More Materials</a>
      </div>

      <div style="margin-top:48px;padding:24px;background:var(--surface-glass);border:1px solid var(--border);border-radius:var(--radius-lg);backdrop-filter:blur(16px);">
        <p style="font-size:.8rem;color:var(--white-40);margin:0;">
          <i class="fa-solid fa-receipt" style="color:var(--accent);margin-right:8px;"></i>
          A payment receipt from PayPal has been sent to <strong style="color:var(--white-70);"><?= $s_email ?></strong>.
          Questions? Email <a href="mailto:<?= SITE_EMAIL ?>" style="color:var(--accent);"><?= SITE_EMAIL ?></a>
        </p>
      </div>

    </div>
  </div>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
