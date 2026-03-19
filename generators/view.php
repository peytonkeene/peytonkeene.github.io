<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../auth/role_helpers.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../helpers/generator_builder.php';
require_once __DIR__ . '/../helpers/generator_access.php';
require_once __DIR__ . '/../helpers/generator_renderer.php';

if (!function_exists('agency_scope_sql') || !function_exists('get_generator_with_structure') || !function_exists('enforce_generator_access')) {
    http_response_code(500);
    echo 'Configuration error: generator helpers are unavailable.';
    exit;
}

$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: /generators/index.php');
    exit;
}

$pdo = db();
$scope = agency_scope_sql('g.agency_id');
$stmt = $pdo->prepare('SELECT g.id
    FROM narrative_generators g
    WHERE g.slug = :slug AND g.is_active = 1 AND ' . $scope['sql'] . '
    LIMIT 1');
$params = $scope['params'];
$params[':slug'] = $slug;
$stmt->execute($params);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo 'Generator not found or inactive.';
    exit;
}

$generator = get_generator_with_structure($pdo, (int)$row['id']);
if (!$generator) {
    http_response_code(404);
    echo 'Generator not found.';
    exit;
}
enforce_generator_access($generator);

$generatedNarrative = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $templateContent = (string)($generator['template']['template_content'] ?? '');
    $input = $_POST;
    unset($input['_submit']);
    $generatedNarrative = build_narrative_from_template($templateContent, $input);
}

$pageTitle = (string)($generator['name'] ?? 'Generator');
include __DIR__ . '/../includes/app_shell_start.php';
?>
<div class="section-header-row">
    <h2><?php echo htmlspecialchars((string)($generator['name'] ?? 'Untitled Generator')); ?></h2>
    <a class="btn btn-outline" href="/generators/index.php">Back to Generators</a>
</div>
<p class="muted"><?php echo htmlspecialchars((string)($generator['description'] ?? '')); ?></p>

<div class="builder-layout">
    <div class="builder-main">
        <form method="post" class="card builder-card">
            <?php foreach ($generator['sections'] as $section): ?>
                <section class="generator-section">
                    <div class="section-header-row">
                        <h3><?php echo htmlspecialchars($section['section_name']); ?></h3>
                        <?php if (!empty($section['is_toggleable'])): ?>
                            <span class="status-pill is-active">Toggleable</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($section['section_description'])): ?>
                        <p class="muted"><?php echo htmlspecialchars($section['section_description']); ?></p>
                    <?php endif; ?>
                    <div class="field-grid">
                        <?php foreach ($section['fields'] as $field): ?>
                            <?php echo render_dynamic_field($field); ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <button class="btn btn-primary" type="submit" name="_submit" value="1">Generate Narrative</button>
        </form>
    </div>

    <aside class="builder-sidebar">
        <article class="card builder-card sticky-card">
            <h3>Narrative Output</h3>
            <?php if ($generatedNarrative !== ''): ?>
                <textarea readonly class="template-editor"><?php echo htmlspecialchars($generatedNarrative); ?></textarea>
            <?php else: ?>
                <p class="muted">Complete the fields and click <strong>Generate Narrative</strong>.</p>
            <?php endif; ?>
        </article>
    </aside>
</div>
<?php include __DIR__ . '/../includes/app_shell_end.php'; ?>
