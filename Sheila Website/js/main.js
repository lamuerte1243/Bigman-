/* ============================================================
   SHEILA THE WRITER — Premium Interactions
   Ultra-luxury dark mode microinteractions
   ============================================================ */
'use strict';

// ── Sticky header ──────────────────────────────────────────────
const header = document.getElementById('site-header');
if (header) {
  const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ── Mobile menu ────────────────────────────────────────────────
const toggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');
if (toggle && navLinks) {
  toggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    toggle.classList.toggle('open');
    // Prevent body scroll when menu open
    document.body.style.overflow = navLinks.classList.contains('open') ? 'hidden' : '';
  });
  document.addEventListener('click', (e) => {
    if (!toggle.contains(e.target) && !navLinks.contains(e.target)) {
      navLinks.classList.remove('open');
      toggle.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
  // Mobile dropdown accordion
  document.querySelectorAll('.has-dropdown > a').forEach(a => {
    a.addEventListener('click', (e) => {
      if (window.innerWidth < 768) {
        e.preventDefault();
        a.closest('.has-dropdown').classList.toggle('open');
      }
    });
  });
}

// ── Scroll reveal (IntersectionObserver) ─────────────────────
if ('IntersectionObserver' in window) {
  const revealIO = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        revealIO.unobserve(e.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

  document.querySelectorAll('.reveal').forEach(el => revealIO.observe(el));
}

// ── Fade-in-up animation trigger ──────────────────────────────
if ('IntersectionObserver' in window) {
  const fadeIO = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.animationPlayState = 'running';
        fadeIO.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-in-up').forEach(el => {
    el.style.animationPlayState = 'paused';
    fadeIO.observe(el);
  });
}

// ── Stat counter animation ─────────────────────────────────────
function animateCount(el) {
  const target  = parseFloat(el.dataset.count);
  const suffix  = el.dataset.suffix || '';
  const prefix  = el.dataset.prefix || '';
  const duration = 2200;
  const start   = performance.now();
  const isFloat = target % 1 !== 0;

  const step = (now) => {
    const p      = Math.min((now - start) / duration, 1);
    const eased  = 1 - Math.pow(1 - p, 4); // quartic ease out
    const value  = target * eased;
    el.textContent = prefix + (isFloat ? value.toFixed(1) : Math.round(value)) + suffix;
    if (p < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

if ('IntersectionObserver' in window) {
  const countIO = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        animateCount(e.target);
        countIO.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('[data-count]').forEach(el => countIO.observe(el));
}

// ── FAQ Accordion ──────────────────────────────────────────────
document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', () => {
    const item   = q.closest('.faq-item');
    const answer = item.querySelector('.faq-answer');
    const isOpen = item.classList.contains('open');

    // Close all
    document.querySelectorAll('.faq-item.open').forEach(el => {
      el.classList.remove('open');
      el.querySelector('.faq-answer').style.maxHeight = '0';
    });

    // Toggle clicked
    if (!isOpen) {
      item.classList.add('open');
      answer.style.maxHeight = answer.scrollHeight + 40 + 'px';
    }
  });
});

// ── Filter Tabs ────────────────────────────────────────────────
document.querySelectorAll('.filter-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    const group = tab.closest('[data-filter-group]');
    if (!group) return;
    group.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    const filter = tab.dataset.filter;
    group.querySelectorAll('[data-category]').forEach(card => {
      if (filter === 'all' || card.dataset.category === filter) {
        card.style.display = '';
        // re-trigger reveal
        setTimeout(() => card.classList.add('visible'), 10);
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// ── Parallax hero glow ─────────────────────────────────────────
(function() {
  const hero = document.querySelector('.hero');
  if (!hero) return;
  let ticking = false;

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        const y = window.scrollY;
        const parallaxAmount = y * 0.3;
        const opacityAmount = 1 - (y / 600);

        const text = hero.querySelector('.hero-text');
        if (text) {
          text.style.transform = `translateY(${parallaxAmount * 0.5}px)`;
          text.style.opacity = Math.max(0, opacityAmount).toString();
        }
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
})();

// ── Cursor glow on premium cards ──────────────────────────────
(function() {
  const cards = document.querySelectorAll(
    '.course-card, .testimonial-card, .category-card, .tutor-card, .material-card'
  );

  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      card.style.setProperty('--mouse-x', x + '%');
      card.style.setProperty('--mouse-y', y + '%');
    });
  });
})();

