(() => {
  'use strict';
  document.documentElement.classList.add('js-motion');
  const root = document.documentElement;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
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
    if (metaTheme) metaTheme.setAttribute('content', theme === 'dark' ? '#101712' : '#f4f6f2');
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

  const megaTrigger = document.querySelector('.nav-mega-trigger');
  const megaMenu = document.querySelector('.mega-menu');
  const megaItem = document.querySelector('.nav-item--mega');
  const siteHeader = document.querySelector('.site-header');
  let megaCloseTimer;
  const positionMegaMenu = () => {
    if (siteHeader && megaMenu && window.innerWidth > 780) megaMenu.style.setProperty('--mega-menu-top', `${siteHeader.getBoundingClientRect().bottom - 8}px`);
  };
  const openMegaMenu = () => {
    clearTimeout(megaCloseTimer);
    positionMegaMenu();
    megaTrigger?.setAttribute('aria-expanded', 'true');
    megaMenu?.classList.add('is-open');
  };
  const scheduleMegaClose = () => {
    clearTimeout(megaCloseTimer);
    megaCloseTimer = setTimeout(() => {
      if (!megaItem?.matches(':hover') && !megaMenu?.matches(':hover')) {
        megaTrigger?.setAttribute('aria-expanded', 'false');
        megaMenu?.classList.remove('is-open');
      }
    }, 180);
  };
  // Keep the mobile dropdown open after a tap. Hover-based close behavior is
  // desktop-only; on touch devices, close via a second tap or outside click.
  megaTrigger?.addEventListener('click', event => {
    event.preventDefault();
    const open = megaTrigger.getAttribute('aria-expanded') === 'true';
    if (open) {
      megaTrigger.setAttribute('aria-expanded', 'false');
      megaMenu?.classList.remove('is-open');
    } else openMegaMenu();
  });
  window.addEventListener('resize', positionMegaMenu);
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    megaItem?.addEventListener('mouseenter', openMegaMenu);
    megaItem?.addEventListener('mouseleave', scheduleMegaClose);
    megaMenu?.addEventListener('mouseenter', openMegaMenu);
    megaMenu?.addEventListener('mouseleave', scheduleMegaClose);
  }
  megaItem?.addEventListener('focusin', openMegaMenu);
  document.addEventListener('click', event => {
    if (megaMenu && !megaMenu.closest('.nav-item--mega')?.contains(event.target)) {
      megaTrigger?.setAttribute('aria-expanded', 'false');
      megaMenu.classList.remove('is-open');
    }
  });

  // Multi-machine selection helpers: card buttons check the matching request form option.
  function syncEquipmentPicker(picker) {
    const selected = picker.querySelectorAll('input[name="equipment[]"]:checked');
    const summary = picker.querySelector('[data-equipment-summary]');
    if (summary) summary.textContent = selected.length ? `${selected.length} selected` : 'Choose equipment';
  }

  function selectEquipment(name) {
    const options = [...document.querySelectorAll('[data-equipment-option], .equipment-choice input[type="checkbox"]')];
    const matching = options.find(input => input.value === name);
    if (matching) {
      matching.checked = true;
      const picker = matching.closest('.equipment-picker');
      if (picker) {
        picker.open = true;
        syncEquipmentPicker(picker);
      }
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

  document.querySelectorAll('.equipment-picker').forEach(picker => {
    picker.querySelectorAll('input[name="equipment[]"]').forEach(input => {
      input.addEventListener('change', () => {
        syncEquipmentPicker(picker);
        const error = picker.closest('.equipment-choice')?.querySelector('[data-equipment-error]');
        if (error) error.hidden = picker.querySelectorAll('input[name="equipment[]"]:checked').length > 0;
      });
    });
    syncEquipmentPicker(picker);
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

  // Contact request tabs switch between the two forms instead of only changing the underline.
  const tabLinks = [...document.querySelectorAll('.contact-tabs a[role="tab"]')];
  const requestForms = tabLinks
    .map(tab => document.getElementById(tab.getAttribute('aria-controls')))
    .filter(Boolean);

  function activateContactForm(tab) {
    const targetId = tab.getAttribute('aria-controls');
    tabLinks.forEach(item => {
      const active = item === tab;
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
    });
    requestForms.forEach(form => {
      form.hidden = form.id !== targetId;
    });
  }

  if (tabLinks.length && requestForms.length) {
    tabLinks.forEach(tab => tab.addEventListener('click', event => {
      event.preventDefault();
      activateContactForm(tab);
    }));
    activateContactForm(tabLinks.find(tab => tab.classList.contains('is-active')) || tabLinks[0]);
  }

  // Homepage machine finder and fleet share one category-filtered equipment list.
  const machineTabs = [...document.querySelectorAll('.equipment-tab[data-machine-filter]')];
  const machineCards = [...document.querySelectorAll('[data-machine-category]')];
  const machineList = document.querySelector('#equipment-list');
  const machineDescription = document.querySelector('.equipment-tab-detail');
  function activateMachineTab(tab, moveFocus = false) {
    const filter = tab.dataset.machineFilter || 'all';
    machineTabs.forEach(item => {
      const active = item === tab;
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
    });
    machineCards.forEach(card => { card.hidden = filter !== 'all' && card.dataset.machineCategory !== filter; });
    if (machineList) machineList.setAttribute('aria-labelledby', tab.id);
    if (machineDescription) machineDescription.textContent = tab.dataset.machineDescription || '';
    if (moveFocus) tab.focus();
  }
  machineTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => activateMachineTab(tab));
    tab.addEventListener('keydown', event => {
      let nextIndex = index;
      if (event.key === 'ArrowRight') nextIndex = (index + 1) % machineTabs.length;
      else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + machineTabs.length) % machineTabs.length;
      else if (event.key === 'Home') nextIndex = 0;
      else if (event.key === 'End') nextIndex = machineTabs.length - 1;
      else return;
      event.preventDefault();
      activateMachineTab(machineTabs[nextIndex], true);
    });
  });
  document.querySelectorAll('[data-machine-nav-filter]').forEach(link => link.addEventListener('click', event => {
    const filter = link.dataset.machineNavFilter;
    const machineTarget = document.querySelector(`.equipment-tab[data-machine-filter="${filter}"]`);
    const catalogueTarget = document.querySelector(`.catalogue-tab[data-catalogue-filter="${filter}"]`);
    if (!machineTarget && !catalogueTarget) return;
    event.preventDefault();
    if (machineTarget) activateMachineTab(machineTarget);
    else activateCatalogueTab(catalogueTarget);
    document.querySelector('#equipment')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    megaTrigger?.setAttribute('aria-expanded', 'false');
    megaMenu?.classList.remove('is-open');
  }));
if (machineTabs[0]) activateMachineTab(machineTabs[0]);

  // Equipment catalogue category tabs.
  const catalogueTabs = [...document.querySelectorAll('[data-catalogue-tab]')];
  const cataloguePanels = catalogueTabs
    .map(tab => document.getElementById(tab.getAttribute('aria-controls')))
    .filter(Boolean);
  function activateCatalogueTab(tab, moveFocus = false) {
    const panelId = tab.getAttribute('aria-controls');
    catalogueTabs.forEach(item => {
      const active = item === tab;
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
    });
    cataloguePanels.forEach(panel => {
      const active = panel.id === panelId;
      panel.hidden = !active;
      panel.classList.toggle('is-active', active);
    });
    if (moveFocus) tab.focus();
  }
  catalogueTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => activateCatalogueTab(tab));
    tab.addEventListener('keydown', event => {
      let nextIndex = index;
      if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextIndex = (index + 1) % catalogueTabs.length;
      else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextIndex = (index - 1 + catalogueTabs.length) % catalogueTabs.length;
      else if (event.key === 'Home') nextIndex = 0;
      else if (event.key === 'End') nextIndex = catalogueTabs.length - 1;
      else return;
      event.preventDefault();
      activateCatalogueTab(catalogueTabs[nextIndex], true);
    });
  });
  if (catalogueTabs[0]) activateCatalogueTab(catalogueTabs.find(tab => tab.getAttribute('aria-selected') === 'true') || catalogueTabs[0]);

  // Honor reduced-motion preferences; enhance rather than block core navigation.
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const revealElements = document.querySelectorAll('[data-reveal], .section-heading, .gravel-feature__content, .local-layout');
  revealElements.forEach(el => el.setAttribute('data-reveal', ''));
  if (!reduceMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.05, rootMargin: '0px 0px -8% 0px' });
    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('is-visible'));
  }

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
    // Critical layout is injected inline so the dialog remains a real viewport popup
    // even if the stylesheet is cached, missing, or overridden by page styles.
    modal.style.cssText = 'position:fixed!important;inset:0!important;z-index:2147483000!important;display:grid!important;place-items:center!important;padding:22px!important;background:rgba(8,12,8,.72)!important;backdrop-filter:blur(5px)!important;overflow-y:auto!important;';
    const modalStyle = document.createElement('style');
    modalStyle.textContent = `
      .request-modal__panel{position:relative!important;display:block!important;width:min(100%,480px)!important;max-height:calc(100vh - 44px)!important;overflow-y:auto!important;margin:auto!important;padding:clamp(28px,6vw,42px)!important;background:var(--surface,#fffefa)!important;color:var(--ink,#1a201a)!important;border:1px solid var(--line,rgba(26,32,26,.16))!important;box-shadow:0 28px 90px #0008!important;transform:none!important;opacity:1!important;z-index:1!important}
      .request-modal__mark{display:grid!important;place-items:center!important;width:48px!important;height:48px!important;margin:0 0 22px!important;border-radius:50%!important;background:var(--accent,#ffb900)!important;color:var(--accent-ink,#1a2016)!important;font-size:25px!important;font-weight:800!important}
      .request-modal--warning .request-modal__mark{background:#f2dfb8!important;color:#70440b!important}
      .request-modal--error .request-modal__mark{background:#f5d6d2!important;color:#8e211b!important}
      .request-modal__eyebrow{margin:0 0 10px!important;color:var(--muted,#666d63)!important;font:10px 'DM Mono',monospace!important;letter-spacing:.12em!important;text-transform:uppercase!important}
      .request-modal__title{margin:0 0 12px!important;font-size:clamp(27px,5vw,36px)!important;line-height:1.05!important;letter-spacing:-.06em!important}
      .request-modal__message{margin:0!important;color:var(--muted,#666d63)!important;font-size:13px!important;line-height:1.8!important}
      .request-modal__actions{display:flex!important;gap:10px!important;flex-wrap:wrap!important;margin-top:27px!important}
      .request-modal__actions .button{flex:1 1 130px!important}
      .request-modal__close{position:absolute!important;top:14px!important;right:14px!important;display:grid!important;place-items:center!important;width:36px!important;height:36px!important;border:1px solid var(--line,rgba(26,32,26,.16))!important;background:transparent!important;color:inherit!important;font-size:22px!important;cursor:pointer!important}
      @media(max-width:480px){.request-modal{padding:14px!important}.request-modal__panel{max-height:calc(100vh - 28px)!important;padding:28px 22px!important}}
    `;
    document.head.append(modalStyle);
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
      modalStyle.remove();
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
