<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');

$loginSuccess = false;

$config = require __DIR__ . '/../config/auth.php';

try {
    require_once __DIR__ . '/../includes/db.php';
    $pdo = db();

    $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, password_hash, role, agency_id, is_active FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $dbUser = $stmt->fetch();

    if ($dbUser && (int)$dbUser['is_active'] === 1 && password_verify($password, (string)$dbUser['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)$dbUser['id'],
            'name' => trim($dbUser['first_name'] . ' ' . $dbUser['last_name']),
            'first_name' => $dbUser['first_name'],
            'last_name' => $dbUser['last_name'],
            'email' => $dbUser['email'],
            'role' => $dbUser['role'] ?: 'user',
            'agency_id' => $dbUser['agency_id'] !== null ? (int)$dbUser['agency_id'] : null,
        ];
        $loginSuccess = true;

        $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id')->execute([':id' => (int)$dbUser['id']]);
    }
} catch (Throwable $e) {
    // Fall back to simple configured login below.
}

if (!$loginSuccess) {
    $user = $config['default_user'];

    if ($email === $user['email'] && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)($user['id'] ?? 1),
            'name' => $user['name'],
            'first_name' => $user['first_name'] ?? $user['name'],
            'last_name' => $user['last_name'] ?? '',
            'email' => $user['email'],
            'role' => $user['role'] ?? 'admin',
            'agency_id' => isset($user['agency_id']) ? (int)$user['agency_id'] : 1,
        ];
        $loginSuccess = true;
    }
}

if ($loginSuccess) {
    header('Location: /dashboard.php');
    exit;
}

$_SESSION['login_error'] = 'Invalid email or password.';
header('Location: /login.php');
exit;
