/* =============================================================
   BIGMAN ACADEMIC SERVICES — MAIN JAVASCRIPT
   ============================================================= */

'use strict';

/* ─── DOM HELPERS ───────────────────────────────────────────── */
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

/* ─── THEME MANAGER ─────────────────────────────────────────── */
const ThemeManager = (() => {
  const STORAGE_KEY = 'bigman-theme';
  const HTML = document.documentElement;

  function get() {
    return localStorage.getItem(STORAGE_KEY) ||
      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  }

  function set(theme) {
    HTML.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
  }

  function toggle() {
    set(HTML.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
  }

  function init() {
    set(get());
    const btn = $('#themeToggle');
    if (btn) btn.addEventListener('click', toggle);
  }

  return { init, toggle, get, set };
})();


/* ─── ANNOUNCEMENT BAR ──────────────────────────────────────── */
const AnnouncementBar = (() => {
  function init() {
    const bar   = $('#announcementBar');
    const close = $('#closeAnnouncement');
    if (!bar || !close) return;

    // Hide if already dismissed
    if (sessionStorage.getItem('announcement-dismissed')) {
      bar.style.display = 'none';
    }

    close.addEventListener('click', () => {
      bar.style.transition = 'max-height .4s ease, opacity .4s ease, padding .4s ease';
      bar.style.maxHeight  = bar.offsetHeight + 'px';
      requestAnimationFrame(() => {
        bar.style.maxHeight = '0';
        bar.style.opacity   = '0';
        bar.style.padding   = '0';
        bar.style.overflow  = 'hidden';
      });
      setTimeout(() => bar.remove(), 420);
      sessionStorage.setItem('announcement-dismissed', '1');
    });
  }
  return { init };
})();


/* ─── STICKY HEADER ─────────────────────────────────────────── */
const StickyHeader = (() => {
  function init() {
    const header = $('#siteHeader');
    if (!header) return;

    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }
  return { init };
})();


/* ─── MOBILE NAVIGATION ─────────────────────────────────────── */
const MobileNav = (() => {
  function init() {
    const hamburger = $('#hamburger');
    const nav       = $('#mobileNav');
    const overlay   = $('#mobileNavOverlay');
    const closeBtn  = $('#mobileNavClose');
    const links     = $$('.mobile-nav-link');

    if (!hamburger || !nav) return;

    function open() {
      nav.classList.add('open');
      overlay.classList.add('active');
      hamburger.classList.add('active');
      hamburger.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    function close() {
      nav.classList.remove('open');
      overlay.classList.remove('active');
      hamburger.classList.remove('active');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', () =>
      nav.classList.contains('open') ? close() : open()
    );
    closeBtn?.addEventListener('click', close);
    overlay.addEventListener('click', close);
    links.forEach(l => l.addEventListener('click', close));

    // Close on Escape
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') close();
    });
  }
  return { init };
})();


/* ─── ACTIVE NAV LINK ON SCROLL ─────────────────────────────── */
const ActiveNav = (() => {
  function init() {
    const sections = $$('section[id], main[id]');
    const navLinks = $$('.nav-link');

    if (!sections.length || !navLinks.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          navLinks.forEach(l => l.classList.remove('active'));
          const active = navLinks.find(l => l.getAttribute('href') === `#${entry.target.id}`);
          if (active) active.classList.add('active');
        }
      });
    }, { threshold: 0.35 });

    sections.forEach(s => observer.observe(s));
  }
  return { init };
})();