// ── Smooth text splitting for hero (letter stagger) ──────────
(function() {
  const heroTitle = document.querySelector('.hero-title');
  if (!heroTitle) return;

  // Only on desktop
  if (window.innerWidth < 768) return;

  // Add entrance animation class
  heroTitle.style.opacity = '1';
})();

// ── Contact / Booking form AJAX ────────────────────────────────
document.querySelectorAll('form[data-ajax]').forEach(form => {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn  = form.querySelector('[type="submit"]');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';
    btn.disabled = true;

    try {
      const res  = await fetch(form.action, { method: 'POST', body: new FormData(form) });
      const data = await res.json();
      showFormMsg(form, data.success ? 'success' : 'error', data.message);
      if (data.success) form.reset();
    } catch (_) {
      showFormMsg(form, 'error', 'Something went wrong. Please try again.');
    } finally {
      btn.innerHTML = orig;
      btn.disabled = false;
    }
  });
});

function showFormMsg(form, type, msg) {
  let el = form.querySelector('.form-response');
  if (!el) { el = document.createElement('div'); form.prepend(el); }
  el.className = 'alert alert-' + type + ' form-response';
  el.innerHTML = (type === 'success'
    ? '<i class="fa-solid fa-circle-check"></i> '
    : '<i class="fa-solid fa-circle-exclamation"></i> ') + msg;
  el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  setTimeout(() => el?.remove(), 7000);
}

// ── Smooth scroll for anchor links ─────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', (e) => {
    const id     = a.getAttribute('href').slice(1);
    const target = document.getElementById(id);
    if (target) {
      e.preventDefault();
      const top = target.getBoundingClientRect().top + window.scrollY - 96;
      window.scrollTo({ top, behavior: 'smooth' });
    }
  });
});

// ── Ambient glow drift animation on hero ──────────────────────
(function() {
  const glows = document.querySelectorAll('.hero::before, .hero::after');
  // CSS handles the animation via keyframes
})();

// ── Staggered card reveals ────────────────────────────────────
(function() {
  const grids = document.querySelectorAll('.course-grid, .tutor-grid, .testimonial-grid, .category-grid, .blog-grid, .materials-grid');

  if (!('IntersectionObserver' in window)) return;

  const gridIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const children = entry.target.querySelectorAll(':scope > *');
        children.forEach((child, i) => {
          child.style.opacity = '0';
          child.style.transform = 'translateY(20px)';
          child.style.transition = `opacity 0.6s ease ${i * 0.08}s, transform 0.6s ease ${i * 0.08}s`;
          requestAnimationFrame(() => {
            setTimeout(() => {
              child.style.opacity = '';
              child.style.transform = '';
            }, 50);
          });
        });
        gridIO.unobserve(entry.target);
      }
    });
  }, { threshold: 0.05 });

  grids.forEach(g => gridIO.observe(g));
})();

// ── Active nav link highlight ─────────────────────────────────
(function() {
  const path = window.location.pathname;
  document.querySelectorAll('.nav-links a').forEach(a => {
    if (a.href && a.href !== '#' && path.includes(a.getAttribute('href').replace('../', ''))) {
      a.classList.add('active');
    }
  });
})();

