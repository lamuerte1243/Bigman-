<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Expert Tutoring — AI & Human Tutors | ' . SITE_NAME;
$meta_desc  = 'One-on-one expert tutoring for TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER and all online courses. AI 24/7 support + human tutor sessions. Book today.';

include 'includes/header.php';
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>Tutoring</span>
    </div>
    <h1>Expert <span style="color:var(--accent);">Tutoring</span></h1>
    <p>Personalized one-on-one guidance from certified educators. AI support available 24/7 — human experts available on your schedule.</p>
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:32px;">
      <a href="tutoring/booking.php?step=select-type" class="btn btn-primary btn-lg">Book a Session <i class="fa-solid fa-calendar"></i></a>
      <a href="#ai-tutor" class="btn btn-outline btn-lg">Try AI Tutor <i class="fa-solid fa-robot"></i></a>
    </div>
  </div>
</section>

<!-- ── TUTORING OPTIONS ────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow">Two Ways to Learn</span>
      <h2>Tutoring That Fits <span>Your Life</span></h2>
      <p>Whether you need help right now at 2AM or prefer scheduled sessions with a specialist — we have you covered.</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--border);max-width:960px;margin:0 auto;" class="reveal">

      <!-- AI Tutor -->
      <div id="ai-tutor" style="background:var(--surface-2);padding:48px 40px;">
        <div style="width:60px;height:60px;background:var(--accent-subtle);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:28px;color:var(--accent);">
          <i class="fa-solid fa-robot"></i>
        </div>
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--accent);margin-bottom:12px;">24/7 Available</p>
        <h3 style="margin-bottom:16px;font-size:1.5rem;">AI Tutor</h3>
        <p style="font-size:.88rem;color:var(--white-50);margin-bottom:28px;line-height:1.75;">Get instant answers to your study questions anytime — even at 3AM the night before your exam. Our AI tutor is trained on expert content across all major exams.</p>
        <ul style="display:flex;flex-direction:column;gap:10px;margin-bottom:36px;">
          <?php foreach(['Instant responses — no waiting','Concept explanations with examples','Practice questions on demand','All subjects — TEAS, HESI, NCLEX, GED, GRE','Available on any device'] as $f): ?>
          <li style="display:flex;gap:12px;font-size:.82rem;color:var(--white-60);">
            <span style="color:var(--accent);font-size:.6rem;margin-top:4px;">▸</span><?= $f ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <div style="font-family:var(--font-display);font-size:2.5rem;font-weight:800;color:var(--white);margin-bottom:8px;">$49<span style="font-size:1rem;font-weight:400;color:var(--white-40);">/mo</span></div>
        <a href="tutoring/booking.php?step=select-type" class="btn btn-primary" style="width:100%;justify-content:center;">Get AI Tutor Access <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <!-- Human Tutor -->
      <div id="human" style="background:var(--surface-3);padding:48px 40px;border:1px solid var(--border-accent);">
        <div style="width:60px;height:60px;background:var(--gold-subtle);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:28px;color:var(--gold);">
          <i class="fa-solid fa-user-graduate"></i>
        </div>
        <p style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);margin-bottom:12px;">Expert Led</p>
        <h3 style="margin-bottom:16px;font-size:1.5rem;">Human Tutor</h3>
        <p style="font-size:.88rem;color:var(--white-50);margin-bottom:28px;line-height:1.75;">Work one-on-one with a credentialed expert who specializes in your specific exam. Get personalized feedback, a custom study plan, and the accountability to succeed.</p>
        <ul style="display:flex;flex-direction:column;gap:10px;margin-bottom:36px;">
          <?php foreach(['Certified specialists for every exam','Custom study plans built for you','Live video sessions — Zoom or Google Meet','Session recordings to review anytime','Progress tracking between sessions'] as $f): ?>
          <li style="display:flex;gap:12px;font-size:.82rem;color:var(--white-60);">
            <span style="color:var(--gold);font-size:.6rem;margin-top:4px;">▸</span><?= $f ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <div style="font-family:var(--font-display);font-size:2.5rem;font-weight:800;color:var(--white);margin-bottom:8px;">$79<span style="font-size:1rem;font-weight:400;color:var(--white-40);">/session</span></div>
        <a href="tutoring/booking.php?step=select-type" class="btn btn-gold" style="width:100%;justify-content:center;">Book a Session <i class="fa-solid fa-calendar"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ── TUTOR PROFILES ──────────────────────────────────────────── -->