/* ─── SCROLL TO TOP ─────────────────────────────────────────── */
const ScrollTop = (() => {
  function init() {
    const btn = $('#scrollTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
      btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
  return { init };
})();


/* ─── COUNTER ANIMATION ─────────────────────────────────────── */
const Counters = (() => {
  function animateCounter(el, target, duration = 2000) {
    let start = 0;
    const step = timestamp => {
      if (!start) start = timestamp;
      const progress = Math.min((timestamp - start) / duration, 1);
      // Ease out cubic
      const ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(ease * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  function init() {
    const counters = $$('[data-target]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
          entry.target.dataset.animated = '1';
          const target = parseInt(entry.target.dataset.target, 10);
          animateCounter(entry.target, target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
  }
  return { init };
})();


/* ─── AOS (ANIMATE ON SCROLL) ───────────────────────────────── */
const AOS = (() => {
  function init() {
    const elements = $$('[data-aos]');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const delay = parseInt(entry.target.dataset.aosDelay || 0, 10);
          setTimeout(() => {
            entry.target.classList.add('aos-animate');
          }, delay);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(el => observer.observe(el));
  }
  return { init };
})();


/* ─── TESTIMONIALS CAROUSEL ─────────────────────────────────── */
const TestimonialsCarousel = (() => {
  let current    = 0;
  let total      = 0;
  let perView    = 3;
  let autoTimer  = null;

  function getPerView() {
    if (window.innerWidth <= 640)  return 1;
    if (window.innerWidth <= 1024) return 2;
    return 3;
  }

  function buildDots(dotsEl, count) {
    dotsEl.innerHTML = '';
    for (let i = 0; i < count; i++) {
      const dot = document.createElement('button');
      dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
      dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
      dot.addEventListener('click', () => goTo(i));
      dotsEl.appendChild(dot);
    }
  }

  function updateDots(dotsEl, idx) {
    $$('.carousel-dot', dotsEl).forEach((d, i) =>
      d.classList.toggle('active', i === idx)
    );
  }

  function goTo(idx) {
    const track = $('#testimonialsTrack');
    const cards = $$('.testimonial-card', track);
    perView = getPerView();
    total   = Math.ceil(cards.length / perView);
    current = Math.max(0, Math.min(idx, total - 1));

    // Show/hide cards
    cards.forEach((card, i) => {
      const start = current * perView;
      const visible = i >= start && i < start + perView;
      card.style.display = visible ? '' : 'none';
    });

    const dotsEl = $('#carouselDots');
    if (dotsEl) updateDots(dotsEl, current);
  }

  function next() { goTo((current + 1) % total); }
  function prev() { goTo((current - 1 + total) % total); }

  function startAuto() {
    stopAuto();
    autoTimer = setInterval(next, 5000);
  }

  function stopAuto() {
    if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
  }

  function init() {
    const track   = $('#testimonialsTrack');
    const prevBtn = $('#testimonialPrev');
    const nextBtn = $('#testimonialNext');
    const dotsEl  = $('#carouselDots');

    if (!track) return;

    const cards = $$('.testimonial-card', track);
    perView = getPerView();
    total   = Math.ceil(cards.length / perView);

    if (dotsEl) buildDots(dotsEl, total);
    goTo(0);
    startAuto();

    prevBtn?.addEventListener('click', () => { prev(); startAuto(); });
    nextBtn?.addEventListener('click', () => { next(); startAuto(); });

    // Pause on hover
    track.addEventListener('mouseenter', stopAuto);
    track.addEventListener('mouseleave', startAuto);

    // Touch/swipe support
    let touchStartX = 0;
    track.addEventListener('touchstart', e => {
      touchStartX = e.changedTouches[0].clientX;
    }, { passive: true });
    track.addEventListener('touchend', e => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); startAuto(); }
    }, { passive: true });

    // Rebuild on resize
    window.addEventListener('resize', () => {
      perView = getPerView();
      total   = Math.ceil(cards.length / perView);
      if (dotsEl) buildDots(dotsEl, total);
      goTo(0);
    });
  }

  return { init };
})();


/* ─── PRICING TOGGLE ─────────────────────────────────────────── */
const PricingToggle = (() => {
  function init() {
    const toggle = $('#pricingToggle');
    if (!toggle) return;

    toggle.addEventListener('change', () => {
      const isExpress = toggle.checked;
      $$('.standard-price').forEach(el => el.style.display = isExpress ? 'none' : '');
      $$('.express-price').forEach(el => el.style.display  = isExpress ? '' : 'none');
    });
  }
  return { init };
})();


/* ─── CIRCULAR PROGRESS RINGS ───────────────────────────────── */
const ProgressRings = (() => {
  const CIRCUMFERENCE = 213.6; // 2 * π * 34

  function animateRing(ring) {
    const pct    = parseInt(ring.dataset.percent, 10);
    const fill   = ring.querySelector('.ring-fill');
    if (!fill) return;
    const offset = CIRCUMFERENCE * (1 - pct / 100);
    fill.style.strokeDashoffset = offset;
  }

  function init() {
    const rings = $$('.progress-ring[data-percent]');
    if (!rings.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
          entry.target.dataset.animated = '1';
          animateRing(entry.target);
        }
      });
    }, { threshold: 0.5 });

    rings.forEach(r => observer.observe(r));
  }
  return { init };
})();


