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
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($carrito_id <= 0 || $cantidad <= 0) {
        die('Datos inválidos');
    }

    
    $stmt = $pdo->prepare("SELECT id FROM carrito WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$carrito_id, current_user_id()]);
    if (!$stmt->fetch()) {
        die('Item no encontrado');
    }

    
    $upd = $pdo->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
    $upd->execute([$cantidad, $carrito_id]);

    header('Location: cart.php');
    exit;
}
?>