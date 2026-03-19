<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_authenticated()) {
    header('Location: ' . app_url('dashboard.php'));
    exit;
}

$error = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Enter both email and password to continue.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (attempt_login($email, $password)) {
        header('Location: ' . app_url('dashboard.php'));
        exit;
    } else {
        $error = 'The credentials provided do not match our records.';
    }
}

$pageTitle = 'Sign In • ' . APP_NAME;
require __DIR__ . '/includes/head.php';
?>
<div class="login-layout">
    <section class="login-panel login-panel-brand">
        <div class="login-brand-content">
            <p class="eyebrow">PulseChart EMS Documentation</p>
            <h1>Precision charting for high-stakes response teams.</h1>
            <p>Secure, organized workflows for EMS narratives, QA review, and operational reporting. Built for field reality and medical accountability.</p>
            <ul>
                <li>Structured narrative tools (coming soon)</li>
                <li>Faster documentation handoff</li>
                <li>Ready for agency-specific expansion</li>
            </ul>
        </div>
    </section>

    <section class="login-panel login-panel-form">
        <form class="login-card" method="post" action="<?= h(app_url('login.php')) ?>" novalidate>
            <p class="eyebrow">Welcome Back</p>
            <h2>Sign in to your account</h2>
            <p class="form-note">Use your assigned credentials to access the EMS platform.</p>

            <?php if ($error !== ''): ?>
                <div class="alert-error"><?= h($error) ?></div>
            <?php endif; ?>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="name@agency.org" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Enter your password" required>

            <button type="submit">Sign In</button>

            <p class="demo-hint">Demo login: <strong>demo@pulsechartems.com</strong> / <strong>EMSdemo!2026</strong></p>
        </form>
    </section>
</div>
</body>
</html>
