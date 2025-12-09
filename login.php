<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';

$pageTitle = 'Ingresar | PrimeElectrove';
$errors = [];
$correo = trim($_POST['correo'] ?? '');
$redirect = $_GET['redirect'] ?? '';

if ($redirect && $redirect[0] !== '/') {
    $redirect = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Token inválido. Recarga la página.';
    }
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($correo === '' || $password === '') {
        $errors[] = 'Correo y contraseña son obligatorios.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre, password_hash, rol FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Credenciales inválidas.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['role'] = $user['rol'];

            $dest = $redirect ? $redirect : '/index.php';
            header('Location: ' . $dest);
            exit;
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Bienvenido de nuevo</h1>
      <p>Ingresa a tu cuenta de PrimeElectrove</p>
    </div>

    <?php if ($errors): ?>
      <div class="alert">
        <ul style="margin:0 0 0 18px; padding:0;">
          <?php foreach ($errors as $e): ?>
            <li><?= h($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="?<?= $redirect ? 'redirect=' . urlencode($redirect) : '' ?>" method="post" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= h($correo) ?>" required placeholder="tu@email.com">
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">
      </div>

      <button class="btn btn-primary btn-block" type="submit">Iniciar sesión</button>
    </form>

    <div class="auth-footer">
      <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>