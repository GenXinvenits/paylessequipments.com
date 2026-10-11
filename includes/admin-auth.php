<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function admin_start_session(): void {
  if (session_status() === PHP_SESSION_ACTIVE) return;
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
  session_name('payless_admin_session');
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/admin',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Strict',
  ]);
  ini_set('session.use_strict_mode', '1');
  ini_set('session.use_only_cookies', '1');
  session_start();
}
admin_start_session();

function admin_escape(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function admin_csrf_token(): string {
  if (empty($_SESSION['admin_csrf'])) $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
  return (string) $_SESSION['admin_csrf'];
}
function admin_verify_csrf(): void {
  $sent = (string) ($_POST['_csrf'] ?? '');
  if ($sent === '' || !hash_equals(admin_csrf_token(), $sent)) {
    http_response_code(419);
    exit('This page expired. Please go back, refresh, and try again.');
  }
}
function admin_current_user(): ?array {
  return isset($_SESSION['admin_user']) && is_array($_SESSION['admin_user']) ? $_SESSION['admin_user'] : null;
}
function admin_require_login(): array {
  $user = admin_current_user();
  if (!$user) {
    header('Location: /admin/login.php');
    exit;
  }
  try {
    $stmt = payless_db()->prepare('SELECT id,email,display_name,role,is_active FROM admin_users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $fresh = $stmt->fetch();
    if (!$fresh || !(int) $fresh['is_active']) {
      $_SESSION = [];
      session_destroy();
      header('Location: /admin/login.php?status=expired');
      exit;
    }
    $_SESSION['admin_user'] = [
      'id' => (int) $fresh['id'],
      'email' => (string) $fresh['email'],
      'display_name' => (string) $fresh['display_name'],
      'role' => (string) $fresh['role'],
    ];
    return $_SESSION['admin_user'];
  } catch (Throwable $e) {
    error_log('PayLess admin session validation failed: ' . $e->getMessage());
    http_response_code(503);
    exit('The admin service is temporarily unavailable.');
  }
}
function admin_require_owner(array $user): void {
  if (($user['role'] ?? '') !== 'owner') {
    http_response_code(403);
    exit('Owner permission is required for this action.');
  }
}
function admin_redirect(string $query = ''): never {
  header('Location: /admin/' . ($query !== '' ? '?' . $query : ''));
  exit;
}
function admin_layout_start(string $title, array $user): void {
  $safeTitle = admin_escape($title);
  $safeName = admin_escape((string) $user['display_name']);
  $csrf = admin_escape(admin_csrf_token());
  $role = admin_escape(ucfirst((string) $user['role']));
  echo '<!doctype html><html lang="en" data-theme="light"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><meta name="color-scheme" content="light dark"><meta name="theme-color" content="#f3f2ed"><title>' . $safeTitle . ' · PayLess Admin</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="/assets/css/site.css"><link rel="stylesheet" href="/assets/css/admin.css"><script>(()=>{try{const s=localStorage.getItem("payless-theme");document.documentElement.dataset.theme=(s==="light"||s==="dark")?s:(matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light")}catch(_){}})();</script></head><body class="admin-body"><header class="admin-top"><a class="admin-brand" href="/admin/">PAYLESS <small>Private administration</small></a><div class="admin-who"><span>' . $safeName . ' · ' . $role . '</span><button class="admin-icon-button" type="button" data-admin-theme-toggle aria-label="Switch to dark theme" aria-pressed="false" title="Switch theme"><span data-admin-theme-icon aria-hidden="true">◐</span></button><form method="post" action="/admin/logout.php"><input type="hidden" name="_csrf" value="' . $csrf . '"><button class="admin-signout" type="submit">Sign out ↗</button></form></div></header><main class="admin-wrap">';
}
function admin_layout_end(): void {
  echo '</main><footer class="admin-wrap admin-footer">PRIVATE BUSINESS WORKSPACE <span aria-hidden="true">/</span> Customer enquiry data is confidential. Do not share screenshots or exports with unauthorized people.</footer><script src="/assets/js/admin-theme.js" defer></script></body></html>';
}
