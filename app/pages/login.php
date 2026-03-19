<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Select user information based on username
    $sql = "SELECT u.user_id, u.username, u.password_hash, u.role, u.status, u.group_id, u.license_number 
            FROM users u
            WHERE u.username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $username, $password_hash, $role, $status, $group_id, $license_number);
        $stmt->fetch();

        if ($status == 'inactive') {
            header("Location: login.html?error=" . urlencode("Account inactive. Please contact your administrator."));
            exit();
        } elseif (password_verify($password, $password_hash)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['group_id'] = $group_id;
            $_SESSION['license_number'] = $license_number; // Add license number to session

            // Redirect based on role
            if ($role === 'controller') {
                header("Location: controller_dashboard.html");
            } elseif ($role === 'admin') {
                header("Location: admin_dashboard.html");
            } else {
                header("Location: dashboard.html");
            }
            exit();
        } else {
            header("Location: login.html?error=" . urlencode("Invalid password."));
            exit();
        }
    } else {
        header("Location: login.html?error=" . urlencode("No user found with the provided credentials."));
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
