<?php
require_once __DIR__ . '/_admin_bootstrap.php';

$generatorId = (int)($_GET['id'] ?? 0);
if ($generatorId < 1) {
    header('Location: /admin/generator-list.php');
    exit;
}

$pdo = db();
$generator = get_generator_with_structure($pdo, $generatorId);
if (!$generator) {
    http_response_code(404);
    echo 'Generator not found.';
    exit;
}

enforce_generator_access($generator);

$scope = agency_scope_sql('id');
$agencySql = 'SELECT id, name FROM agencies WHERE is_active = 1 AND ' . (is_superadmin() ? '1=1' : 'id = :agency_id') . ' ORDER BY name ASC';
$agencyStmt = $pdo->prepare($agencySql);
$agencyStmt->execute($scope['params']);
$agencies = $agencyStmt->fetchAll();

$formData = [
    'id' => $generator['id'],
    'name' => $generator['name'],
    'description' => $generator['description'],
    'is_active' => $generator['is_active'],
    'agency_id' => $generator['agency_id'],
    'template_content' => $generator['template']['template_content'] ?? '',
    'structure' => array_map(static function ($section) {
        return [
            'section_name' => $section['section_name'],
            'section_description' => $section['section_description'],
            'is_toggleable' => (int)$section['is_toggleable'],
            'default_open' => (int)$section['default_open'],
            'fields' => array_map(static function ($field) {
                return [
                    'field_name' => $field['field_name'],
                    'field_label' => $field['field_label'],
                    'field_slug' => $field['field_slug'],
                    'field_type' => $field['field_type'],
                    'placeholder_text' => $field['placeholder_text'],
                    'help_text' => $field['help_text'],
                    'is_required' => (int)$field['is_required'],
                    'is_active' => (int)$field['is_active'],
                    'options' => $field['options'],
                ];
            }, $section['fields']),
        ];
    }, $generator['sections']),
];

$pageTitle = 'Edit Generator';
$isEdit = true;
$actionUrl = '/admin/generator-update.php';
include __DIR__ . '/../includes/app_shell_start.php';
include __DIR__ . '/partials/generator-form.php';
include __DIR__ . '/../includes/app_shell_end.php';
