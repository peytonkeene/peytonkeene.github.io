<?php
// Start session
session_start();

// Include the database connection file
include 'db.php';

// Check if the user is logged in, has a group_id of 3, and has the role of 'controller'
if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3 || $_SESSION['role'] !== 'controller') {
    header("Location: login.html");
    exit();
}

// Delete client from the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientId = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->bind_param("i", $clientId);

    if ($stmt->execute()) {
        header("Location: manage_clients.php");
        exit();
    } else {
        die("Error deleting client: " . $conn->error);
    }

    $stmt->close();
}
?>
