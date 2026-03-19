<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (empty($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}
