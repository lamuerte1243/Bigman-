<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = SITE_NAME . ' — Expert Academic Help | TEAS, HESI, NCLEX, GED, GRE & More';
$meta_desc  = 'Expert academic help with TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER. Online course support for StraighterLine, Sophia, Study.com, Excelsior. AI & human tutoring available 24/7.';

$testimonials = [
  ['name'=>'Brianna T.','role'=>'TEAS Test Taker','exam'=>'TEAS','quote'=>'I scored an 89 on my TEAS after just 3 weeks of prep with Sheila\'s study guide. The practice tests were spot-on to the real exam. Highly recommend!','avatar'=>'https://i.pravatar.cc/80?img=47'],
  ['name'=>'Marcus W.','role'=>'Nursing Student','exam'=>'NCLEX','quote'=>'Failed NCLEX twice on my own. Sheila\'s team helped me understand the NCSBN format and I passed on my third attempt with flying colors. Life-changing.','avatar'=>'https://i.pravatar.cc/80?img=12'],
  ['name'=>'Destiny R.','role'=>'Healthcare Worker','exam'=>'HESI','quote'=>'The HESI prep materials were incredibly detailed. My professor said my improvement was remarkable. Went from a 68 to an 84 in 4 weeks!','avatar'=>'https://i.pravatar.cc/80?img=32'],
  ['name'=>'James K.','role'=>'Adult Learner','exam'=>'GED','quote'=>'Passed all 4 GED subjects on my first try. The study schedule and practice materials kept me on track even while working full time.','avatar'=>'https://i.pravatar.cc/80?img=25'],
  ['name'=>'Ashley M.','role'=>'Grad School Applicant','exam'=>'GRE','quote'=>'Improved my GRE Quant score by 12 points in 6 weeks. The personalized study plan and tutor sessions made all the difference.','avatar'=>'https://i.pravatar.cc/80?img=55'],
  ['name'=>'Tiffany L.','role'=>'StraighterLine Student','exam'=>'Online Course','quote'=>'Sheila\'s team helped me complete 3 StraighterLine courses in one semester. They explain everything so clearly and are always available when I need help.','avatar'=>'https://i.pravatar.cc/80?img=19'],
];

include 'includes/header.php';
?>

