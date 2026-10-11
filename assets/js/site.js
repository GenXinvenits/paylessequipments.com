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

  // Show a clear, accessible popup after an equipment or gravel request submission.
  const params = new URLSearchParams(window.location.search);
  const status = params.get('status');
  const statusMessages = {
    sent: { type: 'success', title: 'Request received', message: 'Thanks for contacting PayLess. Your request was saved and the team has been notified. We’ll follow up about availability, pricing and next steps.', action: 'Done' },
    'saved-email-not-configured': { type: 'warning', title: 'Request saved', message: 'Your request is safely saved, but email notifications are not set up yet. To make sure the team sees it promptly, please call (503) 259-0252.', action: 'Got it', call: true },
    'saved-email-failed': { type: 'warning', title: 'Request saved', message: 'Your request is safely saved, but the email notification could not be sent. If you need a timely response, please call (503) 259-0252.', action: 'Got it', call: true },
    'database-error': { type: 'error', title: 'Request not saved', message: 'Sorry, we couldn’t save your request. Please try again. If the problem continues, call (503) 259-0252.', action: 'Close', call: true },
    missing: { type: 'error', title: 'A few details are missing', message: 'Please complete the required fields and choose at least one equipment option, then submit the form again.', action: 'Review form' },
    invalid: { type: 'error', title: 'Please check your details', message: 'Some information couldn’t be validated. Please review the form and try again.', action: 'Review form' },
    throttle: { type: 'warning', title: 'One moment, please', message: 'Please wait a few seconds before sending another request.', action: 'Got it' }
  };
  const feedback = statusMessages[status];
  if (feedback) {
    const modal = document.createElement('div');
    modal.className = 'request-modal request-modal--' + feedback.type;
    modal.setAttribute('role', 'presentation');
    modal.innerHTML = `<section class="request-modal__panel" role="dialog" aria-modal="true" aria-labelledby="request-modal-title" aria-describedby="request-modal-message" tabindex="-1">
      <button class="request-modal__close" type="button" aria-label="Close message">×</button>
      <div class="request-modal__mark" aria-hidden="true">${feedback.type === 'success' ? '✓' : feedback.type === 'warning' ? '!' : '×'}</div>
      <p class="request-modal__eyebrow">PAYLESS EQUIPMENT RENTAL</p>
      <h2 class="request-modal__title" id="request-modal-title">${feedback.title}</h2>
      <p class="request-modal__message" id="request-modal-message">${feedback.message}</p>
      <div class="request-modal__actions">${feedback.call ? '<a class="button button--primary" href="tel:5032590252">Call PayLess ↗</a>' : ''}<button class="button ${feedback.call ? 'button--outline' : 'button--primary'}" type="button" data-request-modal-dismiss>${feedback.action}</button></div>
    </section>`;
    document.body.append(modal);
    const panel = modal.querySelector('.request-modal__panel');
    const closeButton = modal.querySelector('.request-modal__close');
    const dismiss = () => {
      modal.remove();
      if (window.history?.replaceState) {
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('status');
        window.history.replaceState({}, '', cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);
      }
    };
    closeButton.addEventListener('click', dismiss);
    modal.querySelector('[data-request-modal-dismiss]').addEventListener('click', dismiss);
    modal.addEventListener('click', event => { if (event.target === modal) dismiss(); });
    document.addEventListener('keydown', function onEscape(event) {
      if (event.key === 'Escape' && document.body.contains(modal)) {
        dismiss();
        document.removeEventListener('keydown', onEscape);
      }
    });
    panel.focus();
  }
})();
