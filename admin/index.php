<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$activePage = 'admin';
$pageTitle = 'Admin';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/app-shell-start.php';
$title = 'Administration';
$message = 'Admin tools will be implemented here, including user management and organization-level settings.';
require __DIR__ . '/../pages/placeholder.php';
require __DIR__ . '/../includes/app-shell-end.php';
