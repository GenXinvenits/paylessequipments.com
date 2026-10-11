<?php
declare(strict_types=1);

/**
 * Temporary, localhost-only database connectivity check.
 * Remove this file after verification.
 */
$remoteAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
if (!in_array($remoteAddress, ['127.0.0.1', '::1'], true)) {
  http_response_code(404);
  exit;
}

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/includes/database.php';

try {
  $db = payless_db();
  $db->query('SELECT 1')->fetchColumn();
  echo "PayLess database connection: OK\n";
  echo "The website PHP process connected to MariaDB successfully.\n";
} catch (Throwable $exception) {
  error_log('PayLess database connectivity test failed: ' . get_class($exception) . ': ' . $exception->getMessage());
  http_response_code(503);
  echo "PayLess database connection: FAILED\n";
  echo "Check the OpenLiteSpeed/PHP error log for details.\n";
}
