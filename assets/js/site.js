(() => {
  'use strict';
  document.documentElement.classList.add('js-motion');
  const root = document.documentElement;
  const themeButton = document.querySelector('.theme-toggle');
  const themeIcon = document.querySelector('.theme-toggle__icon');
  const metaTheme = document.querySelector('meta[name="theme-color"]');

  function setTheme(theme, persist = true) {
    root.dataset.theme = theme;
    if (persist) {
      try { localStorage.setItem('payless-theme', theme); } catch (_) {}
    }
    if (themeIcon) themeIcon.textContent = theme === 'dark' ? '☼' : '◐';
    if (themeButton) {
      themeButton.setAttribute('aria-label', theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
      themeButton.setAttribute('aria-pressed', String(theme === 'dark'));
    }
    if (metaTheme) metaTheme.setAttribute('content', theme === 'dark' ? '#151815' : '#f3f2ed');
  }
  setTheme(root.dataset.theme || 'light', false);
  themeButton?.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));

  const menuButton = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.primary-nav');
  menuButton?.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') === 'true';
    menuButton.setAttribute('aria-expanded', String(!open));
    menuButton.setAttribute('aria-label', open ? 'Open navigation' : 'Close navigation');
    nav?.classList.toggle('is-open', !open);
    document.body.classList.toggle('menu-open', !open);
  });
  nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    nav.classList.remove('is-open');
    menuButton?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('menu-open');
  }));

  // Multi-machine selection helpers: card buttons check the matching request form option.
  function selectEquipment(name) {
    const options = [...document.querySelectorAll('[data-equipment-option], .equipment-choice input[type="checkbox"]')];
    const matching = options.find(input => input.value === name);
    if (matching) {
      matching.checked = true;
      matching.closest('.choice-chip')?.classList.add('is-selected');
    }
  }
  document.querySelectorAll('[data-add-equipment]').forEach(button => button.addEventListener('click', () => {
    selectEquipment(button.dataset.addEquipment || '');
    const request = document.querySelector('#request');
    request?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }));
  document.querySelectorAll('[data-add-link]').forEach(link => link.addEventListener('click', () => {
    try { sessionStorage.setItem('payless-prefill-equipment', link.dataset.addLink || ''); } catch (_) {}
  }));
  try {
    const prefill = sessionStorage.getItem('payless-prefill-equipment');
    if (prefill) {
      selectEquipment(prefill);
      sessionStorage.removeItem('payless-prefill-equipment');
    }
  } catch (_) {}

  document.querySelectorAll('.choice-chip input').forEach(input => {
    const sync = () => input.closest('.choice-chip')?.classList.toggle('is-selected', input.checked);
    input.addEventListener('change', sync);
    sync();
  });

  document.querySelectorAll('.request-form').forEach(form => form.addEventListener('submit', event => {
    const choices = form.querySelectorAll('input[name="equipment[]"]:checked');
    const error = form.querySelector('[data-equipment-error]');
    if (form.querySelector('input[name="form_type"]')?.value === 'equipment' && choices.length === 0) {
      event.preventDefault();
      if (error) error.hidden = false;
      form.querySelector('.equipment-choice')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    if (error) error.hidden = true;
  }));

  // Lightweight contact-page tab navigation.
  const tabLinks = document.querySelectorAll('.contact-tabs a');
  tabLinks.forEach(link => link.addEventListener('click', () => {
    tabLinks.forEach(tab => tab.classList.toggle('is-active', tab === link));
  }));

  // Honor reduced-motion preferences; enhance rather than block core navigation.
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!reduceMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -35px 0px' });
    document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));
  } else {
    document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-visible'));
  }

  // GSAP + ScrollTrigger reveal polish.
  window.addEventListener('load', () => {
    if (reduceMotion || !window.gsap) return;
    if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);
    gsap.from('.hero__visual', { opacity: 0, y: 24, duration: 0.9, ease: 'power2.out', delay: 0.12 });
    if (window.ScrollTrigger) {
      gsap.utils.toArray('.section-heading, .request-intro, .gravel-feature__content, .local-layout').forEach(el => {
        gsap.from(el, { opacity: 0, y: 24, duration: 0.7, ease: 'power2.out', scrollTrigger: { trigger: el, start: 'top 86%', once: true } });
      });
    }
  });

  // Lenis smooth scrolling, with a native-scroll fallback.
  window.addEventListener('load', () => {
    if (reduceMotion || !window.Lenis) return;
    const lenis = new Lenis({ duration: 1.05, smoothWheel: true, wheelMultiplier: 0.9 });
    function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
    requestAnimationFrame(raf);
    document.querySelectorAll('a[href^="#"]').forEach(link => link.addEventListener('click', event => {
      const target = document.querySelector(link.getAttribute('href'));
      if (target) { event.preventDefault(); lenis.scrollTo(target, { offset: -88 }); }
    }));
  });

  const params = new URLSearchParams(window.location.search);
  const status = params.get('status');
  const statusMessages = {
    sent: 'Your request was sent. PayLess will follow up with availability and next steps.',
    'not-configured': 'The request form is not connected yet. Please call (503) 259-0252 to send your request.',
    'send-failed': 'The email could not be sent right now. Please call (503) 259-0252.',
    missing: 'Please complete all required fields before submitting.',
    throttle: 'Please wait a moment before submitting another request.',
    invalid: 'The request could not be processed. Please try again or call PayLess.'
  };
  if (status && statusMessages[status]) {
    const message = document.createElement('div');
    message.className = 'form-status' + (status === 'sent' ? ' form-status--success' : '');
    message.setAttribute('role', 'status');
    message.textContent = statusMessages[status];
    const target = document.querySelector('.contact-form-wrap') || document.querySelector('.request-form');
    target?.prepend(message);
    if (status === 'sent') message.scrollIntoView({ block: 'center' });
  }
})();
