(() => {
  const root = document.documentElement;
  const button = document.querySelector('[data-admin-theme-toggle]');
  const icon = button?.querySelector('[data-admin-theme-icon]');
  const meta = document.querySelector('meta[name="theme-color"]');

  function applyTheme(theme, persist = false) {
    const selected = theme === 'dark' ? 'dark' : 'light';
    root.dataset.theme = selected;
    if (persist) {
      try { localStorage.setItem('payless-theme', selected); } catch (_) {}
    }
    if (icon) icon.textContent = selected === 'dark' ? '☼' : '◐';
    if (button) {
      button.setAttribute('aria-label', selected === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
      button.setAttribute('aria-pressed', String(selected === 'dark'));
      button.setAttribute('title', selected === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
    }
    if (meta) meta.setAttribute('content', selected === 'dark' ? '#151815' : '#f3f2ed');
  }

  let initial = root.dataset.theme || 'light';
  try {
    const saved = localStorage.getItem('payless-theme');
    if (saved === 'light' || saved === 'dark') initial = saved;
    else if (window.matchMedia('(prefers-color-scheme: dark)').matches) initial = 'dark';
  } catch (_) {}
  applyTheme(initial);
  button?.addEventListener('click', () => applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark', true));

  const sidebarToggle = document.querySelector('[data-admin-sidebar-toggle]');
  const sidebar = document.querySelector('[data-admin-sidebar]');
  const body = document.body;
  function applySidebar(collapsed, persist = false) {
    const isCollapsed = Boolean(collapsed) && window.matchMedia('(min-width: 681px)').matches;
    body.classList.toggle('admin-sidebar-collapsed', isCollapsed);
    if (sidebarToggle) {
      sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
      sidebarToggle.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
      sidebarToggle.setAttribute('title', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
      const icon = sidebarToggle.querySelector('[aria-hidden="true"]');
      if (icon) icon.textContent = isCollapsed ? '›' : '‹';
      const label = sidebarToggle.querySelector('.admin-sidebar-toggle-label');
      if (label) label.textContent = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
    }
    if (persist) {
      try { localStorage.setItem('payless-admin-sidebar-collapsed', isCollapsed ? '1' : '0'); } catch (_) {}
    }
  }
  let sidebarCollapsed = false;
  try { sidebarCollapsed = localStorage.getItem('payless-admin-sidebar-collapsed') === '1'; } catch (_) {}
  applySidebar(sidebarCollapsed);
  sidebarToggle?.addEventListener('click', () => {
    applySidebar(!body.classList.contains('admin-sidebar-collapsed'), true);
  });
  window.addEventListener('resize', () => {
    if (!window.matchMedia('(min-width: 681px)').matches) body.classList.remove('admin-sidebar-collapsed');
    else {
      let saved = false;
      try { saved = localStorage.getItem('payless-admin-sidebar-collapsed') === '1'; } catch (_) {}
      applySidebar(saved);
    }
  }, { passive: true });

  const navLinks = [...document.querySelectorAll('.admin-nav-link[href*="#"]')];
  function syncActiveNav() {
    const hash = window.location.hash;
    const target = hash === '#requests' ? '#requests' : hash === '#access-management' ? '#access-management' : '#dashboard';
    document.querySelectorAll('.admin-nav-link').forEach(link => {
      const href = link.getAttribute('href') || '';
      const active = target === '#dashboard' ? href === '/admin/' : href.endsWith(target);
      link.classList.toggle('is-active', active);
      if (active) link.setAttribute('aria-current', 'page');
      else link.removeAttribute('aria-current');
    });
  }
  syncActiveNav();
  window.addEventListener('hashchange', syncActiveNav);
  navLinks.forEach(link => link.addEventListener('click', () => {
    const href = link.getAttribute('href') || '';
    if (href.includes('#')) {
      const hash = href.slice(href.indexOf('#'));
      if (window.location.hash === hash) syncActiveNav();
    }
  }));
})();