<!-- ── HERO ──────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-grid" aria-hidden="true"></div>

  <div class="container">
    <!-- Left: Text Content -->
    <div class="hero-text fade-in-up">
      <div class="hero-badge">
        <i class="fa-solid fa-shield-check" style="font-size:.7rem;color:var(--accent);display:inline;"></i>
        Trusted by 4,800+ Students Nationwide
      </div>

      <h1 class="hero-title">
        Pass Every <span class="line2">High-Stakes</span> <em>Exam. Guaranteed.</em>
      </h1>

      <p class="hero-desc">
        Expert academic support for TEAS, HESI, NCLEX, GED, GRE &amp; ACCUPLACER — plus full course assistance for StraighterLine, Sophia, Study.com, and Excelsior. AI and human tutors available 24/7.
      </p>

      <div class="hero-actions">
        <a href="contact.php?intent=enroll" class="btn btn-primary btn-lg">
          Get Started Free <i class="fa-solid fa-arrow-right"></i>
        </a>
        <a href="courses.php" class="hero-play">
          <span class="play-circle"><i class="fa-solid fa-graduation-cap"></i></span>
          Explore Courses
        </a>
      </div>

      <!-- Social proof micro-line -->
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:32px;">
        <div style="display:flex;align-items:center;">
          <img src="https://i.pravatar.cc/32?img=47" alt="" style="width:28px;height:28px;border-radius:50%;border:2px solid var(--accent);object-fit:cover;margin-right:-10px;">
          <img src="https://i.pravatar.cc/32?img=12" alt="" style="width:28px;height:28px;border-radius:50%;border:2px solid var(--accent);object-fit:cover;margin-right:-10px;">
          <img src="https://i.pravatar.cc/32?img=32" alt="" style="width:28px;height:28px;border-radius:50%;border:2px solid var(--accent);object-fit:cover;margin-right:-10px;">
          <img src="https://i.pravatar.cc/32?img=25" alt="" style="width:28px;height:28px;border-radius:50%;border:2px solid var(--accent);object-fit:cover;margin-right:0;">
        </div>
        <div>
          <div style="display:flex;gap:2px;margin-bottom:2px;">
            <i class="fa-solid fa-star" style="color:var(--gold);font-size:.65rem;"></i>
            <i class="fa-solid fa-star" style="color:var(--gold);font-size:.65rem;"></i>
            <i class="fa-solid fa-star" style="color:var(--gold);font-size:.65rem;"></i>
            <i class="fa-solid fa-star" style="color:var(--gold);font-size:.65rem;"></i>
            <i class="fa-solid fa-star" style="color:var(--gold);font-size:.65rem;"></i>
          </div>
          <span style="font-size:.7rem;font-weight:600;color:var(--white-50);letter-spacing:.04em;">4.9/5 from 1,200+ verified reviews</span>
        </div>
      </div>

      <div class="hero-stats">
        <div class="hero-stat">
          <strong><span data-count="4800" data-suffix="+">4800+</span></strong>
          <span>Students Helped</span>
        </div>
        <div class="hero-stat">
          <strong><span data-count="98" data-suffix="%">98%</span></strong>
          <span>Pass Rate</span>
        </div>
        <div class="hero-stat">
          <strong><span data-count="4.9" data-suffix="/5">4.9/5</span></strong>
          <span>Avg. Rating</span>
        </div>
      </div>
    </div>

    <!-- Right: Premium Image Card -->
    <div class="hero-media fade-in-up delay-2">
      <div class="hero-img-wrap">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=700&h=800&fit=crop&auto=format&q=85" alt="Students achieving academic success with expert guidance" loading="lazy">

        <!-- Float Card 1 — Top Left -->
        <div class="float-card fc-1">
          <span class="fc-icon blue"><i class="fa-solid fa-graduation-cap"></i></span>
          <div class="fc-text">
            <strong>4,800+ Students</strong>
            <span>Successfully Helped</span>
          </div>
        </div>

        <!-- Float Card 2 — Right Middle -->
        <div class="float-card fc-2">
          <span class="fc-icon gold"><i class="fa-solid fa-trophy"></i></span>
          <div class="fc-text">
            <strong>98% Pass Rate</strong>
            <span>On All Exams</span>
          </div>
        </div>

        <!-- Float Card 3 — Bottom -->
        <div class="float-card fc-3">
          <span class="fc-icon green"><i class="fa-solid fa-lock"></i></span>
          <div class="fc-text">
            <strong>100% Confidential</strong>
            <span>Privacy Guaranteed</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── PARTNERS STRIP ─────────────────────────────────────────── -->
<section class="partners-strip">
  <div class="container">
    <span class="partner-label">We help with</span>
    <span class="partner-item">ATI · TEAS</span>
    <span class="partner-item">HESI · Elsevier</span>
    <span class="partner-item">NCLEX · NCSBN</span>
    <span class="partner-item">GED Testing</span>
    <span class="partner-item">ETS · GRE</span>
    <span class="partner-item">ACCUPLACER</span>
    <span class="partner-item">StraighterLine</span>
    <span class="partner-item">Sophia Learning</span>
    <span class="partner-item">Study.com</span>
    <span class="partner-item">Excelsior</span>
  </div>
</section>

<!-- ── WHAT WE OFFER ──────────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow">What We Offer</span>
      <h2>Academic Assistance<br><span>Built to Get You Results</span></h2>
      <p>Three focused service tracks — each designed to accelerate your academic success in a specific way.</p>
    </div>

    <div class="category-grid reveal">
      <a href="courses.php" class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-graduation-cap"></i></div>
        <h3>Exam Prep Courses</h3>
        <p>Structured prep programs for TEAS, HESI, NCLEX, GED, GRE, and ACCUPLACER with practice tests and study guides.</p>
        <div class="cat-count">10 Exams Covered</div>
      </a>

      <a href="tutoring.php" class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
        <h3>Tutoring</h3>
        <p>One-on-one sessions with expert tutors or AI-powered 24/7 support. Personalized learning plans for every student.</p>
        <div class="cat-count">AI + Human Tutors</div>
      </a>

      <a href="study-materials.php" class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-book-open"></i></div>
        <h3>Study Materials</h3>
        <p>Premium downloadable study guides, practice tests, flashcard decks, and cheat sheets — all crafted by our experts.</p>
        <div class="cat-count">100+ Materials</div>
      </a>

      <a href="courses.php#online-courses" class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-laptop-code"></i></div>
        <h3>Online Course Help</h3>
        <p>Full support for StraighterLine, Sophia Learning, Study.com, and Excelsior courses — from assignments to finals.</p>
        <div class="cat-count">4 Platforms Supported</div>
      </a>
    </div>
  </div>
