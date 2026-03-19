<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $budget_type = $_POST['budget_type']; // 'monthly' or 'yearly'

    try {
        if ($budget_type == 'monthly') {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $sql = "INSERT INTO expected_expenses (description, amount, date, year, month) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("sdsii", $description, $amount, $date, $year, $month);
        } else {
            $year = date('Y', strtotime($date));
            $sql = "INSERT INTO expected_expenses (description, amount, date, year) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("sdsi", $description, $amount, $date, $year);
        }

        if ($stmt->execute()) {
            header("Location: interactive_budget.php?view=$budget_type");
            exit();
        } else {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expected Expense - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add Expected Expense</h1>
        <form method="POST">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" required>

            <label for="amount">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>

            <label for="budget_type">Budget Type</label>
            <select id="budget_type" name="budget_type" required>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>

            <button type="submit" class="btn">Add Expected Expense</button>
        </form>
    </div>
</body>
</html>
