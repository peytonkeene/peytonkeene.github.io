<?php $user = current_user(); ?>
<header class="topbar">
    <div>
        <p class="topbar-label">EMS Documentation Platform</p>
        <h1 class="topbar-title"><?= h($pageTitle ?? 'Dashboard') ?></h1>
    </div>
    <div class="topbar-user">
        <div>
            <p class="topbar-user-name"><?= h($user['name'] ?? 'User') ?></p>
            <p class="topbar-user-role"><?= h($user['role'] ?? 'Clinician') ?></p>
        </div>
    </div>
</header>