// ── Dark / Light Mode Toggle — Premium Pill Switch ────────────
(function() {
  const STORAGE_KEY = 'sheila-theme';
  const html = document.documentElement;
  const toggleBtn = document.getElementById('theme-toggle');

  // Apply saved preference immediately (before paint)
  const savedTheme = localStorage.getItem(STORAGE_KEY);
  if (savedTheme) {
    html.setAttribute('data-theme', savedTheme);
  } else {
    html.setAttribute('data-theme', 'dark');
  }

  // ── Build the premium pill toggle UI ──
  function buildToggleUI(btn, currentTheme) {
    if (!btn) return;
    btn.innerHTML = '';

    const track = document.createElement('span');
    track.className = 'tt-track';

    // Light option
    const lightPill = document.createElement('span');
    lightPill.className = 'tt-option tt-option-light' + (currentTheme === 'light' ? ' tt-active' : '');
    lightPill.setAttribute('aria-label', 'Light mode');
    lightPill.textContent = 'Light';

    // Dark option
    const darkPill = document.createElement('span');
    darkPill.className = 'tt-option tt-option-dark' + (currentTheme === 'dark' ? ' tt-active' : '');
    darkPill.setAttribute('aria-label', 'Dark mode');
    darkPill.textContent = 'Dark';

    track.appendChild(lightPill);
    track.appendChild(darkPill);
    btn.appendChild(track);
  }

  function applyTheme(theme) {
    // Instant switch — no transition lag
    html.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
    // Update pill UI
    buildToggleUI(toggleBtn, theme);
  }

  // Build initial UI
  buildToggleUI(toggleBtn, html.getAttribute('data-theme') || 'dark');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const current = html.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
    });
  }
})();

// ── Back to Top Button ────────────────────────────────────────
(function() {
  const btn = document.getElementById('back-to-top');
  if (!btn) return;

  const SHOW_THRESHOLD = 300;

  const onScroll = () => {
    if (window.scrollY > SHOW_THRESHOLD) {
      btn.classList.add('visible');
    } else {
      btn.classList.remove('visible');
    }
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // run on load

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

/* ============================================================
   SHEILA THE WRITER — ULTRA PREMIUM INTERACTIONS v2
   Advanced micro-interactions, magnetic buttons, tilt effect,
   enhanced parallax, ripple clicks, cursor glow, scroll progress
   ============================================================ */

// ── Scroll Progress Bar ────────────────────────────────────────
(function() {
  // Inject the progress bar element into DOM
  const bar = document.createElement('div');
  bar.id = 'scroll-progress';
  bar.style.cssText = [
    'position:fixed',
    'top:0',
    'left:0',
    'height:2px',
    'width:0%',
    'background:linear-gradient(90deg,#2346FF 0%,#5B7FFF 50%,#C9A84C 100%)',
    'z-index:9999',
    'pointer-events:none',
    'transition:width 0.1s linear',
    'will-change:width',
  ].join(';');
  document.body.appendChild(bar);

  window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    bar.style.width = Math.min(pct, 100) + '%';
  }, { passive: true });
})();

// ── Magnetic Button Effect ─────────────────────────────────────
(function() {
  const magnetEls = document.querySelectorAll('.btn-primary, .btn-gold');

  magnetEls.forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const rect = btn.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      const dx = (e.clientX - cx) * 0.22;
      const dy = (e.clientY - cy) * 0.22;
      btn.style.transform = `translate(${dx}px, ${dy}px) scale(1.03)`;
    });

    btn.addEventListener('mouseleave', () => {
      btn.style.transform = '';
    });

    // Track mouse position for ripple glow
    btn.addEventListener('mousemove', (e) => {
      const rect = btn.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      btn.style.setProperty('--mouse-x', x + '%');
      btn.style.setProperty('--mouse-y', y + '%');
    });
  });
})();

