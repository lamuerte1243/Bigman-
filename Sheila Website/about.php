<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'About Sheila — ' . SITE_NAME;
$meta_desc  = 'Meet Sheila, your preferred academic consultant. A certified educator and nursing school graduate with over 10 years helping students pass TEAS, HESI, NCLEX, GED, GRE, and online college courses.';

include 'includes/header.php';
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>About</span>
    </div>
    <h1>The Person Behind<br><span style="color:var(--accent);">Every Student's Success</span></h1>
    <p>For over a decade, I've helped thousands of students navigate the academic obstacles that once stood between them and their dreams — nursing licenses, college diplomas, graduate degrees, and career pivots.</p>
  </div>
</section>

<!-- ── STATS ROW ──────────────────────────────────────────────── -->
<section class="stats-banner">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="4800" data-suffix="+">4,800+</span></div>
        <div class="stat-label">Students Successfully Helped</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="98" data-suffix="%">98%</span></div>
        <div class="stat-label">Exam Pass Rate</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="10" data-suffix="+">10+</span></div>
        <div class="stat-label">Years of Experience</div>
      </div>
      <div class="stat-item reveal">
        <div class="stat-number"><span data-count="4.9" data-suffix="★">4.9★</span></div>
        <div class="stat-label">Average Rating</div>
      </div>
    </div>
  </div>
</section>

<!-- ── MY STORY ───────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="two-col reveal">
      <div class="two-col-text">
        <span class="eyebrow">My Story</span>
        <h2>I Was Once the Student<br><span>Who Almost Gave Up</span></h2>
        <p>I know what it feels like to stare at a test prep book and feel completely lost. I know the anxiety of opening a practice exam and seeing questions that look like they're written in another language. I know the shame of failing a nursing entrance exam while your family is counting on you to succeed.</p>
        <p>I failed the TEAS on my first attempt. I was devastated. But that failure changed everything — because it forced me to figure out exactly how to study smarter, not just harder. I developed strategies, deciphered the patterns, learned what the test-makers actually care about. And the second time? I scored in the 92nd percentile.</p>
        <p>That experience became my calling. I went on to earn my nursing degree, complete graduate-level education coursework, and get certified as an academic tutor. But most importantly, I started helping other students crack the same tests that once intimidated me.</p>
        <a href="contact.php" class="btn btn-primary">Work With Me <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div>
        <div class="glass-card" style="padding:48px;text-align:center;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-lg);">
          <div style="width:100px;height:100px;border-radius:50%;background:var(--accent-subtle);border:2px solid var(--accent-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2.5rem;color:var(--accent);">
            <i class="fa-solid fa-user-graduate"></i>
          </div>
          <h3 style="margin-bottom:8px;">Sheila Perry</h3>
          <p style="font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);margin-bottom:20px;">Founder &amp; Lead Academic Consultant</p>
          <p style="font-size:.85rem;line-height:1.75;color:rgba(255,255,255,.5);">"Every student I help is a reminder of why I started. When someone messages me at 11pm saying 'I passed my NCLEX,' it makes every late night worth it."</p>
          <div style="margin-top:28px;padding-top:24px;border-top:1px solid var(--border);display:flex;gap:20px;justify-content:center;flex-wrap:wrap;">
            <div style="text-align:center;">
              <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;color:var(--white);">M.Ed</div>
              <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);margin-top:2px;">Education</div>
            </div>
            <div style="text-align:center;">
              <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;color:var(--white);">RN</div>
              <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);margin-top:2px;">Nursing</div>
            </div>
            <div style="text-align:center;">
              <div style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;color:var(--white);">10+</div>
              <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.3);margin-top:2px;">Years</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── WHY DIFFERENT ───────────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow gold">Why Different</span>
      <h2>What Sets Us <span>Apart</span></h2>
    </div>
    <div class="category-grid reveal">
      <div class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-bullseye"></i></div>
        <h3>Exam-Specific Expertise</h3>
        <p>Every prep program is built around the actual test blueprint — not generic study tips. We know each exam inside and out.</p>
        <div class="cat-count">10+ Exams Mastered</div>
      </div>
      <div class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-robot"></i></div>
        <h3>AI + Human Support</h3>
        <p>Get instant answers 24/7 from our AI tutor or schedule a real expert session when you need deeper guidance.</p>
        <div class="cat-count">24/7 Availability</div>
      </div>
      <div class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-shield-halved"></i></div>
        <h3>100% Confidential</h3>
        <p>Your privacy is our top priority. All sessions and materials are completely private and never shared with third parties.</p>
        <div class="cat-count">Fully Private</div>
      </div>
      <div class="category-card">
        <div class="cat-icon"><i class="fa-solid fa-chart-line"></i></div>
        <h3>Proven Results</h3>
        <p>Students improve by an average of 15+ points on their exam scores after completing our structured prep programs.</p>
        <div class="cat-count">+15 Avg Points</div>
      </div>
    </div>
  </div>
</section>

<!-- ── TIMELINE ────────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Our Journey</span>
      <h2>A Decade of <span>Academic Excellence</span></h2>
    </div>
    <div class="timeline reveal" style="max-width:720px;">
      <div class="timeline-item">
        <div class="timeline-year">2013</div>
        <div class="timeline-content">
          <h4>The Beginning</h4>
          <p>Sheila Perry passes TEAS in the 92nd percentile after failing once. Begins tutoring classmates informally.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-year">2015</div>
        <div class="timeline-content">
          <h4>Sheila The Writer Launched</h4>
          <p>Official launch of the academic consulting practice. First 50 students served. 100% pass rate achieved.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-year">2018</div>
        <div class="timeline-content">
          <h4>Expanded to Online Platforms</h4>
          <p>Added StraighterLine, Sophia, and Study.com support. Team expanded to 4 specialist tutors.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-year">2021</div>
        <div class="timeline-content">
          <h4>1,000 Students Milestone</h4>
          <p>Reached 1,000 students helped. Launched the premium study materials library.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-year">2024</div>
        <div class="timeline-content">
          <h4>AI Tutoring Introduced</h4>
          <p>Launched 24/7 AI tutoring capability. 4,800+ students helped. 98% pass rate maintained.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ───────────────────────────────────────────────────── -->
<section class="cta-section">
  <div class="container">
    <span class="eyebrow" style="justify-content:center;">Ready to Start?</span>
    <h2>Let's Write Your <span>Success Story.</span></h2>
    <p>Join 4,800+ students who have already transformed their academic trajectory with expert guidance.</p>
    <div class="btn-group">
      <a href="contact.php" class="btn btn-primary btn-lg">Get Started Today <i class="fa-solid fa-arrow-right"></i></a>
      <a href="courses.php" class="btn btn-outline btn-lg">Browse Courses</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
