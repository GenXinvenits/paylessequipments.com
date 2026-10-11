<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/admin-auth.php';

if (admin_current_user()) {
  header('Location: /admin/');
  exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  admin_verify_csrf();
  $email = strtolower(trim((string) ($_POST['email'] ?? '')));
  $password = (string) ($_POST['password'] ?? '');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 4096) {
    $error = 'Email or password is incorrect.';
  } else {
    try {
      $db = payless_db();
      $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
      $ipHash = hash_hmac('sha256', $ip, (string) (getenv('PAYLESS_ADMIN_RATE_KEY') ?: 'payless-admin-login-rate-limit'));
      $db->prepare('DELETE FROM admin_login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)')->execute();
      $rate = $db->prepare('SELECT COUNT(*) FROM admin_login_attempts WHERE ip_hash = ? AND attempted_at > (NOW() - INTERVAL 15 MINUTE) AND was_successful = 0');
      $rate->execute([$ipHash]);
      if ((int) $rate->fetchColumn() >= 8) {
        $error = 'Too many attempts. Please wait 15 minutes before trying again.';
      } else {
        $stmt = $db->prepare('SELECT id,email,password_hash,display_name,role,is_active FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $account = $stmt->fetch();
        $valid = $account && (int) $account['is_active'] === 1 && password_verify($password, (string) $account['password_hash']);
        $db->prepare('INSERT INTO admin_login_attempts (ip_hash, was_successful) VALUES (?, ?)')->execute([$ipHash, $valid ? 1 : 0]);
        if ($valid) {
          if (password_needs_rehash((string) $account['password_hash'], PASSWORD_DEFAULT)) {
            $db->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), (int) $account['id']]);
          }
          session_regenerate_id(true);
          $_SESSION['admin_user'] = [
            'id' => (int) $account['id'],
            'email' => (string) $account['email'],
            'display_name' => (string) $account['display_name'],
            'role' => (string) $account['role'],
          ];
          $db->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = ?')->execute([(int) $account['id']]);
          header('Location: /admin/');
          exit;
        }
        $error = 'Email or password is incorrect.';
      }
    } catch (Throwable $e) {
      error_log('PayLess admin login error: ' . $e->getMessage());
      $error = 'Admin sign-in is temporarily unavailable. Please try again later.';
    }
  }
}
?><!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="color-scheme" content="light dark">
<meta name="theme-color" content="#f3f2ed">
<title>Admin sign in · PayLess</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/site.css">
<link rel="stylesheet" href="/assets/css/admin.css">
<script>(()=>{try{const s=localStorage.getItem("payless-theme");document.documentElement.dataset.theme=(s==="light"||s==="dark")?s:(matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light")}catch(_){}})();</script>
</head>
<body class="admin-body admin-login-page">
<main class="admin-login">
  <div class="admin-login__top">
    <a href="/" class="admin-login__brand">PAYLESS<small>PRIVATE ADMINISTRATION</small></a>
    <button class="admin-icon-button" type="button" data-admin-theme-toggle aria-label="Switch to dark theme" aria-pressed="false" title="Switch theme"><span data-admin-theme-icon aria-hidden="true">◐</span></button>
  </div>
  <p class="admin-eyebrow">Authorized personnel only</p>
  <h1>Sign in to your workspace.</h1>
  <p class="admin-login__intro">Customer enquiries and business information are restricted to approved PayLess staff.</p>
  <?php if ($error !== ''): ?><div class="admin-error" role="alert"><?= admin_escape($error) ?></div><?php elseif (($_GET['status'] ?? '') === 'expired'): ?><div class="admin-error" role="alert">Your session expired or your account was disabled. Please sign in again.</div><?php endif; ?>
  <form method="post" action="/admin/login.php" autocomplete="on">
    <input type="hidden" name="_csrf" value="<?= admin_escape(admin_csrf_token()) ?>">
    <label class="admin-field">Work email<input type="email" name="email" autocomplete="username" maxlength="254" required autofocus></label>
    <label class="admin-field">Password<input type="password" name="password" autocomplete="current-password" maxlength="4096" required></label>
    <button class="admin-submit" type="submit">Sign in securely →</button>
  </form>
  <p class="admin-login__secure">This area is not available to customers. Accounts must be created by the owner.</p>
  <a class="admin-login__back" href="/">← Return to public website</a>
</main>
<script src="/assets/js/admin-theme.js" defer></script>
</body>
</html>
