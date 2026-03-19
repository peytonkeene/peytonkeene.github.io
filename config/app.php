<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const APP_NAME = 'PulseChart EMS';
const APP_URL = '';

function app_url(string $path = ''): string
{
    if (APP_URL !== '') {
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }

    return '/' . ltrim($path, '/');
}
