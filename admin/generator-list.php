<?php
require_once __DIR__ . '/_admin_bootstrap.php';

if (!function_exists('agency_scope_sql')) {
    http_response_code(500);
    echo 'Configuration error: agency scoping is unavailable.';
    exit;
}

$pdo = db();
$scope = agency_scope_sql('g.agency_id');
$sql = 'SELECT g.*, a.name AS agency_name, CONCAT(u.first_name, " ", u.last_name) AS created_by_name
    FROM narrative_generators g
    LEFT JOIN agencies a ON a.id = g.agency_id
    LEFT JOIN users u ON u.id = g.created_by_user_id
    WHERE ' . $scope['sql'] . '
    ORDER BY g.updated_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($scope['params']);
$generators = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Generator Management';
include __DIR__ . '/../includes/app_shell_start.php';
?>
<div class="section-header-row">
    <h2>Generator Management</h2>
    <a class="btn btn-primary" href="/admin/generator-builder.php">Create Generator</a>
</div>

<div class="card table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Agency</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Created</th>
                <th>Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($generators as $generator): ?>
            <tr>
                <td><?php echo htmlspecialchars($generator['name']); ?></td>
                <td><?php echo htmlspecialchars($generator['agency_name'] ?? 'Unassigned'); ?></td>
                <td><span class="status-pill <?php echo (int)$generator['is_active'] === 1 ? 'is-active' : 'is-inactive'; ?>"><?php echo (int)$generator['is_active'] === 1 ? 'Active' : 'Inactive'; ?></span></td>
                <td><?php echo htmlspecialchars(trim((string)($generator['created_by_name'] ?? '')) ?: 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars((string)$generator['created_at']); ?></td>
                <td><?php echo htmlspecialchars((string)$generator['updated_at']); ?></td>
                <td class="actions-cell">
                    <a class="btn btn-secondary btn-sm" href="/admin/generator-edit.php?id=<?php echo (int)$generator['id']; ?>">Edit</a>
                    <a class="btn btn-outline btn-sm" href="/generators/view.php?slug=<?php echo urlencode((string)($generator['slug'] ?? '')); ?>">Preview</a>
                    <form method="post" action="/admin/generator-toggle.php" class="inline-form">
                        <input type="hidden" name="id" value="<?php echo (int)$generator['id']; ?>">
                        <input type="hidden" name="is_active" value="<?php echo (int)$generator['is_active'] === 1 ? 0 : 1; ?>">
                        <button class="btn btn-outline btn-sm" type="submit"><?php echo (int)$generator['is_active'] === 1 ? 'Deactivate' : 'Activate'; ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/app_shell_end.php'; ?>
