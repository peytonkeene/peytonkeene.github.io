<?php
require_once __DIR__ . '/_admin_bootstrap.php';

$pdo = db();
$scope = agency_scope_sql('id');
$agencySql = 'SELECT id, name FROM agencies WHERE is_active = 1 AND ' . (is_superadmin() ? '1=1' : 'id = :agency_id') . ' ORDER BY name ASC';
$agencyStmt = $pdo->prepare($agencySql);
$agencyStmt->execute($scope['params']);
$agencies = $agencyStmt->fetchAll(PDO::FETCH_ASSOC);

$formData = [
    'name' => '',
    'slug' => '',
    'description' => '',
    'is_active' => 1,
    'agency_id' => current_user_agency_id(),
    'template_content' => '',
    'structure' => [],
];

$pageTitle = 'Create Generator';
$isEdit = false;
$actionUrl = '/admin/generator-save.php';
include __DIR__ . '/../includes/app_shell_start.php';
include __DIR__ . '/partials/generator-form.php';
include __DIR__ . '/../includes/app_shell_end.php';
