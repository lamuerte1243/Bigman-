<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Study Materials – ' . SITE_NAME;
$meta_desc  = 'Premium study guides, practice tests, flashcards, and exam bundles for TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER, and more. Instant access upon purchase.';

include 'includes/header.php';

$materials = [
  // TEAS
  ['title'=>'TEAS 7 Complete Study Guide','desc'=>'The most comprehensive TEAS 7 prep guide. Covers all four sections: Reading, Mathematics, Science, and English & Language Usage with in-depth content review and proven test strategies.','type'=>'Study Guide','format'=>'PDF · 480 pages','price'=>'$47','category'=>'nursing','badge'=>'Bestseller','badge_class'=>'popular','slug'=>'teas-guide'],
  ['title'=>'TEAS 7 Practice Tests (3-Pack)','desc'=>'Three full-length TEAS 7 practice exams with complete answer rationales. ATI-aligned format with the same question types and difficulty level as the real exam.','type'=>'Practice Tests','format'=>'PDF · 3 Exams','price'=>'$39','category'=>'nursing','badge'=>'Most Popular','badge_class'=>'hot','slug'=>'teas-practice'],
  ['title'=>'TEAS Complete Bundle','desc'=>'Study guide + 3 practice tests + flashcard deck + quick reference cheat sheets. Everything you need to pass TEAS in one comprehensive package.','type'=>'Full Bundle','format'=>'Digital · All Formats','price'=>'$97','category'=>'bundle','badge'=>'Best Value','badge_class'=>'new','slug'=>'teas-bundle'],

  // HESI
  ['title'=>'HESI A² Complete Study Guide','desc'=>'Full content review for all 8 HESI A² sections: Math, Reading Comprehension, Vocabulary, Grammar, Biology, Chemistry, Anatomy & Physiology, and Physics.','type'=>'Study Guide','format'=>'PDF · 520 pages','price'=>'$47','category'=>'nursing','badge'=>null,'badge_class'=>null,'slug'=>'hesi-guide'],
  ['title'=>'HESI A² Flashcard Deck','desc'=>'1,800+ flashcards covering all HESI A² subjects. Perfect for spaced repetition study using Anki, physical cards, or our printable format.','type'=>'Flashcards','format'=>'PDF · 1,800 Cards','price'=>'$29','category'=>'nursing','badge'=>null,'badge_class'=>null,'slug'=>'hesi-flashcards'],
  ['title'=>'HESI Complete Bundle','desc'=>'Study guide + 2 practice tests + full flashcard deck. The complete HESI A² prep system at a discounted bundle price.','type'=>'Full Bundle','format'=>'Digital · All Formats','price'=>'$89','category'=>'bundle','badge'=>'Best Value','badge_class'=>'new','slug'=>'hesi-bundle'],

  // NCLEX
  ['title'=>'NCLEX-RN & PN Study Guide (NGN Edition)','desc'=>'Comprehensive Next-Generation NCLEX guide covering the updated Clinical Judgment Measurement Model, all client need categories, and the new item types.','type'=>'Study Guide','format'=>'PDF · 640 pages','price'=>'$57','category'=>'nursing','badge'=>'Updated 2025','badge_class'=>'new','slug'=>'nclex-guide'],
  ['title'=>'NCLEX Practice Questions (2,000+)','desc'=>'2,000+ practice questions in the NGN format with full rationales for every answer. Covers all NCLEX client need categories with emphasis on Clinical Judgment.','type'=>'Practice Tests','format'=>'PDF · 2,000 Questions','price'=>'$49','category'=>'nursing','badge'=>'High Demand','badge_class'=>'hot','slug'=>'nclex-questions'],

  // GED
  ['title'=>'GED Complete Study Guide — All 4 Subjects','desc'=>'Full content review for Mathematical Reasoning, Science, Social Studies, and Reasoning Through Language Arts. Perfect for adult learners returning to education.','type'=>'Study Guide','format'=>'PDF · 580 pages','price'=>'$45','category'=>'academic','badge'=>null,'badge_class'=>null,'slug'=>'ged-guide'],

  // GRE
  ['title'=>'GRE Complete Prep Package','desc'=>'Verbal Reasoning, Quantitative Reasoning, and Analytical Writing prep. Includes vocabulary system, essay templates, and 3 full practice tests aligned to current GRE format.','type'=>'Full Bundle','format'=>'Digital · All Formats','price'=>'$79','category'=>'grad','badge'=>'Grad School','badge_class'=>'popular','slug'=>'gre-package'],

  // ACCUPLACER
  ['title'=>'ACCUPLACER Complete Bundle','desc'=>'Study guide + practice test bundle covering all ACCUPLACER test versions. Place into college-level courses and skip costly remedial classes.','type'=>'Full Bundle','format'=>'Digital · All Formats','price'=>'$59','category'=>'bundle','badge'=>'Quick Results','badge_class'=>'new','slug'=>'accuplacer-bundle'],
];
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>Study Materials</span>
    </div>
    <h1>Premium Study <span style="color:var(--accent);">Materials</span></h1>
    <p>Comprehensive study guides, practice tests, flashcard decks, and full exam bundles — crafted by experts who know exactly what's on your exam. Every purchase includes instant digital access.</p>
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:32px;">
      <a href="#materials" class="btn btn-primary btn-lg">Browse Materials <i class="fa-solid fa-arrow-down"></i></a>
      <a href="contact.php" class="btn btn-outline btn-lg">Custom Bundle <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ── PARTNERS STRIP ─────────────────────────────────────────── -->