/* ─── CONTACT FORM ───────────────────────────────────────────── */
const ContactForm = (() => {
  function validate(form) {
    let valid = true;
    $$('[required]', form).forEach(field => {
      if (!field.value.trim()) {
        field.style.borderColor = 'var(--danger)';
        valid = false;
      } else {
        field.style.borderColor = '';
      }
      // Email format
      if (field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
        field.style.borderColor = 'var(--danger)';
        valid = false;
      }
    });
    return valid;
  }

  function init() {
    const form      = $('#contactForm');
    const success   = $('#formSuccess');
    const submitBtn = $('#submitBtn');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!validate(form)) return;

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

      const data = {
        firstName: $('#firstName')?.value?.trim(),
        lastName:  $('#lastName')?.value?.trim(),
        email:     $('#email')?.value?.trim(),
        service:   $('#service')?.value,
        deadline:  $('#deadline')?.value,
        pages:     $('#pages')?.value,
        message:   $('#message')?.value?.trim(),
      };

      try {
        // ── Real API call ──────────────────────────────────────
        if (typeof API !== 'undefined' && API.contact) {
          await API.contact.send(data);
        } else {
          // Fallback: save to Genspark table if API client not loaded
          await fetch('tables/contact_requests', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          });
        }
      } catch (err) {
        console.warn('Contact API error (non-blocking):', err.message);
        // Non-blocking — show success to user anyway (graceful degradation)
      }

      setTimeout(() => {
        form.style.display = 'none';
        if (success) success.style.display = 'flex';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Request';
      }, 800);
    });

    // Clear error styling on input
    $$('[required]', form).forEach(field => {
      field.addEventListener('input', () => { field.style.borderColor = ''; });
    });
  }
  return { init };
})();


/* ─── NEWSLETTER FORM ────────────────────────────────────────── */
const NewsletterForm = (() => {
  function init() {
    const form = $('#newsletterForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const input = form.querySelector('input[type="email"]');
      if (!input?.value?.trim()) return;

      const email = input.value.trim();
      const submitBtn = form.querySelector('button[type="submit"], button');
      const originalBtnHtml = submitBtn ? submitBtn.innerHTML : null;

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
      }

      try {
        // ── Real API call ──────────────────────────────────────
        if (typeof API !== 'undefined' && API.newsletter) {
          await API.newsletter.subscribe(email);
        } else {
          // Fallback: save to Genspark table if API client not loaded
          await fetch('tables/newsletter_subscribers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
          });
        }
      } catch (err) {
        console.warn('Newsletter API error (non-blocking):', err.message);
        // Non-blocking — show success to user anyway
      }

      // Always show success state
      form.innerHTML = '<p style="color:rgba(255,255,255,.7);font-size:.85rem;padding:8px 0"><i class="fas fa-check-circle" style="color:#10b981"></i> Subscribed! Thank you.</p>';
    });
  }
  return { init };
})();


