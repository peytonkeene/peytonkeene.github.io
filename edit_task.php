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

// Fetch task details
$taskId = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    die("Task not found.");
}

// Fetch clients for the client dropdown
$clientQuery = "SELECT id, name FROM clients";
$clientResult = $conn->query($clientQuery);
$clients = $clientResult->fetch_all(MYSQLI_ASSOC);

// Initialize variables for form validation
$taskDescription = $task['task_description'];
$assignedTo = $task['assigned_to'];
$dueDate = $task['due_date'];
$clientId = $task['client_id'];
$status = $task['status'];
$descriptionError = $clientError = $successMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate task description
    if (empty($_POST["task_description"])) {
        $descriptionError = "Task description is required.";
    } else {
        $taskDescription = trim($_POST["task_description"]);
    }

    // Validate client selection
    if (empty($_POST["client_id"])) {
        $clientError = "You must select a client.";
    } else {
        $clientId = $_POST["client_id"];
    }

    // Get assigned to, due date, and status
    $assignedTo = trim($_POST["assigned_to"]);
    $dueDate = $_POST["due_date"];
    $status = $_POST["status"];

    // If no errors, update task in the database
    if (empty($descriptionError) && empty($clientError)) {
        $stmt = $conn->prepare("UPDATE tasks SET client_id = ?, task_description = ?, assigned_to = ?, due_date = ?, status = ? WHERE id = ?");
        $stmt->bind_param("issssi", $clientId, $taskDescription, $assignedTo, $dueDate, $status, $taskId);

        if ($stmt->execute()) {
            $successMessage = "Task updated successfully!";
        } else {
            die("Error updating task: " . $conn->error);
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - MedNarrate CRM</title>
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
            max-width: 600px;
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

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            text-align: left;
        }

        input[type="text"], input[type="date"], select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: left;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
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
            <h1>Edit Task</h1>
            <?php if (!empty($successMessage)): ?>
                <div class="success"><?= $successMessage ?></div>
            <?php endif; ?>
            <form action="edit_task.php?id=<?= $taskId ?>" method="POST">
                <label for="task_description">Task Description:</label>
                <input type="text" id="task_description" name="task_description" value="<?= htmlspecialchars($taskDescription) ?>">
                <div class="error"><?= $descriptionError ?></div>

                <label for="client_id">Assign to Client:</label>
                <select id="client_id" name="client_id">
                    <option value="">Select a client</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= ($clientId == $client['id']) ? 'selected' : '' ?>><?= htmlspecialchars($client['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="error"><?= $clientError ?></div>

                <label for="assigned_to">Assigned To:</label>
                <input type="text" id="assigned_to" name="assigned_to" value="<?= htmlspecialchars($assignedTo) ?>">

                <label for="due_date">Due Date:</label>
                <input type="date" id="due_date" name="due_date" value="<?= htmlspecialchars($dueDate) ?>">

                <label for="status">Task Status:</label>
                <select id="status" name="status">
                    <option value="Pending" <?= ($status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?= ($status == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Completed" <?= ($status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>

                <button type="submit">Update Task</button>
            </form>
            <a href="manage_tasks.php" class="back-button">Back to Manage Tasks</a>
        </div>
    </main>
</body>
</html>
