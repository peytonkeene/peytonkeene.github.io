<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = $_POST['emailOrUsername'];

    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the email or username exists in the database
    $sql = "SELECT user_id, email FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $email);
        $stmt->fetch();

        // Generate a unique reset token
        $resetToken = bin2hex(random_bytes(50)); // 100 char token
        $resetLink = "https://mednarrate.net/reset_password.php?token=" . $resetToken;

        // Store the token in the database, remove any existing token for the user
        $expire_at = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token valid for 1 hour
        $sql = "REPLACE INTO password_resets (user_id, token, expire_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $resetToken, $expire_at);
        $stmt->execute();

        // Send the reset link to the user's email
        $to = $email;
        $subject = "Password Reset Request";
        $message = "Hi, please click the following link to reset your password: " . $resetLink;
        $headers = "From: no-reply@mednarrate.net\r\n" .
                   "Reply-To: no-reply@mednarrate.net\r\n" .
                   "Content-Type: text/plain; charset=utf-8\r\n";

        if (mail($to, $subject, $message, $headers)) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Failed to send reset email. Please try again later.";
        }
    } else {
        echo "No user found with the provided email or username.";
    }

    $stmt->close();
    $conn->close();
}
?>
