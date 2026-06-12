/* =============================================================
   BIGMAN ACADEMIC SERVICES — SHARED COMPONENTS
   Injects header, footer, and announcement bar into every page
   ============================================================= */
'use strict';

/* ─── ACTIVE PAGE DETECTION ─────────────────────────────────── */
function getActivePage() {
  const path = window.location.pathname;
  const file = path.split('/').pop() || 'index.html';
  return file;
}

/* ─── HEADER HTML ────────────────────────────────────────────── */
function buildHeader() {
  const page = getActivePage();
  const root = page === 'index.html' || page === '' ? '' : '';

  const navLinks = [
    { href: 'index.html', label: 'Home', icon: 'fa-home', id: 'index.html' },
    { href: 'tutoring.html', label: 'Tutoring', icon: 'fa-chalkboard-teacher', id: 'tutoring.html' },
    { href: 'courses.html', label: 'Courses', icon: 'fa-book-open', id: 'courses.html' },
    { href: 'materials.html', label: 'Materials', icon: 'fa-folder-open', id: 'materials.html' },
    { href: 'expert-help.html', label: 'Expert Help', icon: 'fa-user-tie', id: 'expert-help.html' },
    { href: 'blog.html', label: 'Blog', icon: 'fa-newspaper', id: 'blog.html' },
    { href: 'index.html#contact', label: 'Contact', icon: 'fa-envelope', id: 'contact' },
  ];

  const serviceDropdown = `
    <li class="has-dropdown">
      <a href="index.html#services" class="nav-link">Services <i class="fas fa-chevron-down"></i></a>
      <ul class="dropdown-menu">
        <li><a href="tutoring.html"><i class="fas fa-chalkboard-teacher"></i> Online Tutoring</a></li>
        <li><a href="courses.html"><i class="fas fa-book-open"></i> Courses</a></li>
        <li><a href="materials.html"><i class="fas fa-folder-open"></i> Study Materials</a></li>
        <li><a href="expert-help.html"><i class="fas fa-user-tie"></i> Expert Help</a></li>
        <li><a href="index.html#services"><i class="fas fa-pen-fancy"></i> Essay Writing</a></li>
        <li><a href="index.html#services"><i class="fas fa-microscope"></i> Research Support</a></li>
        <li><a href="index.html#services"><i class="fas fa-book"></i> Dissertation Help</a></li>
      </ul>
    </li>`;

  const desktopNav = navLinks.map(l => {
    if (l.id === 'index.html') return `<li><a href="${l.href}" class="nav-link${page === l.id ? ' active' : ''}">Home</a></li>`;
    if (l.id === 'contact') return `<li><a href="${l.href}" class="nav-link">Contact</a></li>`;
    const isActive = page === l.id;
    // Insert services dropdown after Home, before Tutoring
    return `<li><a href="${l.href}" class="nav-link${isActive ? ' active' : ''}">${l.label}</a></li>`;
  }).join('');

  const mobileNavLinks = navLinks.map(l => {
    const isActive = page === l.id;
    return `<li><a href="${l.href}" class="mobile-nav-link${isActive ? ' active' : ''}"><i class="fas ${l.icon}"></i> ${l.label}</a></li>`;
  }).join('');

  return `
  <!-- Preloader -->
  <div class="preloader" id="preloader">
    <div class="preloader-logo"><i class="fas fa-graduation-cap"></i></div>
    <div class="preloader-bar"><div class="preloader-bar-fill"></div></div>
  </div>

  <!-- Announcement Bar -->
  <div class="announcement-bar" id="announcementBar">
    <div class="container">
      <p>🎓 <strong>Limited Offer:</strong> Get 20% off your first order — Use code <strong>BIGMAN20</strong> at checkout!</p>
      <button class="close-announcement" id="closeAnnouncement" aria-label="Close announcement"><i class="fas fa-times"></i></button>
    </div>
  </div>

  <!-- Header -->
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="index.html" class="logo" aria-label="Bigman Academic Services Home">
        <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="logo-text">
          <span class="logo-main">Bigman</span>
          <span class="logo-sub">Academic Services</span>
        </div>
      </a>
      <nav class="main-nav" id="mainNav" aria-label="Main Navigation">
        <ul class="nav-list">
          <li><a href="index.html" class="nav-link${page === 'index.html' || page === '' ? ' active' : ''}">Home</a></li>
          ${serviceDropdown}
          <li><a href="tutoring.html" class="nav-link${page === 'tutoring.html' ? ' active' : ''}">Tutoring</a></li>
          <li><a href="courses.html" class="nav-link${page === 'courses.html' ? ' active' : ''}">Courses</a></li>
          <li><a href="materials.html" class="nav-link${page === 'materials.html' ? ' active' : ''}">Materials</a></li>
          <li><a href="expert-help.html" class="nav-link${page === 'expert-help.html' ? ' active' : ''}">Expert Help</a></li>
          <li><a href="blog.html" class="nav-link${page === 'blog.html' || page.startsWith('article') ? ' active' : ''}">Blog</a></li>
          <li><a href="index.html#contact" class="nav-link">Contact</a></li>
        </ul>
      </nav>
      <div class="header-actions">
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark/light mode">
          <span class="theme-icon light-icon"><i class="fas fa-sun"></i></span>
          <span class="theme-icon dark-icon"><i class="fas fa-moon"></i></span>
        </button>
        <a href="login.html" class="btn btn-outline btn-sm" style="border:1.5px solid var(--primary);color:var(--primary);background:transparent;"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="signup.html" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Sign Up</a>
        <button class="hamburger" id="hamburger" aria-label="Toggle mobile menu" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile Nav -->
  <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
  <nav class="mobile-nav" id="mobileNav" aria-label="Mobile Navigation">
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="Close mobile menu"><i class="fas fa-times"></i></button>
    <div class="mobile-logo"><i class="fas fa-graduation-cap"></i><span>Bigman Academic</span></div>
    <ul class="mobile-nav-list">
      <li><a href="index.html" class="mobile-nav-link"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="tutoring.html" class="mobile-nav-link"><i class="fas fa-chalkboard-teacher"></i> Tutoring</a></li>
      <li><a href="courses.html" class="mobile-nav-link"><i class="fas fa-book-open"></i> Courses</a></li>
      <li><a href="materials.html" class="mobile-nav-link"><i class="fas fa-folder-open"></i> Materials</a></li>
      <li><a href="expert-help.html" class="mobile-nav-link"><i class="fas fa-user-tie"></i> Expert Help</a></li>
      <li><a href="blog.html" class="mobile-nav-link"><i class="fas fa-newspaper"></i> Blog</a></li>
      <li><a href="index.html#contact" class="mobile-nav-link"><i class="fas fa-envelope"></i> Contact</a></li>
      <li><a href="login.html" class="mobile-nav-link${page === 'login.html' ? ' active' : ''}"><i class="fas fa-sign-in-alt"></i> Student Login</a></li>
      <li><a href="signup.html" class="mobile-nav-link${page === 'signup.html' ? ' active' : ''}"><i class="fas fa-user-plus"></i> Sign Up</a></li>
    </ul>
    <a href="signup.html" class="btn btn-primary btn-full mt-4">Enrol Now — It's Free</a>
  </nav>`;
}

