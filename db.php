<?php
// Database configuration
define('DB_SERVER', 'localhost'); // Database server, usually 'localhost'
define('DB_USERNAME', 'PKeene'); // Replace with your actual database username
define('DB_PASSWORD', 'Peyt52524343!'); // Replace with your actual database password
define('DB_NAME', 'mednarrate'); // The name of your database

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
