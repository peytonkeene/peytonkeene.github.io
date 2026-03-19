<?php

declare(strict_types=1);

require_once __DIR__ . '/app.php';

/**
 * Replace this with a DB lookup when persistence is added.
 */
function find_user_by_email(string $email): ?array
{
    $users = [
        [
            'id' => 1,
            'name' => 'Demo Clinician',
            'email' => 'demo@pulsechartems.com',
            'password_hash' => password_hash('EMSdemo!2026', PASSWORD_DEFAULT),
            'role' => 'Supervisor',
        ],
    ];

    foreach ($users as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return $user;
        }
    }

    return null;
}

function attempt_login(string $email, string $password): bool
{
    $user = find_user_by_email($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    session_regenerate_id(true);

    return true;
}

function is_authenticated(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!is_authenticated()) {
        $_SESSION['auth_error'] = 'Please sign in to continue.';
        header('Location: ' . app_url('login.php'));
        exit;
    }
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}
