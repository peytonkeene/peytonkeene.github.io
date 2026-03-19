<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (!empty($_SESSION['user'])) {
    header('Location: /dashboard.php');
    exit;
}
$pageTitle = 'MedNarrate | Login';
include __DIR__ . '/includes/head.php';
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<div class="login-page">
    <div class="card login-card">
        <aside class="login-image-panel" aria-hidden="true">
            <!-- ADMIN WILL REPLACE THIS IMAGE URL -->
            <div class="login-image-overlay"></div>
            <div class="login-image-content">
                <h2>MedNarrate</h2>
                <p>Clinical Documentation Platform</p>
            </div>
        </aside>

        <section class="login-form-panel">
            <div class="login-header">
                <h1 class="login-title">MedNarrate</h1>
                <p class="login-subtitle">Clinical Documentation Platform</p>
            </div>
            <div class="login-divider" aria-hidden="true"></div>

            <?php if ($error): ?><div class="alert login-alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <form method="post" action="/auth/login_process.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email" required autocomplete="email" placeholder="you@agency.org">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter password">
                </div>
                <button class="btn btn-primary-login btn-block" type="submit">Sign In</button>
            </form>

            <div class="form-links">
                <a href="/contact.php">Forgot password</a>
                <a href="/contact.php">Contact support</a>
            </div>

            <a class="return-home-link" href="/">Return to Home</a>

            <div class="login-footer-meta">
                <a href="/contact.php">Need help accessing your account?</a>
                <p>Powered by MedNarrate</p>
                <small>MedNarrate &copy; <?php echo date('Y'); ?></small>
            </div>
        </section>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
