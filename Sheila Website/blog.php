<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Blog – Study Tips, Exam Guides & Academic Advice | ' . SITE_NAME;
$meta_desc  = 'Expert articles on TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER prep, online college courses, and academic success strategies. Free study tips from a certified academic consultant.';

include 'includes/header.php';

$posts = [
  ['id'=>1,'slug'=>'how-to-pass-teas-7-first-attempt','title'=>'How to Pass the TEAS 7 on Your First Attempt','excerpt'=>'The ATI TEAS 7 is one of the most important tests in your nursing school journey. Here\'s the exact study strategy I used to go from failing to scoring in the 92nd percentile.','category'=>'TEAS','date'=>'June 28, 2026','read_time'=>'8 min read','filter'=>'teas'],
  ['id'=>2,'slug'=>'nclex-ngn-new-format-explained','title'=>'NCLEX NGN: The New Format Fully Explained (2025)','excerpt'=>'The Next Generation NCLEX changed everything. Bow-tie questions, extended matrix items, trend questions — here\'s what every nursing student needs to know.','category'=>'NCLEX','date'=>'June 14, 2026','read_time'=>'11 min read','filter'=>'nclex'],
  ['id'=>3,'slug'=>'straighterline-vs-sophia-comparison','title'=>'StraighterLine vs. Sophia Learning: Which Is Right for You?','excerpt'=>'Both platforms let you earn transferable college credits online for a fraction of the cost. But they\'re not the same. Here\'s a detailed comparison.','category'=>'Online Courses','date'=>'May 31, 2026','read_time'=>'9 min read','filter'=>'online-courses'],
  ['id'=>4,'slug'=>'hesi-a2-study-schedule-8-week','title'=>'The 8-Week HESI A2 Study Schedule That Actually Works','excerpt'=>'Most students study for the HESI A2 the wrong way — cramming too many subjects at once with no structure. This schedule breaks everything down day by day.','category'=>'HESI','date'=>'May 19, 2026','read_time'=>'7 min read','filter'=>'hesi'],
  ['id'=>5,'slug'=>'ged-vs-high-school-diploma','title'=>'GED vs. High School Diploma: What Employers and Colleges Really Think','excerpt'=>'Is a GED as good as a high school diploma? The short answer might surprise you. Here\'s the honest truth about how colleges and employers view your GED.','category'=>'GED','date'=>'May 8, 2026','read_time'=>'6 min read','filter'=>'ged'],
  ['id'=>6,'slug'=>'gre-vocab-fastest-way-to-learn','title'=>'500 GRE Vocabulary Words: The Fastest Way to Learn Them','excerpt'=>'GRE vocabulary is notorious for its difficulty. But with root words, spaced repetition, and context sentences — you can master high-frequency words in half the time.','category'=>'GRE','date'=>'April 25, 2026','read_time'=>'10 min read','filter'=>'gre'],
  ['id'=>7,'slug'=>'accuplacer-tips-place-into-credit-courses','title'=>'7 Tips to Score High on ACCUPLACER and Skip Remedial Math','excerpt'=>'Remedial courses cost you time and money without giving you college credit. Here are 7 strategies to place into credit-bearing math and English on the first try.','category'=>'ACCUPLACER','date'=>'April 12, 2026','read_time'=>'6 min read','filter'=>'accuplacer'],
  ['id'=>8,'slug'=>'study-com-proctored-exams-tips','title'=>'How to Ace Study.com Proctored Exams on the First Try','excerpt'=>'Study.com proctored exams have a reputation for being tough. Here\'s what to expect, how the proctoring software works, and strategies that consistently get students passing.','category'=>'Online Courses','date'=>'March 30, 2026','read_time'=>'8 min read','filter'=>'online-courses'],
  ['id'=>9,'slug'=>'nclex-pharmacology-made-simple','title'=>'NCLEX Pharmacology Made Simple: The Category Approach','excerpt'=>'Pharmacology is the #1 source of anxiety for NCLEX candidates. Instead of memorizing 500 drugs, learn how to categorize medications by class and apply nursing judgment.','category'=>'NCLEX','date'=>'March 15, 2026','read_time'=>'12 min read','filter'=>'nclex'],
];

