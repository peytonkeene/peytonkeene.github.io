<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../auth/role_helpers.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = db();
$scope = agency_scope_sql('g.agency_id');
$sql = 'SELECT g.*, a.name AS agency_name
    FROM generators g
    LEFT JOIN agencies a ON a.id = g.agency_id
    WHERE g.is_active = 1 AND ' . $scope['sql'] . '
    ORDER BY g.name ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($scope['params']);
$generators = $stmt->fetchAll();

$pageTitle = 'Generators';
include __DIR__ . '/../includes/app_shell_start.php';
?>
<div class="section-header-row">
    <h2>Available Narrative Generators</h2>
    <?php if (is_admin_or_superadmin()): ?>
        <a class="btn btn-secondary" href="/admin/generator-list.php">Manage Generators</a>
    <?php endif; ?>
</div>

<div class="grid-2">
    <?php foreach ($generators as $generator): ?>
        <article class="card info-card">
            <h3><?php echo htmlspecialchars($generator['name']); ?></h3>
            <p><?php echo htmlspecialchars((string)($generator['description'] ?? 'No description provided.')); ?></p>
            <div class="generator-meta">Agency: <?php echo htmlspecialchars((string)($generator['agency_name'] ?? 'Unassigned')); ?></div>
            <a class="btn btn-primary btn-sm" href="/generators/view.php?slug=<?php echo urlencode($generator['slug']); ?>">Open Generator</a>
        </article>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/app_shell_end.php'; ?>
