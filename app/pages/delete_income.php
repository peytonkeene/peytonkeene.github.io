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

// Get the income ID from the URL
$income_id = $_GET['id'];

// Prepare the SQL statement to delete the income
$sql = "DELETE FROM incomes WHERE income_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $income_id);

if ($stmt->execute()) {
    header("Location: interactive_budget.php?success=Income deleted successfully");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
