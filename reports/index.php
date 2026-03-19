<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$activePage = 'reports';
$pageTitle = 'Reports';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/app-shell-start.php';
$title = 'Reporting';
$message = 'Reporting modules will be added here for QA trends, staffing insights, and documentation analytics.';
require __DIR__ . '/../pages/placeholder.php';
require __DIR__ . '/../includes/app-shell-end.php';
