<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$target = is_authenticated() ? 'dashboard.php' : 'login.php';
header('Location: ' . app_url($target));
exit;
