<?php
declare(strict_types=1);

/**
 * MariaDB connection for public forms and the private admin dashboard.
 *
 * Credentials may be supplied by the PHP environment or by the untracked
 * server-only includes/database.local.php file. Never commit real credentials.
 */
function payless_db(): PDO {
  static $connection = null;
  if ($connection instanceof PDO) {
    return $connection;
  }

  $localConfigPath = __DIR__ . '/database.local.php';
  $localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
  if (!is_array($localConfig)) {
    throw new RuntimeException('Invalid includes/database.local.php configuration.');
  }

  $host = getenv('PAYLESS_DB_HOST') ?: ($localConfig['host'] ?? 'localhost');
  $port = getenv('PAYLESS_DB_PORT') ?: ($localConfig['port'] ?? '3306');
  $name = getenv('PAYLESS_DB_NAME') ?: ($localConfig['name'] ?? 'db_example_com');
  $user = getenv('PAYLESS_DB_USER') ?: ($localConfig['user'] ?? 'admin');
  $password = getenv('PAYLESS_DB_PASSWORD');
  if ($password === false || $password === '') {
    $password = (string) ($localConfig['password'] ?? '');
  }

  if ($password === '') {
    throw new RuntimeException(
      'Set PAYLESS_DB_PASSWORD in the PHP environment or configure includes/database.local.php.'
    );
  }
  if (!preg_match('/^[a-zA-Z0-9_]+$/', (string) $name)) {
    throw new RuntimeException('Invalid PayLess database name configuration.');
  }
  if (!preg_match('/^[a-zA-Z0-9_.:-]+$/', (string) $host) || !preg_match('/^[0-9]+$/', (string) $port)) {
    throw new RuntimeException('Invalid PayLess database connection configuration.');
  }

  $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
  $connection = new PDO($dsn, (string) $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_TIMEOUT => 5,
  ]);
  return $connection;
}
