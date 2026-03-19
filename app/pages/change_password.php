<?php
// Start session if needed
session_start();

// Include your database connection file
include 'db.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];

    // Hash the new password
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    
    if ($stmt === false) {
        die('Error preparing the statement: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("si", $password_hash, $user_id);

    // Execute the statement and check if successful
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User password updated successfully!";
    } else {
        $_SESSION['success_message'] = "Error: " . htmlspecialchars($stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the manage_users.html page
    header("Location: manage_users.html");
    exit();
}
?>
