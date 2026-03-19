<?php
require_once __DIR__ . '/_admin_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/generator-builder.php');
    exit;
}

$pdo = db();
$user = current_user();
$name = trim((string)($_POST['name'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$isActive = (int)($_POST['is_active'] ?? 0) === 1 ? 1 : 0;
$agencyId = is_superadmin() ? (int)($_POST['agency_id'] ?? 0) : (int)current_user_agency_id();
$templateContent = trim((string)($_POST['template_content'] ?? ''));
$structure = json_decode((string)($_POST['structure_json'] ?? '[]'), true);

if ($name === '' || $agencyId < 1 || !is_array($structure)) {
    header('Location: /admin/generator-builder.php');
    exit;
}

$slug = unique_generator_slug($pdo, $name);

$pdo->beginTransaction();
try {
    $insertGenerator = $pdo->prepare('INSERT INTO generators (agency_id, name, slug, description, is_active, created_by_user_id, created_at, updated_at)
        VALUES (:agency_id, :name, :slug, :description, :is_active, :created_by_user_id, NOW(), NOW())');
    $insertGenerator->execute([
        ':agency_id' => $agencyId,
        ':name' => $name,
        ':slug' => $slug,
        ':description' => $description,
        ':is_active' => $isActive,
        ':created_by_user_id' => (int)($user['id'] ?? 0),
    ]);

    $generatorId = (int)$pdo->lastInsertId();

    $sectionInsert = $pdo->prepare('INSERT INTO generator_sections
        (generator_id, section_name, section_slug, section_order, section_description, is_toggleable, default_open, created_at, updated_at)
        VALUES (:generator_id, :section_name, :section_slug, :section_order, :section_description, :is_toggleable, :default_open, NOW(), NOW())');

    $fieldInsert = $pdo->prepare('INSERT INTO generator_fields
        (generator_id, section_id, field_name, field_label, field_slug, field_type, field_order, placeholder_text, help_text, is_required, is_active, options_json, config_json, created_at, updated_at)
        VALUES (:generator_id, :section_id, :field_name, :field_label, :field_slug, :field_type, :field_order, :placeholder_text, :help_text, :is_required, :is_active, :options_json, :config_json, NOW(), NOW())');

    $sectionOrder = 1;
    foreach ($structure as $section) {
        $sectionName = trim((string)($section['section_name'] ?? ''));
        if ($sectionName === '') {
            continue;
        }

        $sectionInsert->execute([
            ':generator_id' => $generatorId,
            ':section_name' => $sectionName,
            ':section_slug' => slugify((string)($section['section_slug'] ?? $sectionName)),
            ':section_order' => (int)($section['section_order'] ?? $sectionOrder),
            ':section_description' => trim((string)($section['section_description'] ?? '')),
            ':is_toggleable' => !empty($section['is_toggleable']) ? 1 : 0,
            ':default_open' => !empty($section['default_open']) ? 1 : 0,
        ]);

        $sectionId = (int)$pdo->lastInsertId();
        $rows = sanitize_field_rows($section['fields'] ?? [], $sectionId, $generatorId);

        foreach ($rows as $row) {
            $fieldInsert->execute($row);
        }

        $sectionOrder++;
    }

    if ($templateContent !== '') {
        $templateStmt = $pdo->prepare('INSERT INTO generator_templates (generator_id, template_name, template_content, created_at, updated_at)
            VALUES (:generator_id, :template_name, :template_content, NOW(), NOW())');
        $templateStmt->execute([
            ':generator_id' => $generatorId,
            ':template_name' => 'Default Template',
            ':template_content' => $templateContent,
        ]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

header('Location: /admin/generator-list.php');
exit;
