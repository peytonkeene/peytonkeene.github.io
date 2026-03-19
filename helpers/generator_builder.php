<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/generator_access.php';

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'generator';
}

function unique_generator_slug(PDO $pdo, string $name, ?int $excludeId = null): string
{
    $base = slugify($name);
    $slug = $base;
    $counter = 1;

    while (true) {
        $sql = 'SELECT id FROM narrative_generators WHERE slug = :slug';
        if ($excludeId) {
            $sql .= ' AND id != :exclude_id';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();

        if (!$stmt->fetch()) {
            return $slug;
        }

        $counter++;
        $slug = $base . '-' . $counter;
    }
}

function sanitize_field_rows(array $fields, int $sectionId, int $generatorId): array
{
    $rows = [];
    $order = 1;

    foreach ($fields as $field) {
        $fieldName = trim((string)($field['field_name'] ?? ''));
        if ($fieldName === '') {
            continue;
        }

        $fieldType = (string)($field['field_type'] ?? 'text');
        $allowedTypes = [
            'text', 'textarea', 'select', 'checkbox', 'radio', 'toggle_section', 'hidden',
            'chip_selector', 'multi_select_treatments', 'skin_condition_group', 'yes_no_toggle',
            'modal_trigger_block', 'admin_note', 'output_helper_text'
        ];

        if (!in_array($fieldType, $allowedTypes, true)) {
            $fieldType = 'text';
        }

        $options = $field['options'] ?? [];
        $cleanOptions = [];
        if (is_array($options)) {
            foreach ($options as $option) {
                $opt = trim((string)$option);
                if ($opt !== '') {
                    $cleanOptions[] = $opt;
                }
            }
        }

        $rows[] = [
            'generator_id' => $generatorId,
            'section_id' => $sectionId,
            'field_name' => $fieldName,
            'field_label' => trim((string)($field['field_label'] ?? $fieldName)),
            'field_slug' => slugify((string)($field['field_slug'] ?? $fieldName)),
            'field_type' => $fieldType,
            'field_order' => (int)($field['field_order'] ?? $order),
            'placeholder_text' => trim((string)($field['placeholder_text'] ?? '')),
            'help_text' => trim((string)($field['help_text'] ?? '')),
            'is_required' => !empty($field['is_required']) ? 1 : 0,
            'is_active' => isset($field['is_active']) ? (!empty($field['is_active']) ? 1 : 0) : 1,
            'options_json' => json_encode($cleanOptions),
            'config_json' => json_encode(is_array($field['config'] ?? null) ? $field['config'] : []),
        ];

        $order++;
    }

    return $rows;
}

function get_generator_with_structure(PDO $pdo, int $generatorId): ?array
{
    $stmt = $pdo->prepare('SELECT g.*, a.name AS agency_name, CONCAT(u.first_name, " ", u.last_name) AS created_by_name
        FROM narrative_generators g
        LEFT JOIN agencies a ON a.id = g.agency_id
        LEFT JOIN users u ON u.id = g.created_by_user_id
        WHERE g.id = :id LIMIT 1');
    $stmt->execute([':id' => $generatorId]);
    $generator = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$generator) {
        return null;
    }

    $sectionStmt = $pdo->prepare('SELECT * FROM generator_sections WHERE generator_id = :id ORDER BY section_order ASC, id ASC');
    $sectionStmt->execute([':id' => $generatorId]);
    $sections = $sectionStmt->fetchAll(PDO::FETCH_ASSOC);

    $fieldStmt = $pdo->prepare('SELECT * FROM generator_fields WHERE generator_id = :id AND is_active = 1 ORDER BY field_order ASC, id ASC');
    $fieldStmt->execute([':id' => $generatorId]);
    $fields = $fieldStmt->fetchAll(PDO::FETCH_ASSOC);

    $templateStmt = $pdo->prepare('SELECT * FROM generator_templates WHERE generator_id = :id ORDER BY id DESC LIMIT 1');
    $templateStmt->execute([':id' => $generatorId]);
    $template = $templateStmt->fetch(PDO::FETCH_ASSOC);

    $bySection = [];
    foreach ($fields as $field) {
        $field['options'] = json_decode((string)$field['options_json'], true) ?: [];
        $field['config'] = json_decode((string)$field['config_json'], true) ?: [];
        $bySection[(int)$field['section_id']][] = $field;
    }

    foreach ($sections as &$section) {
        $section['fields'] = $bySection[(int)$section['id']] ?? [];
    }
    unset($section);

    $generator['sections'] = $sections;
    $generator['template'] = $template;

    return $generator;
}
