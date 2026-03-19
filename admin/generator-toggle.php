<?php
require_once __DIR__ . '/_admin_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/generator-list.php');
    exit;
}

$generatorId = (int)($_POST['id'] ?? 0);
$isActive = (int)($_POST['is_active'] ?? 0) === 1 ? 1 : 0;
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

$stmt = $pdo->prepare('UPDATE generators SET is_active = :is_active, updated_at = NOW() WHERE id = :id');
$stmt->execute([
    ':is_active' => $isActive,
    ':id' => $generatorId,
]);

header('Location: /admin/generator-list.php');
exit;
