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
})();
