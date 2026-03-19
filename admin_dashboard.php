<?php
// Start session management
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Include the HTML for the admin dashboard
include 'admin_dashboard.html';