$tag_classes = ['TEAS'=>'blue','NCLEX'=>'blue','HESI'=>'purple','GED'=>'green','GRE'=>'gold','ACCUPLACER'=>'gold','Online Courses'=>'purple'];
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>Blog</span>
    </div>
    <h1>Expert Study Tips &amp;<br><span style="color:var(--accent);">Exam Prep Guides</span></h1>
    <p>Free advice from a certified academic consultant with 10+ years helping students pass TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER, and online college courses.</p>
  </div>
</section>

<!-- ── ALL POSTS ──────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <!-- Filter -->
    <div class="filter-tabs reveal" data-filter-group id="blogFilter">
      <button class="filter-tab active" data-filter="all">All Articles</button>
      <button class="filter-tab" data-filter="teas">TEAS</button>
      <button class="filter-tab" data-filter="hesi">HESI</button>
      <button class="filter-tab" data-filter="nclex">NCLEX</button>
      <button class="filter-tab" data-filter="ged">GED</button>
      <button class="filter-tab" data-filter="gre">GRE</button>
      <button class="filter-tab" data-filter="accuplacer">ACCUPLACER</button>
      <button class="filter-tab" data-filter="online-courses">Online Courses</button>
    </div>

    <div class="blog-grid reveal" id="blogGrid" data-filter-group>
      <?php foreach($posts as $i => $post):
        $tag_class = $tag_classes[$post['category']] ?? 'blue';
        $imgs = [
          'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=600&h=340&fit=crop&auto=format&q=75',
          'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&h=340&fit=crop&auto=format&q=75',
        ];
      ?>
      <article class="blog-card" data-category="<?= $post['filter'] ?>">
        <a href="blog/<?= $post['slug'] ?>.php" class="blog-card-img">
          <img src="<?= $imgs[$i % count($imgs)] ?>" alt="<?= h($post['title']) ?>" loading="lazy">
        </a>
        <div class="blog-card-body">
          <span class="blog-tag <?= $tag_class ?>"><?= h($post['category']) ?></span>
          <h3><a href="blog/<?= $post['slug'] ?>.php"><?= h($post['title']) ?></a></h3>
          <p><?= h(substr($post['excerpt'], 0, 155)) ?>…</p>
          <div class="blog-meta">
            <span><?= h($post['date']) ?></span>
            <span class="blog-meta-dot"></span>
            <span><?= h($post['read_time']) ?></span>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── NEWSLETTER ─────────────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div style="max-width:600px;margin:0 auto;text-align:center;" class="reveal">
      <span class="eyebrow" style="justify-content:center;">Stay Updated</span>
      <h2>Get Free Study Tips<br><span>in Your Inbox</span></h2>
      <p style="margin-bottom:40px;">Join 2,400+ students who receive weekly exam prep tips, study schedules, and exclusive discounts on materials and tutoring sessions.</p>
      <form data-ajax="true" action="api/newsletter.php" method="POST" style="display:flex;gap:12px;flex-wrap:wrap;">
        <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
        <input type="email" name="email" placeholder="Your email address" required style="flex:1;min-width:240px;">
        <button type="submit" class="btn btn-primary">Subscribe <i class="fa-solid fa-arrow-right"></i></button>
      </form>
      <p style="font-size:.72rem;color:rgba(255,255,255,.3);margin-top:16px;"><i class="fa-solid fa-lock"></i> No spam. Unsubscribe anytime.</p>
    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <h2>Reading Is Great —<br><span>Expert Help Gets You There Faster.</span></h2>
    <p>Personalized tutoring and premium study materials cut your prep time in half.</p>
    <div class="btn-group">
      <a href="study-materials.php" class="btn btn-primary btn-lg">Shop Materials <i class="fa-solid fa-arrow-right"></i></a>
      <a href="tutoring.php" class="btn btn-outline btn-lg">Book Tutoring</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
