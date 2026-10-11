<?php
declare(strict_types=1);

/**
 * MariaDB connection for website form submissions.
 *
 * Set the password directly in this file on the server. Do not commit the
 * real password to this public GitHub repository.
 */
function payless_db(): PDO {
  static $connection = null;
  if ($connection instanceof PDO) {
    return $connection;
  }

  $host = 'localhost';
  $port = '3306';
  $name = 'db_example_com';
  $user = 'admin';
  $password = 'MySQL@2017';

  if ($name === '' || $user === '' || $password === '' || $password === 'SET_YOUR_DATABASE_PASSWORD_HERE') {
    throw new RuntimeException('Set the MariaDB password in includes/database.php on the server.');
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