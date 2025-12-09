<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'PrimeElectrove';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($pageTitle) ?></title>
  
  <!-- Favicon -->
  <link rel="icon" type="logo.png.png" href="logo.png.png">
  <link rel="shortcut icon" type="logo.png.png" href="logo.png.png">
  
  <!-- CSS -->
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header class="topbar">
    <div class="container nav">
      <a class="brand" href="index.php">PrimeElectrove</a>

      <form class="search" action="index.php" method="get">
        <input type="text" name="q" placeholder="Buscar iPhone..." value="<?= h($_GET['q'] ?? '') ?>">
        <button type="submit">Buscar</button>
      </form>

      <nav class="menu">
        <a href="index.php">Productos</a>
        <a href="cart.php">Carrito</a>

        <?php if (is_logged_in()): ?>
          <a href="profile.php">Mi perfil</a>
          <?php if (is_admin()): ?>
            <a href="admin.php" style="color:#00d4ff;">⚙️ Admin</a>
          <?php endif; ?>
          <span class="hello">Hola, <?= h(current_user_name()) ?></span>
          <a href="logout.php">Salir</a>
        <?php else: ?>
          <a href="login.php">Ingresar</a>
          <a href="register.php">Crear cuenta</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container">