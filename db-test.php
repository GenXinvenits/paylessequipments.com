<?php
declare(strict_types=1);

/**
 * Temporary database connectivity check.
 * Remove this file after verification; it exposes only connection status.
 */
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

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