</section>

<!-- ── FEATURED COURSES ───────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head reveal" style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <span class="eyebrow">Most Popular</span>
        <h2>Featured <span>Prep Courses</span></h2>
        <p>Our highest-rated programs — proven to get students to passing scores fast.</p>
      </div>
      <a href="courses.php" class="btn btn-outline">View All Courses <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="course-grid reveal">
      <!-- TEAS -->
      <article class="course-card">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=600&h=340&fit=crop&auto=format&q=80" alt="TEAS Exam Prep" loading="lazy">
          <span class="course-badge popular">Most Popular</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Healthcare · Nursing</div>
          <h3>TEAS Complete Prep Course</h3>
          <p>Master all four TEAS subject areas — Reading, Math, Science, and English Language. Includes 3 full practice exams and section-by-section drills.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 40+ hours</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 6 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $149</div>
          <a href="courses.php#teas" class="btn btn-primary btn-sm">Enroll Now</a>
        </div>
      </article>

      <!-- NCLEX -->
      <article class="course-card">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&h=340&fit=crop&auto=format&q=80" alt="NCLEX Exam Prep" loading="lazy">
          <span class="course-badge hot">High Demand</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Nursing Licensure</div>
          <h3>NCLEX-RN &amp; NCLEX-PN Prep</h3>
          <p>Next-Generation NCLEX format mastery. Clinical judgment models, case studies, and 2,000+ practice questions with rationales for both RN and PN tracks.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 50+ hours</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 7 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $199</div>
          <a href="courses.php#nclex" class="btn btn-primary btn-sm">Enroll Now</a>
        </div>
      </article>

      <!-- GED -->
      <article class="course-card">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=340&fit=crop&auto=format&q=80" alt="GED Exam Prep" loading="lazy">
          <span class="course-badge new">Updated 2025</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Academic Credential</div>
          <h3>GED Complete Prep — All 4 Subjects</h3>
          <p>Pass all four GED tests: Math, Science, Social Studies, and Reasoning Through Language Arts. Built for adult learners working at their own pace.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 35+ hours</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 5 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.8</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $129</div>
          <a href="courses.php#ged" class="btn btn-primary btn-sm">Enroll Now</a>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ── WHY CHOOSE SHEILA ───────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow gold">Why We're Different</span>
      <h2>Why Students Choose <span>Sheila The Writer</span></h2>
    </div>

    <div class="why-grid reveal">
      <!-- Left col -->
      <div class="why-col">
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--accent-subtle);color:var(--accent);"><i class="fa-solid fa-bullseye"></i></div>
          <div>
            <h4>Exam-Specific Expertise</h4>
            <p>Every prep program is built around the actual test blueprint — not generic study tips. We know each exam inside and out.</p>
          </div>
        </div>
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--gold-subtle);color:var(--gold);"><i class="fa-solid fa-robot"></i></div>
          <div>
            <h4>AI + Human Support</h4>
            <p>Get instant answers 24/7 from our AI tutor or schedule a real expert session when you need deeper guidance.</p>
          </div>
        </div>
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--green-subtle);color:var(--green);"><i class="fa-solid fa-clock-rotate-left"></i></div>
          <div>
            <h4>Flexible Scheduling</h4>
            <p>Sessions at any hour. Whether you study at 6AM or 2AM, we have tutors and AI support ready to help you.</p>
          </div>
        </div>
      </div>

      <!-- Center image -->
      <div class="why-center">
        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=500&h=640&fit=crop&auto=format&q=85" alt="Student studying with expert guidance and support" loading="lazy">
      </div>

      <!-- Right col -->
      <div class="why-col right">
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--purple-subtle);color:var(--purple);"><i class="fa-solid fa-shield-halved"></i></div>
          <div>
            <h4>100% Confidential</h4>
            <p>Your privacy is our top priority. All sessions and materials are completely private and never shared with third parties.</p>
          </div>
        </div>
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--rose-subtle);color:var(--rose);"><i class="fa-solid fa-chart-line"></i></div>
          <div>
            <h4>Proven Score Improvements</h4>
            <p>Students improve by an average of 15+ points on their exam scores after completing our structured prep programs.</p>
          </div>
        </div>
        <div class="why-feature">
          <div class="why-feature-icon" style="background:var(--accent-subtle);color:var(--accent);"><i class="fa-solid fa-headset"></i></div>
          <div>
            <h4>Ongoing Support</h4>
            <p>We don't disappear after you purchase. Follow-up check-ins, question Q&amp;As, and retake support are always included.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── STATS ──────────────────────────────────────────────────── -->
