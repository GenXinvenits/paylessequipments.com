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
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow,noarchive"><title>Admin sign in · PayLess</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:22px;background:#202720;color:#f3f4ed;font:14px/1.6 system-ui,-apple-system,sans-serif}.login{width:min(100%,440px);background:#fffefa;color:#1a201a;padding:clamp(26px,6vw,44px);box-shadow:0 25px 80px #0005}.brand{font-weight:900;letter-spacing:-.04em;font-size:18px}.brand small{display:block;color:#666d63;font:10px ui-monospace,monospace;letter-spacing:.13em;margin-top:3px}.eyebrow{font:10px ui-monospace,monospace;letter-spacing:.13em;color:#666d63;text-transform:uppercase;margin:32px 0 9px}.login h1{font-size:38px;letter-spacing:-.07em;line-height:1.05;margin:0}.intro{color:#666d63;font-size:13px;margin:12px 0 24px}.field{display:grid;gap:6px;margin-top:15px;font-size:12px;font-weight:700}input{width:100%;padding:12px;border:1px solid #cfd2c6;background:white;color:#1a201a;border-radius:0;font:inherit;min-height:46px}.submit{margin-top:22px;width:100%;padding:13px;background:#d8f15b;color:#1a201a;border:0;font-weight:850;cursor:pointer}.error{padding:11px 12px;background:#f7dfdb;border:1px solid #e5bcb5;color:#74231c;font-size:12px;margin:18px 0}.secure{margin-top:20px;font-size:11px;color:#666d63}.back{display:inline-block;margin-top:18px;font-size:12px;text-decoration:underline}
</style></head><body><main class="login"><a href="/" class="brand">PAYLESS<small>PRIVATE ADMINISTRATION</small></a><p class="eyebrow">Authorized personnel only</p><h1>Sign in to your workspace.</h1><p class="intro">Customer enquiries and business information are restricted to approved PayLess staff.</p>
<?php if ($error !== ''): ?><div class="error" role="alert"><?= admin_escape($error) ?></div><?php elseif (($_GET['status'] ?? '') === 'expired'): ?><div class="error" role="alert">Your session expired or your account was disabled. Please sign in again.</div><?php endif; ?>
<form method="post" action="/admin/login.php" autocomplete="on"><input type="hidden" name="_csrf" value="<?= admin_escape(admin_csrf_token()) ?>"><label class="field">Work email<input type="email" name="email" autocomplete="username" maxlength="254" required autofocus></label><label class="field">Password<input type="password" name="password" autocomplete="current-password" maxlength="4096" required></label><button class="submit" type="submit">Sign in securely →</button></form>
<p class="secure">This area is not available to customers. Accounts must be created by the owner.</p><a class="back" href="/">← Return to public website</a></main></body></html>
