<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();

$pageTitle = 'Checkout | PrimeElectrove';
require_once __DIR__ . '/header.php';

$id_usuario = current_user_id();


$sql = "
  SELECT c.id as carrito_id, c.cantidad, p.precio_producto
  FROM carrito c
  INNER JOIN productos p ON c.id_producto = p.id
  WHERE c.id_usuario = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$items = $stmt->fetchAll();

if (!$items) {
    echo '<div class="alert">Tu carrito está vacío. <a href="index.php">Ver productos</a></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}


$total = 0;
foreach ($items as $item) {
    $total += $item['precio_producto'] * $item['cantidad'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        die('Token CSRF inválido');
    }

    
    $ins = $pdo->prepare("
        INSERT INTO facturas (id_usuario, id_carrito, precio_final)
        VALUES (?, NULL, ?)
    ");
    $ins->execute([$id_usuario, $total]);
    
    $factura_id = (int)$pdo->lastInsertId();

    
    $del = $pdo->prepare("DELETE FROM carrito WHERE id_usuario = ?");
    $del->execute([$id_usuario]);

    
    header('Location: invoice.php?id=' . $factura_id);
    exit;
}
?>

<h1>Confirmar compra</h1>

<div class="card" style="padding:20px; max-width:600px;">
    <h2>Resumen de tu pedido</h2>
    <p>Estás a punto de confirmar tu compra por un total de:</p>
    <h2 style="color:#00d4ff; margin:16px 0;"><?= money($total) ?></h2>

    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <button class="btn" type="submit" style="width:100%; padding:12px; font-size:1.1rem;">
            Confirmar y generar factura
        </button>
    </form>

    <p style="margin-top:16px;">
        <a href="cart.php">← Volver al carrito</a>
    </p>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>