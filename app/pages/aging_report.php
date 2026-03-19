<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include any necessary files, like your database connection
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html"); // Redirect to login if not authorized
    exit();
}

// Fetch Accounts Receivable Aging Data
$sql = "
    SELECT 
        CASE 
            WHEN DATEDIFF(NOW(), date_due) <= 30 THEN '1-30 Days'
            WHEN DATEDIFF(NOW(), date_due) BETWEEN 31 AND 60 THEN '31-60 Days'
            WHEN DATEDIFF(NOW(), date_due) BETWEEN 61 AND 90 THEN '61-90 Days'
            WHEN DATEDIFF(NOW(), date_due) > 90 THEN 'Over 90 Days'
        END AS aging_category,
        SUM(amount) AS total_amount
    FROM invoices
    WHERE status = 'overdue'
    GROUP BY aging_category;
";

$result = $conn->query($sql);
$aging_report = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $aging_report[] = $row;
    }
} else {
    $aging_report[] = ["aging_category" => "No Overdue Invoices", "total_amount" => 0];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Receivable Aging Report - MedNarrate</title>
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
        }

        .content {
            width: 100%;
            max-width: 800px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="manage_finances.php">Manage Finances</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <h1>Accounts Receivable Aging Report</h1>
            <table>
                <thead>
                    <tr>
                        <th>Aging Category</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aging_report as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['aging_category']); ?></td>
                            <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
