<?php
// admin.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_admin(); // Solo admins pueden acceder

$pageTitle = 'Panel de Administrador | PrimeElectrove';
require_once __DIR__ . '/header.php';

// Estadísticas rápidas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
$total_productos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM facturas");
$total_facturas = $stmt->fetch()['total'];
?>

<h1>Panel de Administrador</h1>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px; margin:20px 0;">
  
  <div class="card" style="padding:20px; text-align:center;">
    <h2 style="color:#00d4ff; font-size:2.5rem; margin:0;"><?= $total_productos ?></h2>
    <p style="margin:10px 0 0 0;">Productos en catálogo</p>
    <a class="btn" href="admin_productos.php" style="margin-top:12px; display:inline-block;">Gestionar productos</a>
  </div>

  <div class="card" style="padding:20px; text-align:center;">
    <h2 style="color:#00d4ff; font-size:2.5rem; margin:0;"><?= $total_usuarios ?></h2>
    <p style="margin:10px 0 0 0;">Usuarios registrados</p>
    <a class="btn" href="admin_usuarios.php" style="margin-top:12px; display:inline-block;">Gestionar usuarios</a>
  </div>

  <div class="card" style="padding:20px; text-align:center;">
    <h2 style="color:#00d4ff; font-size:2.5rem; margin:0;"><?= $total_facturas ?></h2>
    <p style="margin:10px 0 0 0;">Facturas generadas</p>
  </div>

</div>

<div class="card" style="padding:20px; margin-top:20px;">
  <h3>Accesos rápidos</h3>
  <div style="display:flex; gap:12px; margin-top:12px;">
    <a class="btn" href="admin_productos.php?action=add">➕ Agregar producto</a>
    <a class="btn secondary" href="index.php">🏠 Ver tienda</a>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>