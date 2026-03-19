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

// Get user role from the session
$role = $_SESSION['role'];

// Redirect based on role
if ($role === 'controller') {
    header("Location: controller_dashboard.html");
} elseif ($role === 'admin') {
    header("Location: admin_dashboard.html");
} else {
    header("Location: dashboard.html"); // For regular users
}
exit();
?>