// ── Ripple Click Effect on all buttons ────────────────────────
(function() {
  document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const rect = btn.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const ripple = document.createElement('span');
      ripple.style.cssText = [
        'position:absolute',
        `left:${x}px`,
        `top:${y}px`,
        'width:0',
        'height:0',
        'border-radius:50%',
        'background:rgba(255,255,255,0.25)',
        'transform:translate(-50%,-50%) scale(0)',
        'animation:ripple-expand 0.6s ease-out forwards',
        'pointer-events:none',
        'z-index:10',
      ].join(';');

      // Inject ripple keyframes if not already done
      if (!document.getElementById('ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
          @keyframes ripple-expand {
            from { width: 0; height: 0; opacity: 0.8; }
            to   { width: 300px; height: 300px; opacity: 0; }
          }
        `;
        document.head.appendChild(style);
      }

      btn.appendChild(ripple);
      setTimeout(() => ripple.remove(), 700);
    });
  });
})();

// ── 3D Card Tilt Effect ────────────────────────────────────────
(function() {
  const tiltCards = document.querySelectorAll(
    '.course-card, .tutor-card, .category-card, .glass-card, ' +
    '.booking-type-card, .success-step-card, .pricing-card'
  );

  const MAX_TILT = 6; // degrees

  tiltCards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      const rotX = ((e.clientY - centerY) / (rect.height / 2)) * -MAX_TILT;
      const rotY = ((e.clientX - centerX) / (rect.width / 2)) * MAX_TILT;

      card.style.transform =
        `perspective(1000px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateZ(4px)`;
      card.style.transition = 'transform 0.1s linear, box-shadow 0.3s ease';
    });

    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
      card.style.transition =
        'transform 0.5s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.45s ease';
    });
  });
})();

// ── Enhanced Stagger Grids — more refined timing ───────────────
(function() {
  const grids = document.querySelectorAll(
    '.course-grid, .tutor-grid, .testimonial-grid, .category-grid, ' +
    '.blog-grid, .materials-grid, .booking-type-grid, .subject-select-grid, ' +
    '.success-steps-grid, .schedule-grid'
  );
  if (!('IntersectionObserver' in window)) return;

  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const children = [...entry.target.querySelectorAll(':scope > *')];
      children.forEach((child, i) => {
        child.style.opacity = '0';
        child.style.transform = 'translateY(28px) scale(0.97)';
        child.style.transition = 'none';
        requestAnimationFrame(() => {
          setTimeout(() => {
            child.style.transition = `
              opacity 0.7s cubic-bezier(0.25,0.46,0.45,0.94) ${i * 0.10}s,
              transform 0.7s cubic-bezier(0.34,1.56,0.64,1) ${i * 0.10}s
            `;
            child.style.opacity = '';
            child.style.transform = '';
          }, 40);
        });
      });
      io.unobserve(entry.target);
    });
  }, { threshold: 0.04 });

  grids.forEach(g => io.observe(g));
})();

// ── Enhanced Cursor Glow — covers more element types ──────────
(function() {
  const targets = document.querySelectorAll(
    '.course-card, .testimonial-card, .category-card, ' +
    '.tutor-card, .material-card, .glass-card, #ai-tutor, #human, ' +
    '.faq-item, .stat-item, .float-card, .btn-primary, .btn-gold, ' +
    '.booking-type-card, .subject-option, .success-step-card, ' +
    '.curriculum-item, .pricing-card, .checkout-form-card'
  );

  targets.forEach(el => {
    el.addEventListener('mousemove', (e) => {
      const rect = el.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      el.style.setProperty('--mouse-x', x + '%');
      el.style.setProperty('--mouse-y', y + '%');
    });
  });
})();

// ── Section Heading Stagger Reveal ────────────────────────────
(function() {
  if (!('IntersectionObserver' in window)) return;

  const headIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const head = entry.target;
      const children = [
        head.querySelector('.eyebrow'),
        head.querySelector('h2'),
        head.querySelector('p'),
      ].filter(Boolean);

      children.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'none';
        requestAnimationFrame(() => {
          setTimeout(() => {
            el.style.transition = `
              opacity 0.7s ease ${i * 0.14}s,
              transform 0.7s cubic-bezier(0.34,1.56,0.64,1) ${i * 0.14}s
            `;
            el.style.opacity = '';
            el.style.transform = '';
          }, 30);
        });
      });

      headIO.unobserve(head);
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.section-head').forEach(el => headIO.observe(el));
})();

// ── Tutoring Panel — animated entrance ────────────────────────
(function() {
  const panels = document.querySelectorAll('#ai-tutor, #human');
  if (!panels.length || !('IntersectionObserver' in window)) return;

  const panelIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const panel = entry.target;
      const index = panel.id === 'ai-tutor' ? 0 : 1;

      panel.style.opacity = '0';
      panel.style.transform = index === 0
        ? 'translateX(-32px)' : 'translateX(32px)';
      panel.style.transition = 'none';

      requestAnimationFrame(() => {
        setTimeout(() => {
          panel.style.transition = `
            opacity 0.85s cubic-bezier(0.25,0.46,0.45,0.94) ${index * 0.18}s,
            transform 0.85s cubic-bezier(0.34,1.56,0.64,1) ${index * 0.18}s
          `;
          panel.style.opacity = '';
          panel.style.transform = '';
        }, 50);
      });

      panelIO.unobserve(panel);
    });
  }, { threshold: 0.1 });

  panels.forEach(p => panelIO.observe(p));
})();

// ── Tutor Stat Items — count up on enter ──────────────────────
(function() {
  const statItems = document.querySelectorAll('.tutor-stat strong');
  if (!statItems.length || !('IntersectionObserver' in window)) return;

  const statIO = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const raw = el.textContent.trim();
      // Animate scale-up entrance
      el.style.transform = 'scale(0.7)';
      el.style.opacity = '0';
      requestAnimationFrame(() => {
        setTimeout(() => {
          el.style.transition = 'transform 0.6s cubic-bezier(0.34,1.56,0.64,1), opacity 0.5s ease';
          el.style.transform = '';
          el.style.opacity = '';
        }, 80);
      });
      statIO.unobserve(el);
    });
  }, { threshold: 0.5 });

  statItems.forEach(el => statIO.observe(el));
})();

// ── Smooth Header transparency on scroll ──────────────────────
(function() {
  const hdr = document.getElementById('site-header');
  if (!hdr) return;
  let lastY = 0;

  window.addEventListener('scroll', () => {
    const y = window.scrollY;
    // Hide header when scrolling down fast, show when scrolling up
    if (y > lastY + 5 && y > 200) {
      hdr.style.transform = 'translateY(-100%)';
      hdr.style.transition = 'transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94)';
    } else if (y < lastY - 5 || y < 100) {
      hdr.style.transform = 'translateY(0)';
      hdr.style.transition = 'transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
    }
    lastY = y;
  }, { passive: true });
})();

// ── Float card enhanced parallax on scroll ────────────────────
(function() {
  const fcCards = document.querySelectorAll('.fc-1, .fc-2, .fc-3');
  if (!fcCards.length) return;

  const speeds = [0.06, -0.04, 0.08];
  let ticking = false;

  window.addEventListener('scroll', () => {
    if (ticking) return;
    requestAnimationFrame(() => {
      const y = window.scrollY;
      fcCards.forEach((card, i) => {
        const shift = y * speeds[i];
        card.style.setProperty('--parallax-y', shift + 'px');
      });
      ticking = false;
    });
    ticking = true;
  }, { passive: true });
})();

// ── Partner logos — stagger entrance ──────────────────────────
(function() {
  const strip = document.querySelector('.partners-strip, .partner-logos');
  if (!strip || !('IntersectionObserver' in window)) return;

  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const logos = entry.target.querySelectorAll('.partner-logo, img');
      logos.forEach((logo, i) => {
        logo.style.opacity = '0';
        logo.style.transform = 'translateY(12px)';
        logo.style.transition = 'none';
        setTimeout(() => {
          logo.style.transition = `opacity 0.5s ease ${i*0.06}s, transform 0.5s ease ${i*0.06}s`;
          logo.style.opacity = '';
          logo.style.transform = '';
        }, 50);
      });
      io.unobserve(entry.target);
    });
  }, { threshold: 0.2 });

  io.observe(strip);
})();

// ── Page entrance animation ────────────────────────────────────
(function() {
  document.body.style.opacity = '0';
  document.body.style.transition = 'opacity 0.5s ease';
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      document.body.style.opacity = '1';
    });
  });
})();

// ── Clear Street-inspired page choreography ─────────────────────
(function () {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) return;

  // Subtle image depth follows the viewport without changing content flow.
  const media = document.querySelector('.hero-img-wrap');
  if (media) {
    window.addEventListener('pointermove', (event) => {
      const x = (event.clientX / window.innerWidth - 0.5) * 10;
      const y = (event.clientY / window.innerHeight - 0.5) * 8;
      media.style.transform = `translate3d(${x}px, ${y}px, 0)`;
    }, { passive: true });
  }

  // Add an index to cards so CSS/JS entrances retain a consistent rhythm.
  document.querySelectorAll('.category-grid, .course-grid, .tutor-grid, .blog-grid, .testimonial-grid').forEach((grid) => {
    [...grid.children].forEach((card, index) => card.style.setProperty('--card-index', index));
  });
})();
