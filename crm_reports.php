<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in and has the role of 'controller'
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3 || $_SESSION['role'] !== 'controller') {
    header("Location: login.html");
    exit();
}

// Fetch interaction summary
$interactionSummaryQuery = "SELECT interaction_type, COUNT(*) as total FROM interactions GROUP BY interaction_type";
$interactionSummaryResult = $conn->query($interactionSummaryQuery);

// Fetch clients with most interactions
$topClientsQuery = "SELECT clients.name, COUNT(*) as total FROM interactions
                    JOIN clients ON interactions.client_id = clients.id
                    GROUP BY clients.name
                    ORDER BY total DESC LIMIT 5";
$topClientsResult = $conn->query($topClientsQuery);

// Fetch task overview (completed vs pending)
$taskOverviewQuery = "SELECT status, COUNT(*) as total FROM tasks GROUP BY status";
$taskOverviewResult = $conn->query($taskOverviewQuery);

// Tasks due this week
$tasksDueQuery = "SELECT task_description, due_date FROM tasks WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
$tasksDueResult = $conn->query($tasksDueQuery);

// Error handling
if (!$interactionSummaryResult || !$topClientsResult || !$taskOverviewResult || !$tasksDueResult) {
    die("Query failed: " . $conn->error);
}

// Prepare data
$interactionSummary = $interactionSummaryResult->fetch_all(MYSQLI_ASSOC);
$topClients = $topClientsResult->fetch_all(MYSQLI_ASSOC);
$taskOverview = $taskOverviewResult->fetch_all(MYSQLI_ASSOC);
$tasksDue = $tasksDueResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Reports - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header styling */
        header {
            background-color: #ffffff;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            flex-direction: column;
            position: relative;
            z-index: 10;
        }

        header img {
            height: 120px;
            margin-bottom: 10px;
        }

        nav {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
            z-index: 11;
        }

        nav a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #007bff;
        }

        /* Main content styling */
        main {
            flex: 1;
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            flex-direction: column;
        }

        .content {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .section {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="logout.php" class="logout">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>CRM Reports</h1>

            <!-- Interaction Summary -->
            <div class="section">
                <h2>Interaction Summary by Type</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Interaction Type</th>
                            <th>Total Interactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($interactionSummary as $summary): ?>
                            <tr>
                                <td><?= htmlspecialchars($summary['interaction_type']); ?></td>
                                <td><?= htmlspecialchars($summary['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top Clients by Interactions -->
            <div class="section">
                <h2>Top Clients by Number of Interactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Total Interactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topClients as $client): ?>
                            <tr>
                                <td><?= htmlspecialchars($client['name']); ?></td>
                                <td><?= htmlspecialchars($client['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Task Overview -->
            <div class="section">
                <h2>Task Overview (Completed vs Pending)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Total Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($taskOverview as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['status']); ?></td>
                                <td><?= htmlspecialchars($task['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tasks Due This Week -->
            <div class="section">
                <h2>Tasks Due This Week</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Task Description</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasksDue as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['task_description']); ?></td>
                                <td><?= htmlspecialchars($task['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <a href="crm.php" class="back-button">Back to CRM Dashboard</a>
        </div>
    </main>
</body>
</html>
