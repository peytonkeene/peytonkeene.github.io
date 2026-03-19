<?php
// Start the session
session_start();

// Destroy the session, which logs the user out
session_destroy();

// Redirect to the login page after logout
header("Location: login.html");
exit();
?>
