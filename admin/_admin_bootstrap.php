<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../auth/role_helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../helpers/generator_builder.php';
require_once __DIR__ . '/../helpers/generator_access.php';

require_admin_or_superadmin();