<section class="section bg-alt">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow gold">Meet Our Experts</span>
      <h2>Our <span>Specialist Tutors</span></h2>
      <p>Each tutor is a credentialed professional with real-world experience in their field.</p>
    </div>

    <div class="tutor-grid reveal">
      <div class="tutor-card">
        <div class="tutor-card-img">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop&auto=format&q=80" alt="Dr. Angela Reid" loading="lazy">
          <div class="tutor-badge-pill">NCLEX &amp; HESI</div>
        </div>
        <div class="tutor-card-body">
          <h3>Dr. Angela Reid, RN</h3>
          <div class="role">NCLEX · HESI · Nursing</div>
          <p>Registered nurse with 15 years of clinical and teaching experience. Former nursing program director. Specializes in Next-Generation NCLEX format.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>800+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.9 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>15 yrs</strong><span>Experience</span></div>
          </div>
          <a href="tutoring/booking.php?step=select-subject&type=human&subject=nclex" class="btn btn-gold btn-sm" style="width:100%;justify-content:center;margin-top:16px;">Book Session <i class="fa-solid fa-arrow-right"></i></a>
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
          <p>Former university admissions counselor and standardized test coach. Specialized in nursing school entrance exams with 10+ years experience.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>600+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.8 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>10 yrs</strong><span>Experience</span></div>
          </div>
          <a href="tutoring/booking.php?step=select-subject&type=human&subject=teas" class="btn btn-gold btn-sm" style="width:100%;justify-content:center;margin-top:16px;">Book Session <i class="fa-solid fa-arrow-right"></i></a>
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
          <a href="tutoring/booking.php?step=select-subject&type=human&subject=gre" class="btn btn-gold btn-sm" style="width:100%;justify-content:center;margin-top:16px;">Book Session <i class="fa-solid fa-arrow-right"></i></a>
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
          <p>Course completion expert with deep knowledge of alternative credit platforms. Has helped 500+ students finish online courses rapidly.</p>
          <div class="tutor-stats">
            <div class="tutor-stat"><strong>500+</strong><span>Students</span></div>
            <div class="tutor-stat"><strong>4.9 ★</strong><span>Rating</span></div>
            <div class="tutor-stat"><strong>7 yrs</strong><span>Experience</span></div>
          </div>
          <a href="tutoring/booking.php?step=select-subject&type=human&subject=straighterline" class="btn btn-gold btn-sm" style="width:100%;justify-content:center;margin-top:16px;">Book Session <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── BOOKING CTA SECTION ──────────────────────────────────────── -->
<section class="section" id="book">
  <div class="container">
    <div class="section-head center reveal">
      <span class="eyebrow">Book a Session</span>
      <h2>Ready to Start? <span>Book Now</span></h2>
      <p>Choose your tutor type, select your subject, pick your schedule, and confirm — all in one guided flow. No forms, no waiting rooms.</p>
    </div>
    <div style="text-align:center;" class="reveal">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:680px;margin:0 auto 40px;">
        <a href="tutoring/booking.php?step=select-type" class="btn btn-primary btn-lg" style="justify-content:center;">
          <i class="fa-solid fa-robot"></i> Book AI Tutor
        </a>
        <a href="tutoring/booking.php?step=select-type" class="btn btn-gold btn-lg" style="justify-content:center;">
          <i class="fa-solid fa-user-graduate"></i> Book Human Tutor
        </a>
      </div>
      <p style="font-size:.8rem;color:var(--white-40);">
        <i class="fa-brands fa-paypal" style="color:#003087;"></i> PayPal only &nbsp;·&nbsp;
        <i class="fa-solid fa-lock" style="color:var(--green);"></i> Secure checkout &nbsp;·&nbsp;
        <i class="fa-solid fa-envelope-circle-check" style="color:var(--accent);"></i> Confirmation by email
      </p>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
