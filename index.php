<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

$pageTitle = 'Productos | PrimeElectrove';
require_once __DIR__ . '/header.php';

$q = trim($_GET['q'] ?? '');
$params = [];
$sql = "SELECT id, nombre_producto, precio_producto, descripcion, imagen_url, stock FROM productos";
if ($q !== '') {
    $sql .= " WHERE nombre_producto LIKE ?";
    $params[] = "%$q%";
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();
?>
<h1>iPhones disponibles</h1>

<?php if ($q !== ''): ?>
  <p>Resultados para: "<?= h($q) ?>" (<?= count($productos) ?>)</p>
<?php endif; ?>

<?php if (!$productos): ?>
  <p>No hay productos para mostrar.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($productos as $p): ?>
      <div class="card">
        <?php
          $img = $p['imagen_url'] ?: 'https://via.placeholder.com/400x400?text=iPhone';
          
          if (strpos($img, 'http') !== 0) {
              $img = ltrim($img, '/');
          }
          $desc = $p['descripcion'] ?? '';
          if (function_exists('mb_strimwidth')) {
              $descShort = mb_strimwidth($desc, 0, 90, '…', 'UTF-8');
          } else {
              $descShort = strlen($desc) > 90 ? substr($desc, 0, 90) . '…' : $desc;
          }
        ?>
        <a href="producto.php?id=<?= $p['id'] ?>" style="text-decoration:none; color:inherit;">
          <img src="<?= h($img) ?>" alt="<?= h($p['nombre_producto']) ?>" style="width:100%; height:280px; object-fit:contain; background:#0f1320; padding:16px;">
        </a>
        <div class="body">
          <h3><?= h($p['nombre_producto']) ?></h3>
          <div class="price"><?= money($p['precio_producto']) ?></div>
          <p style="color:#9ca3af; margin:0;">
            <?= h($descShort) ?>
          </p>
          <div class="actions">
            <?php if (is_logged_in()): ?>
              <form action="add_to_cart.php" method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                <input type="hidden" name="cantidad" value="1">
                <button class="btn" type="submit">Agregar al carrito</button>
              </form>
            <?php else: ?>
              <a class="btn" href="login.php">Inicia sesión para comprar</a>
            <?php endif; ?>
            <a class="btn secondary" href="producto.php?id=<?= $p['id'] ?>">Ver detalle</a>
          </div>
          <small style="color:#9ca3af;">Stock: <?= (int)$p['stock'] ?></small>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>