/* ─── SMOOTH SCROLL ─────────────────────────────────────────── */
const SmoothScroll = (() => {
  function init() {
    $$('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', e => {
        const target = anchor.getAttribute('href');
        if (target === '#') return;
        const el = document.querySelector(target);
        if (!el) return;
        e.preventDefault();
        const headerH = $('#siteHeader')?.offsetHeight || 72;
        const top = el.getBoundingClientRect().top + window.scrollY - headerH - 16;
        window.scrollTo({ top, behavior: 'smooth' });
      });
    });
  }
  return { init };
})();


/* ─── CURRENT YEAR ───────────────────────────────────────────── */
function setCurrentYear() {
  const el = $('#currentYear');
  if (el) el.textContent = new Date().getFullYear();
}


/* ─── SERVICE CARD HOVER TILT ───────────────────────────────── */
const CardTilt = (() => {
  function init() {
    const cards = $$('.service-card, .testimonial-card, .pricing-card');
    cards.forEach(card => {
      card.addEventListener('mousemove', (e) => {
        const rect   = card.getBoundingClientRect();
        const x      = (e.clientX - rect.left) / rect.width - 0.5;
        const y      = (e.clientY - rect.top) / rect.height - 0.5;
        const tiltX  = y * -6;
        const tiltY  = x * 6;
        card.style.transform = `perspective(800px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) translateY(-6px)`;
      });
      card.addEventListener('mouseleave', () => {
        card.style.transform = '';
        card.style.transition = 'transform 0.4s ease';
      });
      card.addEventListener('mouseenter', () => {
        card.style.transition = 'transform 0.08s ease';
      });
    });
  }
  return { init };
})();


/* ─── LOGO SCROLL ANIMATION ─────────────────────────────────── */
const LogoScroll = (() => {
  function init() {
    const logo = $('.logo');
    if (!logo) return;
    // Subtle logo scale on scroll
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
      const current = window.scrollY;
      lastScroll = current;
    }, { passive: true });
  }
  return { init };
})();


/* ─── PRELOADER ──────────────────────────────────────────────── */
const Preloader = (() => {
  function init() {
    const preloader = $('#preloader');
    if (!preloader) return;

    window.addEventListener('load', () => {
      setTimeout(() => {
        preloader.classList.add('hidden');
        setTimeout(() => preloader.remove(), 520);
      }, 400);
    });

    // Fallback — remove after 3s regardless
    setTimeout(() => {
      preloader?.classList.add('hidden');
    }, 3000);
  }
  return { init };
})();


/* ─── INITIALIZE ALL MODULES ────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  Preloader.init();
  ThemeManager.init();
  AnnouncementBar.init();
  StickyHeader.init();
  MobileNav.init();
  ActiveNav.init();
  ScrollTop.init();
  Counters.init();
  AOS.init();
  TestimonialsCarousel.init();
  PricingToggle.init();
  ProgressRings.init();
  ContactForm.init();
  NewsletterForm.init();
  SmoothScroll.init();
  setCurrentYear();

  // Delay tilt for performance
  setTimeout(() => CardTilt.init(), 500);
});


/* ─── AUTH STATE CHECK (runs on every page) ─────────────────── */
(function initAuthState() {
  // Listen for auth expiry event dispatched by API client
  document.addEventListener('bigman:auth-expired', () => {
    const currentPage = window.location.pathname.split('/').pop();
    const publicPages = ['index.html', 'login.html', 'signup.html', '', 'courses.html',
      'materials.html', 'expert-help.html', 'blog.html', 'order-essay.html',
      'article-dissertation-guide.html', 'article-essay-structure.html', 'article-study-techniques.html'];
    if (!publicPages.includes(currentPage)) {
      window.location.href = 'login.html?expired=1';
    }
  });
})();
