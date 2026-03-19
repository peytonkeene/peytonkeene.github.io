<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$activePage = 'account';
$pageTitle = 'Account';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/app-shell-start.php';
$title = 'Account Center';
$message = 'Profile, security, and preferences will live here for each authenticated user.';
require __DIR__ . '/../pages/placeholder.php';
require __DIR__ . '/../includes/app-shell-end.php';
