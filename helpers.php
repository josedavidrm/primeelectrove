<?php
require_once __DIR__ . '/session.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', ''); // Déjalo vacío si sirves directamente desde localhost/carpeta o localhost:3000
}

function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function money($n): string {
    return '$' . number_format((float)$n, 2, '.', ',');
}
