<aside class="sidebar">
    <div class="brand">
        <span class="brand-dot"></span>
        <div>
            <p class="brand-name">PulseChart</p>
            <p class="brand-sub">EMS Suite</p>
        </div>
    </div>

    <nav class="nav">
        <a class="nav-link <?= active_nav('dashboard', $activePage ?? '') ?>" href="<?= h(app_url('dashboard.php')) ?>">Dashboard</a>
        <a class="nav-link <?= active_nav('generators', $activePage ?? '') ?>" href="<?= h(app_url('generators/index.php')) ?>">Narrative Generators</a>
        <a class="nav-link <?= active_nav('reports', $activePage ?? '') ?>" href="<?= h(app_url('reports/index.php')) ?>">Reports</a>
        <a class="nav-link <?= active_nav('admin', $activePage ?? '') ?>" href="<?= h(app_url('admin/index.php')) ?>">Admin</a>
        <a class="nav-link <?= active_nav('account', $activePage ?? '') ?>" href="<?= h(app_url('account/index.php')) ?>">Account</a>
    </nav>

    <a class="logout-link" href="<?= h(app_url('logout.php')) ?>">Sign Out</a>
</aside>
