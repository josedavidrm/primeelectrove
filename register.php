<?php
// register.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/auth.php';

$pageTitle = 'Crear cuenta | PrimeElectrove';
$errors = [];

$nombre   = trim($_POST['nombre']   ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$correo   = trim($_POST['correo']   ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Token inválido. Recarga la página.';
    }

    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === '' || $apellido === '' || $correo === '' || $password === '' || $password2 === '') {
        $errors[] = 'Todos los campos son obligatorios.';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Correo inválido.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if ($password !== $password2) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            $errors[] = 'Ese correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("
                INSERT INTO usuarios (nombre, apellido, correo, password_hash, rol)
                VALUES (?, ?, ?, ?, 'user')
            ");
            $ins->execute([$nombre, $apellido, $correo, $hash]);

            $userId = (int)$pdo->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user_id']   = $userId;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['role']      = 'user';

            header('Location: index.php');
            exit;
        }
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <h1>Crear cuenta</h1>
      <p>Únete a PrimeElectrove y comienza a comprar</p>
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

    <form action="" method="post" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      
      <div class="form-row">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?= h($nombre) ?>" required placeholder="Juan">
        </div>
        <div class="form-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido" value="<?= h($apellido) ?>" required placeholder="Pérez">
        </div>
      </div>

      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= h($correo) ?>" required placeholder="tu@email.com">
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres">
      </div>

      <div class="form-group">
        <label for="password2">Confirmar contraseña</label>
        <input type="password" id="password2" name="password2" required placeholder="Repite tu contraseña">
      </div>

      <button class="btn btn-primary btn-block" type="submit">Crear cuenta</button>
    </form>

    <div class="auth-footer">
      <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>