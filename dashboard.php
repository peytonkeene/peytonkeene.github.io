<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_auth();

$activePage = 'dashboard';
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/app-shell-start.php';
?>
<div class="welcome-card">
    <h2>Welcome back, <?= h(current_user()['name'] ?? 'Clinician') ?>.</h2>
    <p>Your EMS documentation workspace is ready. Start with a module below or continue where you left off.</p>
</div>

<div class="grid-cards">
    <a class="feature-card" href="<?= h(app_url('generators/index.php')) ?>">
        <h3>Narrative Generators</h3>
        <p>Launch the modular narrative workspace. Generator templates will be added here.</p>
    </a>
    <a class="feature-card" href="<?= h(app_url('admin/index.php')) ?>">
        <h3>Admin</h3>
        <p>Manage platform settings, account permissions, and organizational options.</p>
    </a>
    <a class="feature-card" href="<?= h(app_url('reports/index.php')) ?>">
        <h3>Reports</h3>
        <p>View operational and quality reporting modules when available.</p>
    </a>
    <a class="feature-card" href="<?= h(app_url('account/index.php')) ?>">
        <h3>Account</h3>
        <p>Update user profile preferences, security settings, and notification behavior.</p>
    </a>
</div>
<?php require __DIR__ . '/includes/app-shell-end.php'; ?>
