<?php
require_once __DIR__ . '/../auth/role_helpers.php';

function can_access_generator(array $generator): bool
{
    if (is_superadmin()) {
        return true;
    }

    $agencyId = current_user_agency_id();
    return isset($generator['agency_id']) && (int)$generator['agency_id'] === (int)$agencyId;
}

function enforce_generator_access(array $generator): void
{
    if (!can_access_generator($generator)) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}
