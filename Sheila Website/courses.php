<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Exam Prep Courses — TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER | ' . SITE_NAME;
$meta_desc  = 'Structured exam prep courses for TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER, and more. Also supporting StraighterLine, Sophia, Study.com, and Excelsior online courses.';

include 'includes/header.php';
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>Courses</span>
    </div>
    <h1>Exam Prep <span style="color:var(--accent);">Courses</span></h1>
    <p>Structured, expert-built prep programs for every major nursing, healthcare, and academic exam — plus full support for major online learning platforms.</p>
  </div>
</section>

<!-- ── EXAM PREP SECTION ──────────────────────────────────────── -->
<section class="section" id="exam-prep">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Standardized Exams</span>
      <h2>Online Exams <span>We Help With</span></h2>
      <p>Each program is built around the actual exam blueprint and updated for the latest test formats.</p>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs reveal" data-filter-group id="exam-filter">
      <button class="filter-tab active" data-filter="all">All Exams</button>
      <button class="filter-tab" data-filter="nursing">Nursing &amp; Healthcare</button>
      <button class="filter-tab" data-filter="academic">Academic &amp; GED</button>
      <button class="filter-tab" data-filter="grad">Grad School</button>
    </div>

    <div class="course-grid reveal" data-filter-group>

      <!-- TEAS -->
      <article class="course-card" id="teas" data-category="nursing">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=600&h=340&fit=crop&auto=format&q=80" alt="TEAS Prep" loading="lazy">
          <span class="course-badge popular">Most Popular</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Healthcare · Nursing Entrance</div>
          <h3>TEAS Complete Prep Course</h3>
          <p>Comprehensive preparation covering all four TEAS subject areas: Reading, Math, Science, and English Language Usage. ATI TEAS 7th Edition content with 3 full-length practice exams and detailed score analysis.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>3 Full practice exams with answer rationales</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>500+ subject-specific drill questions</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>6-week structured study schedule</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 40+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 6 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $149</div>
          <a href="courses/order.php?step=detail&course=teas" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <!-- HESI -->
      <article class="course-card" id="hesi" data-category="nursing">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=600&h=340&fit=crop&auto=format&q=80" alt="HESI A² Exam Preparation" loading="lazy">
          <span class="course-badge hot">High Demand</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Healthcare · Nursing Entrance</div>
          <h3>HESI A² Complete Prep</h3>
          <p>Full preparation for the HESI Admission Assessment Exam including all 8 tested subjects: Math, Reading Comprehension, Vocabulary, Grammar, Biology, Chemistry, Anatomy &amp; Physiology, and Physics.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>All 8 HESI subject areas covered</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>2 full practice exams + section tests</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>Anatomy &amp; Physiology deep dives</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 45+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 8 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.8</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $159</div>
          <a href="courses/order.php?step=detail&course=hesi" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <!-- NCLEX -->
      <article class="course-card" id="nclex" data-category="nursing">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&h=340&fit=crop&auto=format&q=80" alt="NCLEX Prep" loading="lazy">
          <span class="course-badge popular">Most Popular</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Nursing Licensure · NCSBN</div>
          <h3>NCLEX-RN &amp; NCLEX-PN Prep</h3>
          <p>Comprehensive Next-Generation NCLEX preparation for both RN and PN licensure. Covers the updated Clinical Judgment Measurement Model (CJMM), case studies, and 2,000+ practice questions with rationales.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>NGN format mastery (bowtie, matrix, etc.)</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>2,000+ practice questions with rationales</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>Separate RN and PN track options</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 50+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 7 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $199</div>
          <a href="courses/order.php?step=detail&course=nclex" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <!-- GED -->
      <article class="course-card" id="ged" data-category="academic">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=600&h=340&fit=crop&auto=format&q=80" alt="GED Prep" loading="lazy">
          <span class="course-badge new">Updated 2025</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Academic Credential · Adult Education</div>
          <h3>GED Complete Prep — All 4 Subjects</h3>
          <p>Structured preparation for all four GED test subjects: Mathematical Reasoning, Science, Social Studies, and Reasoning Through Language Arts. Built for adult learners — goes at your pace, explains concepts from the ground up.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>All 4 GED subjects in one program</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>4 full practice tests (one per subject)</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>Essay writing guidance for RLA</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 35+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 5 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.8</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $129</div>
          <a href="courses/order.php?step=detail&course=ged" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <!-- GRE -->
      <article class="course-card" id="gre" data-category="grad">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&h=340&fit=crop&auto=format&q=80" alt="GRE Prep" loading="lazy">
          <span class="course-badge info" style="background:var(--purple);">Grad School</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Graduate Admissions · ETS</div>
          <h3>GRE Complete Prep Course</h3>
          <p>Comprehensive GRE preparation covering Verbal Reasoning, Quantitative Reasoning, and Analytical Writing. Target scores in the 155–170 range for competitive programs using official ETS materials.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>All 3 GRE sections covered in depth</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>Analytical Writing essay templates &amp; practice</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>Vocabulary building system (500 words)</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 48+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 6 modules</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $179</div>
          <a href="courses/order.php?step=detail&course=gre" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <!-- ACCUPLACER -->
      <article class="course-card" id="accuplacer" data-category="academic">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&h=340&fit=crop&auto=format&q=80" alt="ACCUPLACER Prep" loading="lazy">
          <span class="course-badge new">Quick Results</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">College Placement · CollegeBoard</div>
          <h3>ACCUPLACER Complete Prep</h3>
          <p>Master all ACCUPLACER tests: Reading, Writing, Math (Arithmetic, Quantitative Reasoning, Advanced Algebra &amp; Functions). Place into higher-level courses and avoid remedial classes.</p>
          <ul style="font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:16px;display:flex;flex-direction:column;gap:8px;padding-left:0;list-style:none;">
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>All ACCUPLACER test versions covered</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>300+ practice problems with step-by-step solutions</li>
            <li style="display:flex;gap:10px;align-items:flex-start;"><span style="color:var(--accent);flex-shrink:0;font-size:.65rem;margin-top:3px;">▸</span>CLT (Classic Learning Test) module included</li>
          </ul>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-clock"></i> 25+ hrs</span>
            <span class="course-meta-item"><i class="fa-solid fa-list-check"></i> 5 modules</span>
            <span class="course-rating"><span class="stars">★★★★☆</span> 4.7</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $99</div>
          <a href="courses/order.php?step=detail&course=accuplacer" class="btn btn-primary btn-sm">Enroll Now <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

    </div>
  </div>
