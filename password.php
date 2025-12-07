<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_login();
$pageTitle = 'Cambiar contraseña | PrimeElectrove';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Token inválido. Recarga la página.';
    }

    $pass_actual = $_POST['pass_actual'] ?? '';
    $pass_nueva  = $_POST['pass_nueva'] ?? '';
    $pass_conf   = $_POST['pass_conf'] ?? '';

    if ($pass_actual === '' || $pass_nueva === '' || $pass_conf === '') {
        $errors[] = 'Todos los campos son obligatorios.';
    }
    if (strlen($pass_nueva) < 6) {
        $errors[] = 'La contraseña nueva debe tener al menos 6 caracteres.';
    }
    if ($pass_nueva !== $pass_conf) {
        $errors[] = 'La confirmación no coincide con la nueva contraseña.';
    }

    if (!$errors) {
        
        $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = ?");
        $stmt->execute([current_user_id()]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($pass_actual, $row['password_hash'])) {
            $errors[] = 'La contraseña actual es incorrecta.';
        } else {
            $nuevo_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
            $upd->execute([$nuevo_hash, current_user_id()]);

            session_regenerate_id(true);
            $success = 'Contraseña actualizada correctamente.';
        }
    }
}

require_once __DIR__ . '/header.php';
?>
<h1>Cambiar contraseña</h1>

<?php if ($success): ?>
  <div class="alert" style="border-color:#1f6feb; color:#93c5fd;"><?= h($success) ?></div>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="alert">
    <ul style="margin:0 0 0 18px; padding:0;">
      <?php foreach ($errors as $e): ?>
        <li><?= h($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form action="" method="post" class="card" style="padding:16px; max-width:520px;">
  <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
  <div class="row">
    <label>Contraseña actual
      <input type="password" name="pass_actual" required>
    </label>
    <label>Nueva contraseña
      <input type="password" name="pass_nueva" required>
    </label>
    <label>Confirmar nueva contraseña
      <input type="password" name="pass_conf" required>
    </label>
    <button class="btn" type="submit">Actualizar contraseña</button>
  </div>
</form>

<p style="margin-top:12px;">
  <a class="btn secondary" href="profile.php">Volver a Mi perfil</a>
</p>

<?php require_once __DIR__ . '/footer.php'; ?>
