<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        die('Token CSRF inválido');
    }

    $carrito_id = (int)($_POST['carrito_id'] ?? 0);

    if ($carrito_id <= 0) {
        die('Datos inválidos');
    }

    
    $stmt = $pdo->prepare("SELECT id FROM carrito WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$carrito_id, current_user_id()]);
    if (!$stmt->fetch()) {
        die('Item no encontrado');
    }

    
    $del = $pdo->prepare("DELETE FROM carrito WHERE id = ?");
    $del->execute([$carrito_id]);

    header('Location: cart.php');
    exit;
}
?>