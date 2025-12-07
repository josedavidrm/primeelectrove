<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';


require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        die('Token CSRF inválido');
    }

    $id_producto = (int)($_POST['id_producto'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_producto <= 0 || $cantidad <= 0) {
        die('Datos inválidos');
    }

    
    $stmt = $pdo->prepare("SELECT id, stock FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();

    if (!$producto) {
        die('Producto no encontrado');
    }

    if ($producto['stock'] < $cantidad) {
        die('Stock insuficiente');
    }

    $id_usuario = current_user_id();

    
    $stmt = $pdo->prepare("SELECT id, cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
    $stmt->execute([$id_usuario, $id_producto]);
    $item = $stmt->fetch();

    if ($item) {
        
        $nueva_cantidad = $item['cantidad'] + $cantidad;
        $upd = $pdo->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        $upd->execute([$nueva_cantidad, $item['id']]);
    } else {
        
        $ins = $pdo->prepare("INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)");
        $ins->execute([$id_usuario, $id_producto, $cantidad]);
    }


    header('Location: cart.php');
    exit;
}
?>