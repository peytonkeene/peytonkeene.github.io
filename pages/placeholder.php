<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $message */
?>
<div class="placeholder-card">
    <h2><?= h($title ?? 'Module') ?></h2>
    <p><?= h($message ?? 'This module is ready for future development.') ?></p>
</div>
