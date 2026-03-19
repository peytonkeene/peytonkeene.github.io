<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

// Check if the user is logged in and is part of group 3 (controller)
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_name = $_POST['client_name'];
    $client_email = $_POST['client_email'];
    $date_created = $_POST['date_created'];
    $date_due = $_POST['date_due'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    // Prepare the SQL statement
    $sql = "INSERT INTO invoices (client_name, client_email, date_created, date_due, amount, status, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $client_name, $client_email, $date_created, $date_due, $amount, $status, $description);

    if ($stmt->execute()) {
        header("Location: invoice_manager.php?success=Invoice created successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"> <!-- Favicon -->
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
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <img src="images/logo.png" alt="MedNarrate Logo">
        <nav>
            <a href="invoice_manager.php">Back to Invoice Manager</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>
    
    <main>
        <div class="content">
            <h1>Create New Invoice</h1>
            <form action="create_invoice.php" method="POST">
                <label for="client_name">Client Name:</label>
                <input type="text" id="client_name" name="client_name" required>

                <label for="client_email">Client Email:</label>
                <input type="email" id="client_email" name="client_email" required>

                <label for="date_created">Date Created:</label>
                <input type="date" id="date_created" name="date_created" required>

                <label for="date_due">Date Due:</label>
                <input type="date" id="date_due" name="date_due" required>

                <label for="amount">Amount:</label>
                <input type="number" step="0.01" id="amount" name="amount" required>

                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>

                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>

                <button type="submit">Create Invoice</button>
            </form>
        </div>
    </main>
</body>
</html>
