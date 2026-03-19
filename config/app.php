<?php
// Application configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'MedNarrate');
define('APP_URL', '/');
