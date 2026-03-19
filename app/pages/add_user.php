<?php
session_start();
require 'db.php'; // Ensure your database connection file is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $group_id = $_SESSION['group_id']; // Ensure this is set in the session
    $role = $_POST['role'];

    // Prepare and execute query to add user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, group_id, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssis", $username, $email, $password, $group_id, $role);

    if ($stmt->execute()) {
        // Redirect to a user created confirmation page
        header('Location: user_created.html');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