</section>

<!-- ── ONLINE COURSES SECTION ─────────────────────────────────── -->
<section class="section bg-alt" id="online-courses">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow purple">Online Platforms</span>
      <h2>Online Courses <span>We Help With</span></h2>
      <p>Full assignment and coursework support for major alternative credit platforms. Our experts know these platforms inside and out.</p>
    </div>

    <div class="course-grid reveal">

      <article class="course-card" id="straighterline">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?w=600&h=340&fit=crop&auto=format&q=80" alt="StraighterLine" loading="lazy">
          <span class="course-badge popular">Top Platform</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Alternative Credit · StraighterLine</div>
          <h3>StraighterLine Course Support</h3>
          <p>Full academic assistance for all StraighterLine courses. We help with quizzes, discussions, assignments, and proctored finals across 60+ courses.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-graduation-cap"></i> 60+ Courses</span>
            <span class="course-meta-item"><i class="fa-solid fa-bolt"></i> Fast Completion</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.9</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $199</div>
          <a href="courses/order.php?step=detail&course=straighterline" class="btn btn-primary btn-sm">Get Help <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <article class="course-card" id="sophia">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?w=600&h=340&fit=crop&auto=format&q=80" alt="Sophia Learning" loading="lazy">
          <span class="course-badge new">Unlimited Plan Friendly</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Alternative Credit · Sophia Learning</div>
          <h3>Sophia Learning Course Support</h3>
          <p>Expert help with Sophia Learning's subscription-based college courses. Maximize the value of your unlimited plan by completing courses faster.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-graduation-cap"></i> 50+ Courses</span>
            <span class="course-meta-item"><i class="fa-solid fa-bolt"></i> Fast Completion</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.8</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $179</div>
          <a href="courses/order.php?step=detail&course=sophia" class="btn btn-primary btn-sm">Get Help <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

      <article class="course-card" id="studycom">
        <div class="course-card-img">
          <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=340&fit=crop&auto=format&q=80" alt="Study.com" loading="lazy">
          <span class="course-badge hot">Credit Transfer Ready</span>
        </div>
        <div class="course-card-body">
          <div class="course-cat">Alternative Credit · Study.com</div>
          <h3>Study.com Course Support</h3>
          <p>Full guidance for Study.com's college credit-eligible courses and proctored exams — transferable to hundreds of colleges through ACE credit recommendations.</p>
          <div class="course-meta">
            <span class="course-meta-item"><i class="fa-solid fa-graduation-cap"></i> 100+ Courses</span>
            <span class="course-meta-item"><i class="fa-solid fa-certificate"></i> ACE Credit</span>
            <span class="course-rating"><span class="stars">★★★★★</span> 4.8</span>
          </div>
        </div>
        <div class="course-card-footer">
          <div class="course-price"><span class="from">from</span> $189</div>
          <a href="courses/order.php?step=detail&course=sophia" class="btn btn-primary btn-sm">Get Help <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>

    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <span class="eyebrow" style="justify-content:center;">Ready to Start?</span>
    <h2>Not Sure Which Course<br><span>is Right for You?</span></h2>
    <p>Tell us your goal and we'll recommend the perfect prep program and match you with the right expert.</p>
    <div class="btn-group">
      <a href="contact.php" class="btn btn-primary btn-lg">Free Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="tutoring.php" class="btn btn-outline btn-lg">Explore Tutoring</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
