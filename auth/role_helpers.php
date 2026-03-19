<?php

function current_user(): array
{
    return $_SESSION['user'] ?? [];
}

function current_user_role(): string
{
    return (string)(current_user()['role'] ?? 'user');
}

function current_user_agency_id(): ?int
{
    if (!isset(current_user()['agency_id']) || current_user()['agency_id'] === null || current_user()['agency_id'] === '') {
        return null;
    }

    return (int)current_user()['agency_id'];
}

function is_superadmin(): bool
{
    return current_user_role() === 'superadmin';
}

function is_admin_or_superadmin(): bool
{
    $role = current_user_role();
    return $role === 'admin' || $role === 'superadmin';
}

function require_admin_or_superadmin(): void
{
    if (!is_admin_or_superadmin()) {
        header('Location: /dashboard.php');
        exit;
    }
}

function agency_scope_sql(string $column = 'agency_id'): array
{
    if (is_superadmin()) {
        return ['sql' => '1=1', 'params' => []];
    }

    $agencyId = current_user_agency_id();
    return ['sql' => "$column = :agency_id", 'params' => [':agency_id' => $agencyId]];
}
