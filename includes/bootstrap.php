<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbConfigPath = __DIR__ . '/../config/database.php';

if (!file_exists($dbConfigPath)) {
    die('Database configuration file not found: ' . $dbConfigPath);
}

$db = require $dbConfigPath;

if (!is_array($db)) {
    die('Database configuration file must return an array.');
}

$requiredKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'];

foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $db)) {
        die('Missing database config key: ' . $key);
    }
}

$dsn = 'mysql:host=' . $db['DB_HOST'] . ';dbname=' . $db['DB_NAME'] . ';charset=' . $db['DB_CHARSET'];

try {
    $pdo = new PDO($dsn, $db['DB_USER'], $db['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
