<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$config = require __DIR__ . '/../config/auth.php';
$user = $config['default_user'];

$email = trim($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');

if ($email === $user['email'] && password_verify($password, $user['password_hash'])) {
    $_SESSION['user'] = [
        'name' => $user['name'],
        'email' => $user['email'],
    ];

    header('Location: /dashboard.php');
    exit;
}

$_SESSION['login_error'] = 'Invalid email or password.';
header('Location: /login.php');
exit;
