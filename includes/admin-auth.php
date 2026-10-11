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
  echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><title>' . $safeTitle . ' · PayLess Admin</title><style>
  :root{color-scheme:light;--bg:#f3f2ed;--paper:#fffefa;--ink:#1a201a;--muted:#666d63;--line:#dedfd5;--accent:#d8f15b;--dark:#202720;--red:#a42e24;--green:#2b6846}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:14px/1.55 system-ui,-apple-system,sans-serif}a{color:inherit;text-decoration:none}button,input,select{font:inherit}.top{background:var(--dark);color:#f3f4ed;padding:17px max(22px,calc((100% - 1320px)/2));display:flex;align-items:center;justify-content:space-between;gap:18px}.brand{font-weight:850;letter-spacing:-.04em}.brand small{display:block;font-size:10px;color:#b9c2b4;letter-spacing:.13em;text-transform:uppercase}.who{display:flex;align-items:center;gap:14px;color:#d6ddcf;font-size:12px}.who a{border:1px solid #ffffff40;padding:7px 11px}.wrap{width:min(1320px,calc(100% - 40px));margin:34px auto 70px}.eyebrow{font:10px ui-monospace,monospace;letter-spacing:.13em;color:var(--muted);text-transform:uppercase}.head{display:flex;align-items:end;justify-content:space-between;gap:18px;margin-bottom:25px}.head h1{font-size:clamp(30px,4vw,46px);letter-spacing:-.065em;line-height:1.04;margin:7px 0}.head p{color:var(--muted);margin:8px 0 0}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px}.stat,.card{background:var(--paper);border:1px solid var(--line);padding:20px}.stat span{color:var(--muted);font-size:12px}.stat strong{display:block;font-size:30px;letter-spacing:-.06em;margin-top:5px}.card{padding:0;overflow:hidden}.filters{padding:16px;display:flex;gap:10px;flex-wrap:wrap;border-bottom:1px solid var(--line)}input,select{min-height:42px;padding:9px 11px;border:1px solid #cfd2c6;background:#fff;color:#1a201a;border-radius:2px}input[type=search]{flex:1;min-width:180px}.btn{border:1px solid var(--ink);background:var(--ink);color:white;padding:10px 15px;min-height:42px;cursor:pointer;font-weight:700;font-size:12px}.btn.light{background:transparent;color:var(--ink)}.btn.accent{background:var(--accent);color:var(--ink);border-color:var(--accent)}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;min-width:850px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid #e8e8e0;vertical-align:top}th{background:#f8f8f2;color:var(--muted);font:10px ui-monospace,monospace;letter-spacing:.08em;text-transform:uppercase}td{font-size:12px}td strong{display:block;font-size:13px}td small{display:block;color:var(--muted);margin-top:3px}.pill{display:inline-block;padding:4px 8px;background:#e9eadf;border-radius:30px;font-size:10px;font-weight:800;text-transform:capitalize}.pill.new{background:#f7edca}.pill.contacted,.pill.quoted{background:#dce8f6}.pill.completed{background:#dceedd}.pill.archived{background:#e7e7e7}.actions{display:flex;gap:7px;align-items:center;flex-wrap:wrap}.actions select{min-height:34px;padding:5px;font-size:11px}.actions .btn{min-height:34px;padding:6px 9px}.empty{padding:40px;text-align:center;color:var(--muted)}.notice{padding:12px 15px;margin-bottom:18px;background:#e1efda;border:1px solid #bdd6b2}.notice.error{background:#f7dfdb;border-color:#e5bcb5}.detail{padding:24px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.detail h2{font-size:21px;letter-spacing:-.04em;margin:0 0 18px}.detail dl{margin:0}.detail dt{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-top:14px}.detail dd{margin:3px 0 0;overflow-wrap:anywhere}.detail .wide{grid-column:1/-1}.section-title{font-size:20px;letter-spacing:-.04em;margin:28px 0 12px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.form-grid label{display:grid;gap:5px;font-size:12px;font-weight:700}.form-grid .wide{grid-column:1/-1}.helper{color:var(--muted);font-size:12px}.user-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:15px 0;border-bottom:1px solid var(--line)}.user-row:last-child{border-bottom:0}.user-row small{display:block;color:var(--muted)}.pager{display:flex;justify-content:space-between;align-items:center;padding:15px 18px;color:var(--muted);font-size:12px}.footer{color:var(--muted);font-size:11px;margin-top:24px}@media(max-width:900px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.head{align-items:start;flex-direction:column}.detail{grid-template-columns:1fr}}@media(max-width:520px){.top{align-items:flex-start}.who{align-items:flex-end;flex-direction:column;gap:5px}.wrap{width:calc(100% - 24px);margin-top:22px}.grid{gap:8px}.stat{padding:14px}.stat strong{font-size:24px}.form-grid{grid-template-columns:1fr}.form-grid .wide{grid-column:auto}.detail{padding:16px}}
  </style></head><body><header class="top"><a class="brand" href="/admin/">PAYLESS <small>Private administration</small></a><div class="who"><span>' . $safeName . ' · ' . $role . '</span><form method="post" action="/admin/logout.php"><input type="hidden" name="_csrf" value="' . $csrf . '"><button type="submit" style="border:1px solid #ffffff40;background:transparent;color:inherit;padding:7px 11px;cursor:pointer">Sign out</button></form></div></header><main class="wrap">';
}
function admin_layout_end(): void {
  echo '</main><footer class="wrap footer">Private business workspace · Customer enquiry data is confidential. Do not share screenshots or exports with unauthorized people.</footer></body></html>';
}
