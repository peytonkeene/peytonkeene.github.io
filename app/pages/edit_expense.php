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

// Get the expense ID from the URL
$expense_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated data from the form submission
    $description = $_POST['description'];
    $date = $_POST['date'];
    $amount = $_POST['amount'];

    // Prepare the SQL statement to update the expense
    $sql = "UPDATE expenses SET description = ?, date = ?, amount = ? WHERE expense_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssdi', $description, $date, $amount, $expense_id);

    if ($stmt->execute()) {
        header("Location: expense_manager.php?success=Expense updated successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Fetch the existing expense data for pre-filling the form
    $sql = "SELECT * FROM expenses WHERE expense_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expense = $result->fetch_assoc();

    if (!$expense) {
        echo "Expense not found.";
        exit();
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
    <title>Edit Expense - MedNarrate</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon"> <!-- Favicon -->
    <style>
        /* Include the same styles as the Invoice Manager to keep the design consistent */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            width: 100%;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            text-align: center;
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
            width: 100%;
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

        form input, form textarea {
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
            <a href="expense_manager.php">Back to Expense Manager</a>
            <a href="logout.php">Log Out</a>
        </nav>
    </header>
    
    <main>
        <div class="content">
            <h1>Edit Expense</h1>
            <form action="edit_expense.php?id=<?php echo htmlspecialchars($expense_id); ?>" method="POST">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($expense['description']); ?>" required>

                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($expense['date']); ?>" required>

                <label for="amount">Amount:</label>
                <input type="number" step="0.01" id="amount" name="amount" value="<?php echo htmlspecialchars($expense['amount']); ?>" required>

                <button type="submit">Update Expense</button>
            </form>
        </div>
    </main>
</body>
</html>
