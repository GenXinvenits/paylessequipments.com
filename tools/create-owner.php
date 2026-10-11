<?php
declare(strict_types=1);

// Run from a shell with PHP CLI. Never run this through a browser.
if (PHP_SAPI !== 'cli') {
  http_response_code(404);
  exit;
}
require_once __DIR__ . '/../includes/database.php';

function prompt_secret(string $prompt): string {
  fwrite(STDOUT, $prompt);
  if (DIRECTORY_SEPARATOR === '/' && function_exists('shell_exec') && function_exists('exec')) {
    $stty = trim((string) shell_exec('stty -g 2>/dev/null'));
    if ($stty !== '') {
      shell_exec('stty -echo');
      $value = (string) fgets(STDIN);
      shell_exec('stty ' . escapeshellarg($stty));
      fwrite(STDOUT, PHP_EOL);
      return rtrim($value, "\r\n");
    }
  }
  return rtrim((string) fgets(STDIN), "\r\n");
}
try {
  $db = payless_db();
  $count = (int) $db->query("SELECT COUNT(*) FROM admin_users WHERE role = 'owner'")->fetchColumn();
  if ($count > 0) {
    fwrite(STDERR, "An owner account already exists. For safety, this script will not create another one.\n");
    exit(1);
  }
  $email = strtolower(trim((string) ($argv[1] ?? '')));
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php tools/create-owner.php owner@example.com\n");
    exit(1);
  }
  fwrite(STDOUT, "Owner display name: ");
  $name = trim((string) fgets(STDIN));
  if ($name === '' || strlen($name) > 120) {
    fwrite(STDERR, "A display name of 1–120 characters is required.\n");
    exit(1);
  }
  $password = prompt_secret("Create a strong owner password (minimum 14 characters): ");
  if (strlen($password) < 14 || strlen($password) > 4096) {
    fwrite(STDERR, "Password must be at least 14 characters. Nothing was created.\n");
    exit(1);
  }
  $confirm = prompt_secret("Confirm password: ");
  if (!hash_equals($password, $confirm)) {
    fwrite(STDERR, "Passwords did not match. Nothing was created.\n");
    exit(1);
  }
  $stmt = $db->prepare("INSERT INTO admin_users (email,password_hash,display_name,role,is_active) VALUES (?,?,?,'owner',1)");
  $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $name]);
  fwrite(STDOUT, "Owner account created. Sign in at /admin/login.php.\n");
} catch (Throwable $e) {
  fwrite(STDERR, "Could not create owner account. Confirm the admin schema was imported and database access works.\n");
  error_log('PayLess owner provisioning failed: ' . $e->getMessage());
  exit(1);
}