/* ─── FOOTER HTML ────────────────────────────────────────────── */
function buildFooter() {
  return `
  <footer class="site-footer" id="footer">
    <div class="footer-top">
      <div class="container footer-grid">
        <div class="footer-col footer-brand">
          <a href="index.html" class="logo footer-logo">
            <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <div class="logo-text">
              <span class="logo-main">Bigman</span>
              <span class="logo-sub">Academic Services</span>
            </div>
          </a>
          <p>Your trusted partner for academic excellence. Professional, confidential, and results-driven support for students worldwide.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Our Pages</h4>
          <ul class="footer-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="tutoring.html">Tutoring</a></li>
            <li><a href="courses.html">Courses</a></li>
            <li><a href="materials.html">Study Materials</a></li>
            <li><a href="expert-help.html">Expert Help</a></li>
            <li><a href="blog.html">Blog & Articles</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Our Services</h4>
          <ul class="footer-links">
            <li><a href="index.html#services">Essay Writing</a></li>
            <li><a href="index.html#services">Research Support</a></li>
            <li><a href="index.html#services">Dissertation Help</a></li>
            <li><a href="tutoring.html">Online Tutoring</a></li>
            <li><a href="index.html#services">Proofreading</a></li>
            <li><a href="index.html#services">Math & Sciences</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Get In Touch</h4>
          <ul class="footer-contact">
            <li><i class="fas fa-envelope"></i><a href="mailto:support@bigmanacademicservices.com">support@bigmanacademicservices.com</a></li>
            <li><i class="fas fa-phone"></i><a href="tel:+1234567890">+1 (234) 567-890</a></li>
            <li><i class="fab fa-whatsapp"></i><a href="https://wa.me/1234567890" target="_blank">WhatsApp Support</a></li>
            <li><i class="fas fa-clock"></i><span>Available 24/7</span></li>
          </ul>
          <div class="footer-newsletter">
            <h5>Get Academic Tips</h5>
            <form class="newsletter-form" id="newsletterForm">
              <input type="email" placeholder="Your email address" required aria-label="Email for newsletter" />
              <button type="submit" aria-label="Subscribe"><i class="fas fa-paper-plane"></i></button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>&copy; <span id="currentYear"></span> Bigman Academic Services. All Rights Reserved.</p>
        <ul class="footer-legal">
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Cookie Policy</a></li>
          <li><a href="#">Refund Policy</a></li>
        </ul>
      </div>
    </div>
  </footer>

  <button class="scroll-top" id="scrollTop" aria-label="Scroll to top"><i class="fas fa-arrow-up"></i></button>
  <a href="https://wa.me/1234567890" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="whatsapp-tooltip">Chat with us!</span>
  </a>`;
}

/* ─── INJECT COMPONENTS ──────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Inject header before #page-content
  const headerPlaceholder = document.getElementById('site-header-placeholder');
  if (headerPlaceholder) headerPlaceholder.innerHTML = buildHeader();

  // Inject footer
  const footerPlaceholder = document.getElementById('site-footer-placeholder');
  if (footerPlaceholder) footerPlaceholder.innerHTML = buildFooter();
});
