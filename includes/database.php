<?php
declare(strict_types=1);

/**
 * MariaDB connection for website form submissions.
 * Keep the database password in the OpenLiteSpeed PHP environment;
 * never commit credentials to this public repository.
 */
function payless_db(): PDO {
  static $connection = null;
  if ($connection instanceof PDO) {
    return $connection;
  }

  $host = getenv('PAYLESS_DB_HOST') ?: '127.0.0.1';
  $port = getenv('PAYLESS_DB_PORT') ?: '3306';
  $name = getenv('PAYLESS_DB_NAME') ?: 'db_example_com';
  $user = getenv('PAYLESS_DB_USER') ?: 'admin';
  $password = getenv('PAYLESS_DB_PASSWORD');

  if ($name === '' || $user === '' || $password === false || $password === '') {
    throw new RuntimeException('PayLess database settings are not configured.');
  }
  if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
    throw new RuntimeException('Invalid PayLess database name configuration.');
  }

  $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
  $connection = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_TIMEOUT => 5,
  ]);
  return $connection;
}
