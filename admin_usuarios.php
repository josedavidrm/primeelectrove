<?php
// admin_usuarios.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_admin();

$pageTitle = 'Gestión de Usuarios | Admin';
$action = $_GET['action'] ?? 'list';
$usuario_id = (int)($_GET['id'] ?? 0);
$errors = [];
$success = '';


if ($action === 'delete' && $usuario_id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
        if ($usuario_id === current_user_id()) {
            $errors[] = 'No puedes eliminarte a ti mismo';
        } else {
            $del = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $del->execute([$usuario_id]);
            header('Location: admin_usuarios.php?success=deleted');
            exit;
        }
    }
}


if ($action === 'toggle_role' && $usuario_id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
        if ($usuario_id === current_user_id()) {
            $errors[] = 'No puedes cambiar tu propio rol';
        } else {
            $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $user = $stmt->fetch();
            
            $nuevo_rol = $user['rol'] === 'admin' ? 'user' : 'admin';
            $upd = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
            $upd->execute([$nuevo_rol, $usuario_id]);
            
            header('Location: admin_usuarios.php?success=role_changed');
            exit;
        }
    }
}


if (($action === 'add' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Token CSRF inválido';
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'user';

    if ($nombre === '' || $apellido === '' || $correo === '') {
        $errors[] = 'Nombre, apellido y correo son obligatorios';
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Correo inválido';
    }
    if ($action === 'add' && strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres';
    }

    if (!$errors) {
        if ($action === 'add') {
            $check = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $check->execute([$correo]);
            if ($check->fetch()) {
                $errors[] = 'Ese correo ya está registrado';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $pdo->prepare("
                    INSERT INTO usuarios (nombre, apellido, correo, password_hash, rol)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $ins->execute([$nombre, $apellido, $correo, $hash, $rol]);
                header('Location: admin_usuarios.php?success=added');
                exit;
            }
        } elseif ($action === 'edit' && $usuario_id > 0) {
            if ($password !== '') {
                if (strlen($password) < 6) {
                    $errors[] = 'La contraseña debe tener al menos 6 caracteres';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare("
                        UPDATE usuarios 
                        SET nombre = ?, apellido = ?, correo = ?, password_hash = ?, rol = ?
                        WHERE id = ?
                    ");
                    $upd->execute([$nombre, $apellido, $correo, $hash, $rol, $usuario_id]);
                    header('Location: admin_usuarios.php?success=updated');
                    exit;
                }
            } else {
                $upd = $pdo->prepare("
                    UPDATE usuarios 
                    SET nombre = ?, apellido = ?, correo = ?, rol = ?
                    WHERE id = ?
                ");
                $upd->execute([$nombre, $apellido, $correo, $rol, $usuario_id]);
                header('Location: admin_usuarios.php?success=updated');
                exit;
            }
        }
    }
}


$usuario = null;
if ($action === 'edit' && $usuario_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        die('Usuario no encontrado');
    }
}


$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
$usuarios = $stmt->fetchAll();


if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added': $success = 'Usuario agregado correctamente'; break;
        case 'updated': $success = 'Usuario actualizado correctamente'; break;
        case 'deleted': $success = 'Usuario eliminado correctamente'; break;
        case 'role_changed': $success = 'Rol de usuario actualizado'; break;
    }
}

require_once __DIR__ . '/header.php';
?>

<h1>Gestión de Usuarios</h1>

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


<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="card" style="padding:20px; max-width:700px; margin-bottom:30px;">
    <h2><?= $action === 'add' ? 'Agregar Usuario' : 'Editar Usuario' ?></h2>
    <form action="" method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="row">
        <label>Nombre
          <input type="text" name="nombre" value="<?= h($usuario['nombre'] ?? '') ?>" required>
        </label>
        <label>Apellido
          <input type="text" name="apellido" value="<?= h($usuario['apellido'] ?? '') ?>" required>
        </label>
        <label>Correo
          <input type="email" name="correo" value="<?= h($usuario['correo'] ?? '') ?>" required>
        </label>
        <label>Contraseña <?= $action === 'edit' ? '(dejar vacío para no cambiar)' : '' ?>
          <input type="password" name="password" <?= $action === 'add' ? 'required' : '' ?>>
        </label>
        <label>Rol
          <select name="rol" style="width:100%; padding:10px; border-radius:10px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb;">
            <option value="user" <?= ($usuario['rol'] ?? '') === 'user' ? 'selected' : '' ?>>Usuario</option>
            <option value="admin" <?= ($usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
          </select>
        </label>
        <div style="display:flex; gap:10px;">
          <button class="btn" type="submit"><?= $action === 'add' ? 'Agregar' : 'Actualizar' ?></button>
          <a class="btn secondary" href="admin_usuarios.php">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
<?php endif; ?>


<?php if ($action === 'list'): ?>
  <div style="margin-bottom:20px;">
    <a class="btn" href="admin_usuarios.php?action=add">➕ Agregar usuario</a>
    <a class="btn secondary" href="admin.php">← Volver al panel</a>
  </div>

  <div class="card" style="padding:20px;">
    <table style="width:100%; border-collapse:collapse; color:#e5e7eb;">
      <thead>
        <tr style="border-bottom:2px solid #2a3042;">
          <th style="text-align:left; padding:12px;">ID</th>
          <th style="text-align:left; padding:12px;">Nombre</th>
          <th style="text-align:left; padding:12px;">Correo</th>
          <th style="text-align:center; padding:12px;">Rol</th>
          <th style="text-align:center; padding:12px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr style="border-bottom:1px solid #21273a;">
          <td style="padding:12px;"><?= $u['id'] ?></td>
          <td style="padding:12px;"><strong><?= h($u['nombre'] . ' ' . $u['apellido']) ?></strong></td>
          <td style="padding:12px;"><?= h($u['correo']) ?></td>
          <td style="text-align:center; padding:12px;">
            <span style="padding:4px 12px; border-radius:6px; background:<?= $u['rol'] === 'admin' ? '#1f6feb' : '#374151' ?>; color:#fff; font-size:0.85rem;">
              <?= $u['rol'] === 'admin' ? '⚙️ Admin' : '👤 User' ?>
            </span>
          </td>
          <td style="text-align:center; padding:12px;">
            <a class="btn" href="admin_usuarios.php?action=edit&id=<?= $u['id'] ?>" style="padding:6px 10px; font-size:0.85rem;">✏️ Editar</a>
            
            <?php if ($u['id'] !== current_user_id()): ?>
              <form action="admin_usuarios.php?action=toggle_role&id=<?= $u['id'] ?>" method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <button class="btn" type="submit" style="padding:6px 10px; font-size:0.85rem; background:#f59e0b;">
                  <?= $u['rol'] === 'admin' ? '⬇️ Quitar admin' : '⬆️ Hacer admin' ?>
                </button>
              </form>
              
              <a class="btn secondary" href="admin_usuarios.php?action=delete&id=<?= $u['id'] ?>" 
                 onclick="return confirm('¿Eliminar este usuario?')" style="padding:6px 10px; font-size:0.85rem;">🗑️ Eliminar</a>
            <?php else: ?>
              <span style="color:#9ca3af; font-size:0.85rem;">(Tú)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>


<?php if ($action === 'delete' && $usuario_id > 0): ?>
  <?php
    $stmt = $pdo->prepare("SELECT nombre, apellido FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usr = $stmt->fetch();
  ?>
  <div class="card" style="padding:20px; max-width:500px;">
    <h2>Confirmar eliminación</h2>
    <p>¿Estás seguro de eliminar al usuario <strong><?= h($usr['nombre'] . ' ' . $usr['apellido']) ?></strong>?</p>
    <form action="" method="post" style="margin-top:16px;">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <button class="btn secondary" type="submit">Sí, eliminar</button>
      <a class="btn" href="admin_usuarios.php">Cancelar</a>
    </form>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>