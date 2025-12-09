<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name(): string {
    return $_SESSION['user_name'] ?? '';
}

function current_user_role(): string {
    return $_SESSION['role'] ?? 'guest';
}

function is_logged_in(): bool {
    return current_user_id() !== null;
}

function is_admin(): bool {
    return current_user_role() === 'admin';
}

function require_login(): void {
    if (!is_logged_in()) {
        // Redirige al login con retorno a la URL actual
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header('Location: login.php?redirect=' . $redirect);
        exit;
    }
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: index.php');
        exit;
    }
}