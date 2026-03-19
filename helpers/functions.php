<?php

declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function active_nav(string $item, string $current): string
{
    return $item === $current ? 'is-active' : '';
}
