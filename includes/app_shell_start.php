<?php require_once __DIR__ . '/../auth/check_auth.php'; ?>
<?php $pageTitle = $pageTitle ?? APP_NAME; include __DIR__ . '/head.php'; ?>
<div class="app-layout">
    <aside class="sidebar">
        <div>
            <a class="brand" href="/dashboard.php">MedNarrate</a>
            <nav class="side-nav">
                <a href="/dashboard.php">Dashboard</a>
                <a href="/generators/index.php">Generators</a>
                <a href="/reports/index.php">Reports</a>
                <a href="/account/index.php">Account</a>
                <a href="/admin/index.php">Admin</a>
            </nav>
        </div>
        <a class="btn btn-outline btn-block" href="/auth/logout.php">Logout</a>
    </aside>
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title"><?php echo htmlspecialchars($pageTitle); ?></div>
            <div class="user-chip">
                <span><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'User'); ?></span>
                <small><?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?></small>
            </div>
        </header>
        <section class="app-content">
