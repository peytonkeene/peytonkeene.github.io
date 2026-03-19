<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Get the group_id of the logged-in admin
$admin_group_id = $_SESSION['group_id'];

// Fetch users that belong to the same group as the admin
$sql = "SELECT first_name, last_name, username, email, license_number FROM users WHERE group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_group_id);
$stmt->execute();
$result = $stmt->get_result();

// Store users in an array
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    $no_users_message = "No users found.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --bg: #edf3f6;
            --surface: rgba(255, 255, 255, 0.94);
            --surface-soft: #f6fafc;
            --surface-strong: #ffffff;
            --text: #18242d;
            --text-soft: #5f7180;
            --label: #536575;
            --brand: #0f5f7a;
            --brand-strong: #0b4d63;
            --brand-border: #bed8e1;
            --border: rgba(215, 227, 233, 0.96);
            --border-strong: #c4d2db;
            --danger: #b83b49;
            --danger-soft: #fff4f5;
            --success: #1d6e4e;
            --success-soft: #e8f7ef;
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
            font-family: Inter, Roboto, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
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

        .container {
            width: min(1280px, calc(100% - 32px));
            margin: 0 auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 30;
            border-bottom: 1px solid rgba(196, 210, 219, 0.75);
            background: rgba(248, 251, 252, 0.88);
            backdrop-filter: blur(18px) saturate(160%);
        }

        .nav-shell {
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
            min-width: 0;
        }

        .brand img {
            height: 52px;
            width: auto;
            padding: 6px;
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--shadow-soft);
        }

        .brand-copy h1 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .eyebrow,
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .eyebrow,
        .section-label {
            margin-bottom: 8px;
            border: 1px solid var(--brand-border);
            background: rgba(227, 241, 246, 0.75);
            color: var(--brand-strong);
        }

        nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        nav a {
            padding: 10px 14px;
            border-radius: 999px;
            color: var(--text-soft);
            text-decoration: none;
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

        main {
            flex: 1;
            padding: 28px 0 40px;
        }

        .page-stack {
            display: grid;
            gap: 22px;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            padding: 30px;
            border: 1px solid rgba(190, 216, 225, 0.95);
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(15, 95, 122, 0.98), rgba(10, 64, 90, 0.94));
            color: #fff;
            box-shadow: var(--shadow);
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -8% -45% auto;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,.22), rgba(255,255,255,0));
            pointer-events: none;
        }

        .hero-card h2 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .hero-card p {
            margin: 0;
            max-width: 64ch;
            color: rgba(255,255,255,.84);
        }

        .panel {
            padding: 26px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }

        .panel h3 {
            margin: 0 0 8px;
            font-size: 1.45rem;
            letter-spacing: -0.02em;
        }

        .panel-intro {
            margin: 0 0 20px;
            color: var(--text-soft);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .summary-card {
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
        }

        .summary-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.5rem;
            letter-spacing: -0.03em;
        }

        .summary-card span {
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 760px;
        }

        thead th {
            padding: 16px 14px;
            text-align: left;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--label);
            background: #f3f8fb;
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 16px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text);
            font-size: 0.96rem;
        }

        tbody tr:hover {
            background: rgba(15, 95, 122, 0.035);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .action-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .edit-button,
        .delete-button,
        .back-button,
        .secondary-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.14s ease, box-shadow 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
        }

        .edit-button,
        .back-button {
            border: 1px solid transparent;
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
            box-shadow: 0 12px 24px rgba(15, 95, 122, 0.18);
        }

        .delete-button {
            border: 1px solid rgba(184, 59, 73, 0.22);
            background: var(--danger-soft);
            color: var(--danger);
        }

        .secondary-link {
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-soft);
        }

        .edit-button:hover,
        .delete-button:hover,
        .back-button:hover,
        .secondary-link:hover {
            transform: translateY(-1px);
        }

        .delete-button:hover {
            border-color: rgba(184, 59, 73, 0.34);
            background: #ffecee;
        }

        .empty-state {
            padding: 20px;
            border: 1px dashed var(--brand-border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
            color: var(--text-soft);
        }

        footer {
            border-top: 1px solid rgba(196, 210, 219, 0.85);
            background: rgba(18, 32, 43, 0.96);
            color: rgba(255,255,255,0.82);
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

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .nav-shell {
                min-height: auto;
                padding: 14px 0;
                flex-direction: column;
                align-items: flex-start;
            }

            nav { width: 100%; }
        }

        @media (max-width: 640px) {
            .container {
                width: min(100% - 20px, 1280px);
            }

            .hero-card,
            .panel {
                padding: 20px;
            }

            .brand img {
                height: 46px;
            }

            nav a {
                padding: 9px 12px;
                font-size: 0.9rem;
            }

            .action-group {
                flex-direction: column;
                align-items: stretch;
            }

            .edit-button,
            .delete-button,
            .back-button,
            .secondary-link {
                width: 100%;
            }
        }
    </style>
    <script>
        function confirmDelete(username) {
            if (confirm("Are you sure you want to delete this user?")) {
                window.location.href = "delete_user.php?username=" + encodeURIComponent(username);
            }
        }
    </script>
</head>
<body>
    <header class="site-header">
        <div class="container nav-shell">
            <a class="brand" href="admin_dashboard.html">
                <img src="images/logo.png" alt="MedNarrate Logo">
                <div class="brand-copy">
                    <span class="eyebrow">MedNarrate Admin Workspace</span>
                    <h1>Manage Users</h1>
                </div>
            </a>
            <nav>
                <a href="admin_dashboard.html">Dashboard</a>
                <a href="add_users.html">Add Users</a>
                <a class="active" href="manage_users.php">Manage Users</a>
                <a href="logout.php">Log Out</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container page-stack">
            <section class="hero-card">
                <span class="eyebrow">User Directory</span>
                <h2>Review and manage user accounts in a cleaner MedNarrate admin view.</h2>
                <p>View, edit, and manage MedNarrate user accounts. Administrators can update user information, assign roles, and remove access while maintaining established permissions and system controls.</p>
            </section>

            <section class="panel">
                <span class="section-label">Account Management</span>
                <h3>Manage Users</h3>
                <p class="panel-intro">View and manage users assigned to your administrative group. Administrators can update user information, adjust roles, or remove access as needed while maintaining existing permission structures.</p>

                <div class="summary-grid">
                    <div class="summary-card">
                        <strong><?php echo count($users); ?></strong>
                        <span>Users currently listed for this admin group</span>
                    </div>
                    <div class="summary-card">
                        <strong>Admin</strong>
                        <span>Access to this page is restricted to authorized administrators</span>
                    </div>
                    <div class="summary-card">
                        <strong>Live</strong>
                        <span>User data displayed reflects the current system records</span>
                    </div>
                </div>

                <?php if (isset($no_users_message)): ?>
                    <div class="empty-state">
                        <?php echo htmlspecialchars($no_users_message); ?>
                    </div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>License Number</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['license_number']); ?></td>
                                        <td>
                                            <a href="edit_user.php?username=<?php echo urlencode($user['username']); ?>" class="edit-button">Edit</a>
                                        </td>
                                        <td>
                                            <button onclick="confirmDelete('<?php echo urlencode($user['username']); ?>')" class="delete-button">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="action-group" style="margin-top: 18px;">
                    <a href="add_users.html" class="secondary-link">Add User</a>
                    <a href="admin_dashboard.html" class="back-button">Back to Dashboard</a>
                </div>
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
