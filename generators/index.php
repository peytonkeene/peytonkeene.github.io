<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$activePage = 'generators';
$pageTitle = 'Narrative Generators';
require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/app-shell-start.php';
?>
<div class="placeholder-card">
    <h2>Narrative Generator Workspace</h2>
    <p>This section is reserved for future narrative generator modules. Add new generator pages in <strong>/generators/</strong> and link them from cards below.</p>
</div>
<div class="grid-cards">
    <button class="feature-card feature-card-button" type="button">Medical Narrative Generator (Placeholder)</button>
    <button class="feature-card feature-card-button" type="button">Trauma Narrative Generator (Placeholder)</button>
    <button class="feature-card feature-card-button" type="button">Refusal Narrative Generator (Placeholder)</button>
</div>
<?php require __DIR__ . '/../includes/app-shell-end.php'; ?>
