<?php
// cart.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();

$pageTitle = 'Mi Carrito | PrimeElectrove';
require_once __DIR__ . '/header.php';

$id_usuario = current_user_id();

// Obtener items del carrito con info del producto
$sql = "
  SELECT c.id as carrito_id, c.cantidad, p.id as producto_id, p.nombre_producto, p.precio_producto, p.imagen_url
  FROM carrito c
  INNER JOIN productos p ON c.id_producto = p.id
  WHERE c.id_usuario = ?
  ORDER BY c.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario]);
$items = $stmt->fetchAll();

$total = 0;
?>

<h1>Mi Carrito</h1>

<?php if (!$items): ?>
  <div class="card" style="padding:16px; max-width:600px;">
    <p>Tu carrito está vacío.</p>
    <a class="btn" href="index.php">Ver productos</a>
  </div>
<?php else: ?>
  <div class="card" style="padding:16px; max-width:900px;">
    
    <!-- Versión de tabla mejorada -->
    <table style="width:100%; border-collapse:collapse; color:#e5e7eb;">
      <thead>
        <tr style="border-bottom:2px solid #2a3042; background:#1a1f2d;">
          <th style="text-align:left; padding:12px; font-weight:600;">Producto</th>
          <th style="text-align:center; padding:12px; font-weight:600;">Cantidad</th>
          <th style="text-align:right; padding:12px; font-weight:600;">Precio Unit.</th>
          <th style="text-align:right; padding:12px; font-weight:600;">Subtotal</th>
          <th style="text-align:center; padding:12px; font-weight:600;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item):
          $subtotal = $item['precio_producto'] * $item['cantidad'];
          $total += $subtotal;
        ?>
        <tr style="border-bottom:1px solid #21273a;">
          <td style="padding:12px; color:#e5e7eb;">
            <strong><?= h($item['nombre_producto']) ?></strong>
          </td>
          <td style="text-align:center; padding:12px; color:#e5e7eb;">
            <form action="update_cart.php" method="post" style="display:inline-flex; align-items:center; gap:6px;">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="carrito_id" value="<?= $item['carrito_id'] ?>">
              <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" 
                     style="width:70px; padding:6px; border-radius:6px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb; text-align:center;">
              <button class="btn" type="submit" style="padding:6px 10px; font-size:0.85rem;">✓</button>
            </form>
          </td>
          <td style="text-align:right; padding:12px; color:#00d4ff; font-weight:600;">
            <?= money($item['precio_producto']) ?>
          </td>
          <td style="text-align:right; padding:12px; color:#00d4ff; font-weight:700; font-size:1.05rem;">
            <?= money($subtotal) ?>
          </td>
          <td style="text-align:center; padding:12px;">
            <form action="remove_from_cart.php" method="post" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="carrito_id" value="<?= $item['carrito_id'] ?>">
              <button class="btn secondary" type="submit" style="padding:6px 10px; font-size:0.85rem;">🗑️ Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Total -->
    <div style="margin-top:20px; padding-top:16px; border-top:2px solid #2a3042; text-align:right;">
      <h2 style="color:#00d4ff; margin:0;">Total: <?= money($total) ?></h2>
    </div>

    <!-- Botones de acción -->
    <div style="margin-top:20px; display:flex; gap:12px; justify-content:flex-end;">
      <a class="btn secondary" href="index.php">Seguir comprando</a>
      <a class="btn" href="checkout.php">Proceder al pago</a>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>