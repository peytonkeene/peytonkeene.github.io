<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['group_id'] != 3) {
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM expected_incomes WHERE expected_income_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: interactive_budget.php?view=monthly");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