<section class="stats-banner">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="4800" data-suffix="+">4800+</span></div>
        <div class="stat-label">Students Successfully Helped</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="98" data-suffix="%">98%</span></div>
        <div class="stat-label">Exam Pass Rate</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="10" data-suffix="+">10+</span></div>
        <div class="stat-label">Exams &amp; Platforms Covered</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="4.9" data-suffix="/5">4.9/5</span></div>
        <div class="stat-label">Average Student Rating</div>
      </div>
    </div>
  </div>
</section>

<!-- ── TUTORS ─────────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow purple">Meet the Team</span>
      <h2>Expert Tutors <span>Ready to Help You</span></h2>
      <p>Seasoned educators and healthcare professionals who have walked the same path you're on.</p>
    </div>

    <div class="tutor-grid reveal">
      <div class="tutor-card">
        <div class="tutor-card-img">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop&auto=format&q=80" alt="Dr. Angela Reid" loading="lazy">
          <div class="tutor-badge-pill">NCLEX Specialist</div>
        </div>
        <div class="tutor-card-body">
          <h3>Dr. Angela Reid, RN</h3>
          <div class="role">NCLEX · HESI · Nursing</div>
          <p>15+ years as a registered nurse and nursing educator. Helped 800+ students pass NCLEX on first attempt.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>800+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.9 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>15 yrs</strong><span>Experience</span></div>
          </div>
        </div>
      </div>

      <div class="tutor-card">
        <div class="tutor-card-img">
          <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&auto=format&q=80" alt="Marcus Johnson" loading="lazy">
          <div class="tutor-badge-pill">TEAS Expert</div>
        </div>
        <div class="tutor-card-body">
          <h3>Marcus Johnson, M.Ed</h3>
          <div class="role">TEAS · GED · ACCUPLACER</div>
          <p>Former university admissions counselor and standardized test coach. Specialized in nursing school entrance exams.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>600+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.8 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>10 yrs</strong><span>Experience</span></div>
          </div>
        </div>
      </div>

      <div class="tutor-card">
        <div class="tutor-card-img">
          <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop&auto=format&q=80" alt="Dr. Priya Nair" loading="lazy">
          <div class="tutor-badge-pill">GRE Specialist</div>
        </div>
        <div class="tutor-card-body">
          <h3>Dr. Priya Nair, Ph.D</h3>
          <div class="role">GRE · Grad School Prep</div>
          <p>Princeton-trained education researcher. Scored 170/170 on GRE Quant. Expert in Verbal Reasoning and Analytical Writing.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>400+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>5.0 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>8 yrs</strong><span>Experience</span></div>
          </div>
        </div>
      </div>

      <div class="tutor-card">
        <div class="tutor-card-img">
          <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&auto=format&q=80" alt="Coach Derek Williams" loading="lazy">
          <div class="tutor-badge-pill">Online Courses</div>
        </div>
        <div class="tutor-card-body">
          <h3>Coach Derek Williams</h3>
          <div class="role">StraighterLine · Sophia · Study.com</div>
          <p>Course completion expert with deep knowledge of alternative credit platforms. Has helped 500+ students finish online courses.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>500+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.9 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>7 yrs</strong><span>Experience</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-40">
      <a href="tutoring.php" class="btn btn-outline btn-lg">Meet All Tutors <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ── TESTIMONIALS ───────────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow gold">Student Stories</span>
      <h2>Real Results from <span>Real Students</span></h2>
      <p>Over 4,800 students have passed their exams and completed their courses with our help. Here's what they say.</p>
    </div>

    <div class="testimonial-grid reveal">
      <?php foreach($testimonials as $t): ?>
      <div class="testimonial-card">
        <div class="tcard-stars">★★★★★</div>
        <p class="tcard-quote">"<?= h($t['quote']) ?>"</p>
        <div class="tcard-author">
          <img class="tcard-avatar" src="<?= h($t['avatar']) ?>" alt="<?= h($t['name']) ?>" loading="lazy">
          <div class="tcard-info">
            <strong><?= h($t['name']) ?></strong>
            <span><?= h($t['role']) ?></span>
            <span class="tcard-tag"><?= h($t['exam']) ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── BLOG PREVIEW ───────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head reveal" style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <span class="eyebrow">Learn Smarter</span>
        <h2>From the <span>Study Blog</span></h2>
        <p>Expert advice, study strategies, and exam insider tips updated weekly.</p>
      </div>
      <a href="blog.php" class="btn btn-outline">All Articles <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="blog-grid reveal">
      <article class="blog-card">
        <a href="blog.php" class="blog-card-img">
          <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&h=340&fit=crop&auto=format&q=80" alt="TEAS Study Tips" loading="lazy">
        </a>
        <div class="blog-card-body">
          <span class="blog-tag blue">TEAS</span>
          <h3><a href="blog.php">7 Proven TEAS Study Strategies That Actually Work in 2025</a></h3>
          <p>Stop wasting time on ineffective study methods. These 7 evidence-based strategies have helped our students boost their TEAS scores by 15+ points.</p>
          <div class="blog-meta">
            <span>Sheila Williams</span>
            <span class="blog-meta-dot"></span>
            <span>June 15, 2025</span>
            <span class="blog-meta-dot"></span>
            <span>8 min read</span>
          </div>
        </div>
      </article>

      <article class="blog-card">
        <a href="blog.php" class="blog-card-img">
          <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=340&fit=crop&auto=format&q=80" alt="NCLEX Study Tips and Exam Preparation" loading="lazy">
        </a>
        <div class="blog-card-body">
          <span class="blog-tag gold">NCLEX</span>
          <h3><a href="blog.php">Understanding Next-Generation NCLEX: What's Changed and How to Prepare</a></h3>
          <p>The NGN format is dramatically different from older NCLEX versions. Learn exactly what's new, what's tested, and the best way to study for 2025.</p>
          <div class="blog-meta">
            <span>Dr. Angela Reid</span>
            <span class="blog-meta-dot"></span>
            <span>June 8, 2025</span>
            <span class="blog-meta-dot"></span>
            <span>10 min read</span>
          </div>
        </div>
      </article>

      <article class="blog-card">
        <a href="blog.php" class="blog-card-img">
          <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=340&fit=crop&auto=format&q=80" alt="Online Courses Tips" loading="lazy">
        </a>
        <div class="blog-card-body">
          <span class="blog-tag green">Online Courses</span>
          <h3><a href="blog.php">How to Complete StraighterLine Courses 3x Faster (Without Cutting Corners)</a></h3>
          <p>Our students have cracked the code on StraighterLine pacing. Here's the exact system they use to speed through courses without missing a thing.</p>
          <div class="blog-meta">
            <span>Coach Derek Williams</span>
            <span class="blog-meta-dot"></span>
            <span>May 28, 2025</span>
            <span class="blog-meta-dot"></span>
            <span>6 min read</span>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ── FINAL CTA ───────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <span class="eyebrow" style="justify-content:center;">Ready to Start?</span>
    <h2>Your Passing Score<br><span>Starts Here.</span></h2>
    <p>Join 4,800+ students who have already transformed their academic trajectory with Sheila The Writer's expert guidance.</p>
    <div class="btn-group">
      <a href="contact.php?intent=enroll" class="btn btn-primary btn-lg">
        Get Expert Help <i class="fa-solid fa-arrow-right"></i>
      </a>
      <a href="courses.php" class="btn btn-outline btn-lg">
        Browse All Courses
      </a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
