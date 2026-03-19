<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../auth/role_helpers.php';

if (!is_admin_or_superadmin()) {
    header('Location: /dashboard.php');
    exit;
}

header('Location: /admin/generator-list.php');
exit;
