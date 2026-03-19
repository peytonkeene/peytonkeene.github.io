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

// Get the user's ID and group ID from the session
$user_id = $_SESSION['user_id'];
$group_id = $_SESSION['group_id'];

// Fetch the narrative generators available to the user's group
$sql = "
    SELECT ng.generator_name, ng.generator_page
    FROM group_generators gg
    JOIN narrative_generators ng ON gg.generator_id = ng.generator_id
    WHERE gg.group_id = ?
";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$narrative_generators = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Narrative Generators</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --bg: #edf3f6;
            --surface: rgba(255, 255, 255, 0.94);
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
        .generator-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 14px;
        }
        .generator-list li {
            padding: 0;
            border: none;
            background: transparent;
        }
        .generator-link,
        .generator-empty {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: linear-gradient(180deg, #fff, #f8fbfd);
            text-decoration: none;
        }
        .generator-link {
            transition: transform 0.14s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .generator-link:hover {
            transform: translateY(-1px);
            border-color: var(--brand-border);
            box-shadow: 0 14px 28px rgba(15, 95, 122, 0.10);
        }
        .generator-link strong,
        .generator-empty strong {
            display: block;
            color: var(--text);
            font-size: 1rem;
        }
        .generator-link span,
        .generator-empty span {
            color: var(--text-soft);
            font-size: 0.95rem;
        }
        .generator-arrow {
            color: var(--brand);
            font-size: 1.2rem;
            font-weight: 800;
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
        @media (max-width: 860px) {
            .nav { min-height: auto; padding: 14px 0; flex-direction: column; align-items: flex-start; }
            nav { width: 100%; }
        }
        @media (max-width: 640px) {
            .container { width: min(100% - 20px, 1180px); }
            .hero-card, .card { padding: 20px; }
            .brand img { height: 46px; }
            nav a { padding: 9px 12px; font-size: 0.9rem; }
            .generator-link, .generator-empty { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="dashboard.php">
                <img src="images/logo.png" alt="MedNarrate Logo">
                <div>
                    <span class="eyebrow">MedNarrate Clinical Workspace</span>
                    <h1>Available Narrative Generators</h1>
                </div>
            </a>
            <nav>
                <a class="active" href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="logout">Log Out</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container page-stack">
            <section class="hero-card">
                <span class="eyebrow">Generator Access</span>
                <h2>Open the narrative generators assigned to your organization.</h2>
                <p>Choose from the generators currently available to your group and continue directly into the existing narrative workflow.</p>
            </section>

            <section class="card">
                <span class="section-label">Assigned Generators</span>
                <h3>Your Available Narrative Generators</h3>
                <ul class="generator-list">
                    <?php if (empty($narrative_generators)): ?>
                        <li>
                            <div class="generator-empty">
                                <div>
                                    <strong>No narrative generators assigned</strong>
                                    <span>There are currently no generators available to your account.</span>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach ($narrative_generators as $generator): ?>
                            <li>
                                <a class="generator-link" href="<?php echo htmlspecialchars($generator['generator_page']); ?>">
                                    <div>
                                        <strong><?php echo htmlspecialchars($generator['generator_name']); ?></strong>
                                        <span>Open this narrative generator in the current workflow.</span>
                                    </div>
                                    <span class="generator-arrow">&rsaquo;</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">&copy; 2024 MedNarrate. All rights reserved.</div>
    </footer>
</body>
</html>
