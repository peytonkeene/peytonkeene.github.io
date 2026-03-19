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

// Check if a task ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $taskId = $_POST['id'];

        // Delete the task from the database
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $taskId);

        if ($stmt->execute()) {
            header("Location: manage_tasks.php?message=Task deleted successfully");
            exit();
        } else {
            die("Error deleting task: " . $conn->error);
        }

        $stmt->close();
    } else {
        die("No task ID provided.");
    }
} else {
    die("Invalid request.");
}
?>
