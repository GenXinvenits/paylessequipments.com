<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

try {
    require_once __DIR__ . '/includes/database.php';

    $pdo = payless_db();
    $result = $pdo->query('SELECT DATABASE() AS db, VERSION() AS version');
    $row = $result->fetch();

    echo "DATABASE CONNECTION: OK\n";
    echo "Database: " . ($row['db'] ?? 'unknown') . "\n";
    echo "MariaDB/MySQL version: " . ($row['version'] ?? 'unknown') . "\n";
} catch (Throwable $e) {
    http_response_code(500);
    error_log('PayLess db-test failed: ' . $e->getMessage());
    echo "DATABASE CONNECTION: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
