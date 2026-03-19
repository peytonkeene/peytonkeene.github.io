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

// Get the admin's group_id
$admin_group_id = $_SESSION['group_id'];

// Get the user_id from the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare and execute the SQL statement to delete the user
    $sql = "DELETE FROM users WHERE user_id = ? AND group_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $admin_group_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete user.";
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

// Redirect back to manage users page
header("Location: manage_users.php");
exit();
?>
