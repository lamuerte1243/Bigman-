<!-- ── Footer ──────────────────────────────────────────────────── -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Brand col -->
      <div class="footer-brand">
        <a href="<?= isset($in_subdir) ? '../' : '' ?>index.php" class="brand" style="display:inline-flex;margin-bottom:4px;">
          <img src="<?= isset($in_subdir) ? '../' : '' ?>sheila-white.png" alt="Sheila The Writer" class="brand-logo-img brand-logo-footer">
        </a>
        <p class="footer-desc">Expert academic help for nursing students, healthcare professionals, and adult learners navigating high-stakes exams and online courses.</p>
        <div class="footer-social">
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="Twitter/X"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
        </div>
      </div>

      <!-- Services -->
      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php">Exam Prep Courses</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>tutoring.php">AI Tutoring (24/7)</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>tutoring.php#human">Human Tutoring</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>study-materials.php">Study Materials</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>contact.php">Custom Support</a></li>
        </ul>
      </div>

      <!-- Exams -->
      <div class="footer-col">
        <h4>Exams We Cover</h4>
        <ul>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#teas">TEAS Prep</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#hesi">HESI Prep</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#nclex">NCLEX Prep</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#ged">GED Prep</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#gre">GRE Prep</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>courses.php#accuplacer">ACCUPLACER Prep</a></li>
        </ul>
      </div>

      <!-- Company -->
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>about.php">About Sheila</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>blog.php">Blog &amp; Resources</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>contact.php">Contact Us</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>privacy.php">Privacy Policy</a></li>
          <li><a href="<?= isset($in_subdir) ? '../' : '' ?>terms.php">Terms of Service</a></li>
        </ul>
        <div style="margin-top:24px;">
          <p style="font-size:.65rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.3);margin-bottom:10px;">Contact</p>
          <a href="mailto:<?= SITE_EMAIL ?>" style="color:rgba(255,255,255,.4);font-size:.8rem;display:block;margin-bottom:6px;transition:color .2s;"><?= SITE_EMAIL ?></a>
          <a href="tel:<?= SITE_PHONE ?>" style="color:rgba(255,255,255,.4);font-size:.8rem;transition:color .2s;"><?= SITE_PHONE ?></a>
        </div>
      </div>
    </div>

    <div class="glow-line"></div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
      <div style="display:flex;gap:20px;">
        <a href="<?= isset($in_subdir) ? '../' : '' ?>privacy.php">Privacy</a>
        <a href="<?= isset($in_subdir) ? '../' : '' ?>terms.php">Terms</a>
      </div>
      <span>Wilmington, Delaware, USA</span>
    </div>
  </div>
</footer>

<!-- ── Back to Top ────────────────────────────────────────────── -->
<button class="back-to-top" id="back-to-top" aria-label="Back to top">
  <i class="fa-solid fa-chevron-up"></i>
</button>

<!-- ── JS ──────────────────────────────────────────────────────── -->
<script src="<?= isset($in_subdir) ? '../' : '' ?>js/main.js"></script>
</body>
</html>
