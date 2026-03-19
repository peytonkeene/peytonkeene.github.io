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
<div class="login-wrap">
    <div class="card login-card">
        <h1>MedNarrate</h1>
        <p>Clinical Documentation Platform</p>
        <?php if ($error): ?><div class="alert"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post" action="/auth/login_process.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required autocomplete="email" placeholder="you@agency.org">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter password">
            </div>
            <button class="btn btn-primary btn-block" type="submit">Sign In</button>
        </form>
        <div class="form-links">
            <a href="/contact.php">Forgot password</a>
            <a href="/contact.php">Contact support</a>
        </div>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
