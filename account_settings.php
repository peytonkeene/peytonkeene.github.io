<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch the current user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, first_name, last_name, license_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $first_name, $last_name, $license_number);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $new_first_name = $_POST['first_name'] ?? $first_name;
    $new_last_name = $_POST['last_name'] ?? $last_name;
    $new_license_number = $_POST['license_number'] ?? $license_number;

    $sql = "UPDATE users SET first_name = ?, last_name = ?, license_number = ?";
    
    if ($new_password) {
        $sql .= ", password_hash = ?";
    }
    
    $sql .= " WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($new_password) {
        $stmt->bind_param("ssssi", $new_first_name, $new_last_name, $new_license_number, $new_password, $user_id);
    } else {
        $stmt->bind_param("sssi", $new_first_name, $new_last_name, $new_license_number, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Account updated successfully!";
    } else {
        $error_message = "Failed to update account. Please try again.";
    }

    $stmt->close();
    $conn->close();

    // Redirect based on group_id
    if ($_SESSION['group_id'] == 2) { // Assuming GCEMS group_id is 2
        header("Location: gcems_dashboard.html");
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.html");
    } else {
        header("Location: dashboard.html");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --bg: #edf3f6;
            --surface: rgba(255, 255, 255, 0.94);
            --surface-soft: #f6fafc;
            --text: #18242d;
            --text-soft: #5f7180;
            --label: #536575;
            --brand: #0f5f7a;
            --brand-strong: #0b4d63;
            --brand-border: #bed8e1;
            --border: rgba(215, 227, 233, 0.96);
            --shadow: 0 20px 50px rgba(15, 42, 56, 0.10);
            --shadow-soft: 0 10px 28px rgba(15, 42, 56, 0.08);
            --radius-xl: 24px;
            --radius-lg: 20px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(15, 95, 122, 0.10), transparent 28rem),
                radial-gradient(circle at top right, rgba(17, 94, 89, 0.08), transparent 24rem),
                linear-gradient(180deg, #f8fbfc 0%, var(--bg) 48%, #e8f0f4 100%);
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }
        a { color: inherit; }
        .container { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 30;
            border-bottom: 1px solid rgba(196, 210, 219, 0.75);
            background: rgba(248, 251, 252, 0.88);
            backdrop-filter: blur(18px) saturate(160%);
        }
        .nav {
            min-height: 84px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .brand img {
            height: 52px;
            padding: 6px;
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--shadow-soft);
        }
        .eyebrow {
            display: inline-flex;
            margin-bottom: 4px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--brand-border);
            background: rgba(227, 241, 246, 0.75);
            color: var(--brand-strong);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .brand h1 { margin: 0; font-size: 1.1rem; }
        nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        nav a {
            padding: 10px 14px;
            border-radius: 999px;
            text-decoration: none;
            color: var(--text-soft);
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.18s ease;
        }
        nav a:hover,
        nav a.active {
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            box-shadow: 0 10px 24px rgba(15, 95, 122, 0.22);
        }
        main { flex: 1; padding: 28px 0 40px; }
        .page-stack { display: grid; gap: 22px; }
        .hero-card {
            padding: 30px;
            border: 1px solid rgba(190, 216, 225, 0.95);
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(15, 95, 122, 0.98), rgba(10, 64, 90, 0.94));
            color: #fff;
            box-shadow: var(--shadow);
        }
        .hero-card h2 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.05;
            letter-spacing: -0.03em;
        }
        .hero-card p {
            margin: 0;
            max-width: 60ch;
            color: rgba(255, 255, 255, 0.84);
        }
        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(260px, 0.85fr);
            gap: 22px;
            align-items: start;
        }
        .card {
            padding: 26px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }
        .section-label {
            display: inline-block;
            margin-bottom: 10px;
            color: var(--label);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        h3 {
            margin: 0 0 12px;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
        }
        p {
            margin: 0 0 16px;
            color: var(--text-soft);
            font-size: 1rem;
        }
        .account-meta {
            display: grid;
            gap: 12px;
            margin-bottom: 22px;
        }
        .account-meta-item {
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
        }
        .account-meta-item strong {
            display: block;
            margin-bottom: 4px;
            color: var(--label);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .account-meta-item span {
            color: var(--text);
            font-size: 1rem;
        }
        form {
            display: grid;
            gap: 16px;
        }
        label {
            display: block;
            margin-bottom: 7px;
            color: var(--label);
            font-weight: 800;
            font-size: 0.84rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            min-height: 48px;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: linear-gradient(180deg, #fff, #fcfeff);
            color: var(--text);
            font-size: 1rem;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }
        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(15, 95, 122, 0.12);
            background: #fff;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            padding: 12px 18px;
            border: 1px solid transparent;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 16px 32px rgba(15, 95, 122, 0.20);
            transition: transform 0.14s ease, box-shadow 0.18s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .success-message,
        .error-message {
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 0.95rem;
            margin-top: 18px;
        }
        .success-message {
            border: 1px solid rgba(15, 118, 110, 0.18);
            background: rgba(15, 118, 110, 0.08);
            color: #0f766e;
        }
        .error-message {
            border: 1px solid rgba(180, 35, 24, 0.18);
            background: rgba(180, 35, 24, 0.06);
            color: #b42318;
        }
        .info-list {
            display: grid;
            gap: 14px;
        }
        .info-item {
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
        }
        .info-item strong {
            display: block;
            margin-bottom: 6px;
            color: var(--text);
        }
        footer {
            border-top: 1px solid rgba(196, 210, 219, 0.85);
            background: rgba(18, 32, 43, 0.96);
            color: rgba(255, 255, 255, 0.82);
        }
        footer .container {
            padding: 18px 16px;
            text-align: center;
            font-size: 0.92rem;
        }
        footer a {
            color: #fff;
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
            margin: 0 8px;
        }
        @media (max-width: 960px) {
            .content-grid { grid-template-columns: 1fr; }
            .nav { min-height: auto; padding: 14px 0; flex-direction: column; align-items: flex-start; }
            nav { width: 100%; }
        }
        @media (max-width: 640px) {
            .container { width: min(100% - 20px, 1180px); }
            .hero-card, .card { padding: 20px; }
            .brand img { height: 46px; }
            nav a { padding: 9px 12px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="<?php
                if ($_SESSION['group_id'] == 2) {
                    echo 'gcems_dashboard.html';
                } elseif ($_SESSION['role'] === 'admin') {
                    echo 'admin_dashboard.html';
                } else {
                    echo 'dashboard.html';
                }
            ?>">
                <img src="images/logo.png" alt="MedNarrate Logo">
                <div>
                    <span class="eyebrow">MedNarrate Clinical Workspace</span>
                    <h1>Account Settings</h1>
                </div>
            </a>
            <nav>
                <a class="active" href="<?php
                    if ($_SESSION['group_id'] == 2) {
                        echo 'gcems_dashboard.html';
                    } elseif ($_SESSION['role'] === 'admin') {
                        echo 'admin_dashboard.html';
                    } else {
                        echo 'dashboard.html';
                    }
                ?>">Dashboard</a>
                <a href="logout.php">Log Out</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="container page-stack">
            <section class="hero-card">
                <span class="eyebrow">Profile Management</span>
                <h2>Manage account details used in your MedNarrate documentation workspace.</h2>
                <p>Review your current account information and update personal details or password using the same existing save behavior.</p>
            </section>

            <section class="content-grid">
                <article class="card">
                    <span class="section-label">Account Profile</span>
                    <h3>Update Account Information</h3>

                    <div class="account-meta">
                        <div class="account-meta-item">
                            <strong>Username</strong>
                            <span><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div class="account-meta-item">
                            <strong>Email</strong>
                            <span><?php echo htmlspecialchars($email); ?></span>
                        </div>
                    </div>

                    <form method="post" action="">
                        <div>
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        </div>

                        <div>
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        </div>

                        <div>
                            <label for="license_number">License Number</label>
                            <input type="text" id="license_number" name="license_number" value="<?php echo htmlspecialchars($license_number); ?>" required>
                        </div>

                        <div>
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter new password">
                        </div>

                        <button type="submit" class="btn">Update Account</button>
                    </form>

                    <?php if (isset($error_message)) { ?>
                        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php } ?>
                    <?php if (isset($_SESSION['success_message'])) { ?>
                        <p class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
                    <?php } ?>
                </article>

                <aside class="card">
                    <span class="section-label">Profile Notes</span>
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Clinical Identity</strong>
                            <p>Keep your name and license details current for consistent documentation context.</p>
                        </div>
                        <div class="info-item">
                            <strong>Password Updates</strong>
                            <p>Leave the password field blank if you do not want to change the current password.</p>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </main>
    <footer>
        <div class="container">
            <a href="privacy.html">Privacy Policy</a>
            <a href="terms.html">Terms of Service</a>
            &copy; 2024 MedNarrate. All rights reserved.
        </div>
    </footer>
</body>
</html>