<section class="partners-strip">
  <div class="container">
    <span class="partner-label">Available for</span>
    <span class="partner-item"><i class="fa-solid fa-bolt" style="font-size:.6rem;color:var(--accent);"></i> Instant Access</span>
    <span class="partner-item"><i class="fa-solid fa-shield-halved" style="font-size:.6rem;color:var(--green);"></i> Expert-Written</span>
    <span class="partner-item"><i class="fa-solid fa-file-pdf" style="font-size:.6rem;color:var(--rose);"></i> PDF Format</span>
    <span class="partner-item"><i class="fa-solid fa-layer-group" style="font-size:.6rem;color:var(--purple);"></i> Flashcard Decks</span>
    <span class="partner-item"><i class="fa-solid fa-star" style="font-size:.6rem;color:var(--gold);"></i> 4.9/5 Rated</span>
  </div>
</section>

<!-- ── MATERIALS GRID ─────────────────────────────────────────── -->
<section class="section" id="materials">
  <div class="container">
    <div class="section-head reveal" style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <span class="eyebrow">All Materials</span>
        <h2>Study Materials <span>by Exam Type</span></h2>
        <p>Every material is written by certified experts, updated for the latest exam versions, and delivered instantly upon purchase.</p>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs reveal" data-filter-group>
      <button class="filter-tab active" data-filter="all">All Materials</button>
      <button class="filter-tab" data-filter="nursing">Nursing &amp; Healthcare</button>
      <button class="filter-tab" data-filter="academic">Academic &amp; GED</button>
      <button class="filter-tab" data-filter="grad">Grad School</button>
      <button class="filter-tab" data-filter="bundle">Bundles</button>
    </div>

    <div class="materials-grid reveal" data-filter-group>
      <?php foreach($materials as $m): ?>
      <div class="material-card" data-category="<?= $m['category'] ?>">
        <?php if ($m['badge']): ?>
        <div style="position:absolute;top:16px;right:16px;">
          <span class="course-badge <?= $m['badge_class'] ?>"><?= $m['badge'] ?></span>
        </div>
        <?php endif; ?>
        <div class="material-icon"><i class="fa-solid fa-book-open"></i></div>
        <div>
          <p style="font-size:.62rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);margin-bottom:6px;"><?= $m['type'] ?></p>
          <h3><?= $m['title'] ?></h3>
        </div>
        <p><?= $m['desc'] ?></p>
        <div class="material-meta">
          <span class="material-meta-item"><i class="fa-solid fa-file"></i> <?= $m['format'] ?></span>
          <span class="material-price"><?= $m['price'] ?></span>
        </div>
        <a href="study-materials/order.php?step=detail&material=<?= urlencode($m['slug']) ?>" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;margin-top:12px;">
          Order Now <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <span class="eyebrow" style="justify-content:center;">Need Something Custom?</span>
    <h2>Can't Find What<br><span>You're Looking For?</span></h2>
    <p>We create custom study packages tailored to your specific exam, timeline, and weak areas. Contact us to build yours.</p>
    <div class="btn-group">
      <a href="contact.php" class="btn btn-primary btn-lg">Request Custom Bundle <i class="fa-solid fa-arrow-right"></i></a>
      <a href="tutoring.php" class="btn btn-outline btn-lg">Book Tutoring Instead</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
