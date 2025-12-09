<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

$producto_id = (int)($_GET['id'] ?? 0);

if ($producto_id <= 0) {
    header('Location: index.php');
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$producto_id]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: index.php');
    exit;
}

$pageTitle = h($producto['nombre_producto']) . ' | PrimeElectrove';
require_once __DIR__ . '/header.php';

$img = $producto['imagen_url'] ?: 'https://via.placeholder.com/600x600?text=iPhone';
if (strpos($img, 'http') !== 0) {
    $img = ltrim($img, '/');
}
?>

<div style="max-width:1000px; margin:0 auto;">
  <a href="index.php" style="color:#00d4ff; text-decoration:none; display:inline-block; margin-bottom:20px;">
    ← Volver a productos
  </a>

  <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:start;">
    
    
    <div class="card" style="padding:30px; text-align:center;">
      <img src="<?= h($img) ?>" alt="<?= h($producto['nombre_producto']) ?>" 
           style="max-width:100%; height:auto; max-height:500px; object-fit:contain; border-radius:12px;">
    </div>

    
    <div>
      <h1 style="margin-top:0; font-size:2rem;"><?= h($producto['nombre_producto']) ?></h1>
      
      <div class="price" style="font-size:2rem; margin:20px 0;"><?= money($producto['precio_producto']) ?></div>
      
      <div class="card" style="padding:20px; margin:20px 0;">
        <h3 style="margin-top:0;">Descripción</h3>
        <p style="color:#cbd5e1; line-height:1.6;">
          <?= nl2br(h($producto['descripcion'] ?: 'Sin descripción disponible')) ?>
        </p>
      </div>

      <div class="card" style="padding:20px; margin:20px 0;">
        <p style="margin:0;">
          <strong>Stock disponible:</strong> 
          <span style="color:<?= $producto['stock'] > 0 ? '#00d4ff' : '#f87171' ?>;">
            <?= $producto['stock'] > 0 ? $producto['stock'] . ' unidades' : 'Agotado' ?>
          </span>
        </p>
      </div>

      
      <div style="margin-top:30px;">
        <?php if ($producto['stock'] > 0): ?>
          <?php if (is_logged_in()): ?>
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
              
              <div style="display:flex; gap:12px; align-items:center; margin-bottom:16px;">
                <label style="color:#cbd5e1;">Cantidad:</label>
                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" 
                       style="width:80px; padding:10px; border-radius:8px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb; text-align:center;">
              </div>
              
              <button class="btn" type="submit" style="width:100%; padding:14px; font-size:1.1rem;">
                🛒 Agregar al carrito
              </button>
            </form>
          <?php else: ?>
            <a class="btn" href="login.php" style="width:100%; padding:14px; font-size:1.1rem; text-align:center; display:block; text-decoration:none;">
              Inicia sesión para comprar
            </a>
          <?php endif; ?>
        <?php else: ?>
          <button class="btn" disabled style="width:100%; padding:14px; font-size:1.1rem;">
            Producto agotado
          </button>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>