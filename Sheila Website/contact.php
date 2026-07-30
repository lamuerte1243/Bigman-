<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$page_title = 'Contact – Get Help With Your Exam | ' . SITE_NAME;
$meta_desc  = 'Contact Sheila The Writer for personalized academic help. TEAS, HESI, NCLEX, GED, GRE, ACCUPLACER prep, tutoring, and study materials. Fast response guaranteed.';

$pre_course   = isset($_GET['course'])   ? h($_GET['course'])   : '';
$pre_material = isset($_GET['material']) ? h($_GET['material']) : '';
$pre_subject  = $pre_course ?: $pre_material;

include 'includes/header.php';
?>

<!-- ── PAGE HERO ──────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a><span class="sep">›</span><span>Contact</span>
    </div>
    <h1>Let's Map Out <span style="color:var(--accent);">Your Path</span><br>to Passing</h1>
    <p>Fill out the form below and we'll respond within 2 business hours. Tell us your exam, timeline, and goals — and we'll put together the right plan for you.</p>
  </div>
</section>

<!-- ── CONTACT MAIN ───────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="contact-grid reveal">

      <!-- Info -->
      <div class="contact-info">
        <h3 style="margin-bottom:32px;font-size:1.25rem;">How We Help</h3>

        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fa-solid fa-bolt"></i></div>
          <div>
            <h4>Fast Response</h4>
            <p>Replies within 2 business hours — often within 30 minutes during peak hours.</p>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <div>
            <h4>100% Confidential</h4>
            <p>Your information and sessions are completely private and never shared.</p>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fa-solid fa-star"></i></div>
          <div>
            <h4>4.9/5 Rated</h4>
            <p>Over 4,800 students served with a 98% exam pass rate.</p>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fa-solid fa-envelope"></i></div>
          <div>
            <h4>Email</h4>
            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
          </div>
        </div>

        <div class="contact-detail">
          <div class="contact-detail-icon"><i class="fa-solid fa-phone"></i></div>
          <div>
            <h4>Phone / WhatsApp</h4>
            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
          </div>
        </div>

        <div style="margin-top:40px;padding:28px;background:var(--accent-subtle);border:1px solid var(--accent-border);border-radius:var(--radius);">
          <p style="font-size:.78rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--accent);margin-bottom:10px;">Quick Stats</p>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
              <div style="font-family:var(--font-display);font-size:1.75rem;font-weight:800;color:var(--white);line-height:1;">4,800+</div>
              <div style="font-size:.68rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-top:4px;">Students Helped</div>
            </div>
            <div>
              <div style="font-family:var(--font-display);font-size:1.75rem;font-weight:800;color:var(--white);line-height:1;">98%</div>
              <div style="font-size:.68rem;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;margin-top:4px;">Pass Rate</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="contact-form-wrap">
        <h3 style="margin-bottom:8px;">Send Us a Message</h3>
        <p style="font-size:.82rem;color:rgba(255,255,255,.4);margin-bottom:32px;">All fields marked with * are required.</p>

        <?php if (function_exists('flash_html')) echo flash_html(); ?>

        <form id="contactForm" data-ajax="true" action="api/contact.php" method="POST" novalidate>
          <?php if (function_exists('csrf_field')) echo csrf_field(); ?>

          <div class="form-row">
            <div class="form-group">
              <label for="first_name">First Name *</label>
              <input type="text" id="first_name" name="first_name" placeholder="Jane" required autocomplete="given-name">
            </div>
            <div class="form-group">
              <label for="last_name">Last Name *</label>
              <input type="text" id="last_name" name="last_name" placeholder="Smith" required autocomplete="family-name">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" placeholder="jane@email.com" required autocomplete="email">
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="+1 (555) 000-0000" autocomplete="tel">
            </div>
          </div>

          <div class="form-group">
            <label for="subject">What Do You Need Help With? *</label>
            <select id="subject" name="subject" required>
              <option value="">— Select a topic —</option>
              <optgroup label="Exam Prep Courses">
                <option value="TEAS Prep Course">TEAS Prep Course</option>
                <option value="HESI Prep Course">HESI Prep Course</option>
                <option value="NCLEX Prep Course">NCLEX Prep Course</option>
                <option value="GED Prep Course">GED Prep Course</option>
                <option value="GRE Prep Course">GRE Prep Course</option>
                <option value="ACCUPLACER Prep Course">ACCUPLACER Prep Course</option>
              </optgroup>
              <optgroup label="Online Platform Courses">
                <option value="StraighterLine Course Help">StraighterLine Course Help</option>
                <option value="Sophia Course Help">Sophia Course Help</option>
                <option value="Study.com Course Help">Study.com Course Help</option>
                <option value="Excelsior Course Help">Excelsior Course Help</option>
              </optgroup>
              <optgroup label="Tutoring">
                <option value="AI Tutoring">AI Tutoring (24/7)</option>
                <option value="Human Tutoring">Human Tutoring Session</option>
                <option value="Personalized Study Plan">Personalized Study Plan</option>
              </optgroup>
              <optgroup label="Study Materials">
                <option value="TEAS Study Materials">TEAS Study Materials / Bundle</option>
                <option value="HESI Study Materials">HESI Study Materials / Bundle</option>
                <option value="NCLEX Study Materials">NCLEX Study Materials / Bundle</option>
                <option value="GED Study Materials">GED Study Materials / Bundle</option>
                <option value="GRE Study Materials">GRE Study Materials / Bundle</option>
              </optgroup>
              <option value="Custom / Other">Custom Request / Other</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="exam_date">Exam / Deadline Date</label>
              <input type="date" id="exam_date" name="exam_date" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
              <label for="budget">Budget Range</label>
              <select id="budget" name="budget">
                <option value="">— Optional —</option>
                <option value="Under $50">Under $50</option>
                <option value="$50 – $100">$50 – $100</option>
                <option value="$100 – $200">$100 – $200</option>
                <option value="$200 – $300">$200 – $300</option>
                <option value="$300+">$300+</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="message">Tell Us About Your Goals *</label>
            <textarea id="message" name="message" placeholder="Tell us about your exam, current score, target score, timeline, and any specific challenges you're facing..." required></textarea>
          </div>

          <div class="form-group" style="flex-direction:row;align-items:flex-start;gap:12px;">
            <input type="checkbox" id="consent" name="consent" required style="width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:var(--accent);">
            <label for="consent" style="font-size:.78rem;text-transform:none;letter-spacing:0;font-weight:400;color:rgba(255,255,255,.4);">I agree to be contacted by Sheila The Writer regarding my inquiry. Your information is kept strictly confidential.</label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
            Send My Message <i class="fa-solid fa-arrow-right"></i>
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
