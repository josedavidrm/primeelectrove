<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_login();
$pageTitle = 'Mi perfil | PrimeElectrove';


$stmt = $pdo->prepare("SELECT id, nombre, apellido, correo, rol, creado_en FROM usuarios WHERE id = ?");
$stmt->execute([current_user_id()]);
$user = $stmt->fetch();

require_once __DIR__ . '/header.php';
?>
<h1>Mi perfil</h1>

<div class="card" style="padding:16px; max-width:640px;">
  <p><strong>Nombre:</strong> <?= h($user['nombre'] . ' ' . $user['apellido']) ?></p>
  <p><strong>Correo:</strong> <?= h($user['correo']) ?></p>
  <p><strong>Rol:</strong> <?= h($user['rol']) ?></p>
  <p><strong>Miembro desde:</strong> <?= h($user['creado_en']) ?></p>
  <div style="margin-top:12px;">
    <a class="btn" href="password.php">Cambiar contraseña</a>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>