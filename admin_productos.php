<?php
// admin_productos.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

require_admin();

$pageTitle = 'Gestión de Productos | Admin';
$action = $_GET['action'] ?? 'list';
$producto_id = (int)($_GET['id'] ?? 0);
$errors = [];
$success = '';

// ELIMINAR PRODUCTO
if ($action === 'delete' && $producto_id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate()) {
        // Obtener imagen para eliminarla del servidor
        $stmt = $pdo->prepare("SELECT imagen_url FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch();
        
        // Eliminar imagen si existe en imagenes/
        if ($prod && $prod['imagen_url'] && strpos($prod['imagen_url'], 'imagenes/') === 0) {
            $ruta_imagen = __DIR__ . '/' . $prod['imagen_url'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }
        
        $del = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $del->execute([$producto_id]);
        header('Location: admin_productos.php?success=deleted');
        exit;
    }
}

// AGREGAR O EDITAR PRODUCTO
if (($action === 'add' || $action === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Token CSRF inválido';
    }

    $nombre = trim($_POST['nombre_producto'] ?? '');
    $precio = trim($_POST['precio_producto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $imagen_url = trim($_POST['imagen_url_manual'] ?? ''); // URL manual opcional

    if ($nombre === '' || $precio === '') {
        $errors[] = 'Nombre y precio son obligatorios';
    }
    if (!is_numeric($precio) || $precio < 0) {
        $errors[] = 'Precio inválido';
    }
    if ($stock < 0) {
        $errors[] = 'Stock inválido';
    }

    // PROCESAR SUBIDA DE IMAGEN
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];
        $nombre_archivo = $archivo['name'];
        $tmp_name = $archivo['tmp_name'];
        $size = $archivo['size'];
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        
        // Validar extensión
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $extensiones_permitidas)) {
            $errors[] = 'Solo se permiten imágenes (jpg, jpeg, png, gif, webp)';
        }
        
        // Validar tamaño (máximo 5MB)
        if ($size > 5 * 1024 * 1024) {
            $errors[] = 'La imagen no debe superar 5MB';
        }
        
        if (!$errors) {
            // Generar nombre único
            $nombre_unico = uniqid('producto_', true) . '.' . $extension;
            $ruta_destino = __DIR__ . '/imagenes/' . $nombre_unico;
            
            // Mover archivo
            if (move_uploaded_file($tmp_name, $ruta_destino)) {
                $imagen_url = 'imagenes/' . $nombre_unico;
                
                // Si es edición, eliminar imagen anterior
                if ($action === 'edit' && $producto_id > 0) {
                    $stmt = $pdo->prepare("SELECT imagen_url FROM productos WHERE id = ?");
                    $stmt->execute([$producto_id]);
                    $prod_anterior = $stmt->fetch();
                    if ($prod_anterior && $prod_anterior['imagen_url'] && strpos($prod_anterior['imagen_url'], 'imagenes/') === 0) {
                        $ruta_anterior = __DIR__ . '/' . $prod_anterior['imagen_url'];
                        if (file_exists($ruta_anterior)) {
                            unlink($ruta_anterior);
                        }
                    }
                }
            } else {
                $errors[] = 'Error al subir la imagen';
            }
        }
    }

    if (!$errors) {
        if ($action === 'add') {
            $ins = $pdo->prepare("
                INSERT INTO productos (nombre_producto, precio_producto, descripcion, imagen_url, stock)
                VALUES (?, ?, ?, ?, ?)
            ");
            $ins->execute([$nombre, $precio, $descripcion, $imagen_url, $stock]);
            header('Location: admin_productos.php?success=added');
            exit;
        } elseif ($action === 'edit' && $producto_id > 0) {
            // Si no se subió nueva imagen, mantener la anterior
            if ($imagen_url === '') {
                $stmt = $pdo->prepare("SELECT imagen_url FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $prod = $stmt->fetch();
                $imagen_url = $prod['imagen_url'];
            }
            
            $upd = $pdo->prepare("
                UPDATE productos 
                SET nombre_producto = ?, precio_producto = ?, descripcion = ?, imagen_url = ?, stock = ?
                WHERE id = ?
            ");
            $upd->execute([$nombre, $precio, $descripcion, $imagen_url, $stock, $producto_id]);
            header('Location: admin_productos.php?success=updated');
            exit;
        }
    }
}

// OBTENER DATOS PARA EDITAR
$producto = null;
if ($action === 'edit' && $producto_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch();
    if (!$producto) {
        die('Producto no encontrado');
    }
}

// LISTAR PRODUCTOS
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll();

// Mensajes de éxito
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'added': $success = 'Producto agregado correctamente'; break;
        case 'updated': $success = 'Producto actualizado correctamente'; break;
        case 'deleted': $success = 'Producto eliminado correctamente'; break;
    }
}

require_once __DIR__ . '/header.php';
?>

<h1>Gestión de Productos</h1>

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

<!-- FORMULARIO AGREGAR/EDITAR -->
<?php if ($action === 'add' || $action === 'edit'): ?>
  <div class="card" style="padding:20px; max-width:700px; margin-bottom:30px;">
    <h2><?= $action === 'add' ? 'Agregar Producto' : 'Editar Producto' ?></h2>
    
    <!-- Mostrar imagen actual si existe -->
    <?php if ($action === 'edit' && $producto['imagen_url']): ?>
      <div style="margin-bottom:16px;">
        <p style="margin-bottom:8px;"><strong>Imagen actual:</strong></p>
        <img src="<?= h($producto['imagen_url']) ?>" alt="Imagen actual" style="max-width:200px; border-radius:8px; border:1px solid #2a3042;">
      </div>
    <?php endif; ?>
    
    <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="row">
        <label>Nombre del producto
          <input type="text" name="nombre_producto" value="<?= h($producto['nombre_producto'] ?? '') ?>" required>
        </label>
        <label>Precio
          <input type="number" step="0.01" name="precio_producto" value="<?= h($producto['precio_producto'] ?? '') ?>" required>
        </label>
        <label>Descripción
          <textarea name="descripcion" rows="3" style="width:100%; padding:10px; border-radius:10px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb;"><?= h($producto['descripcion'] ?? '') ?></textarea>
        </label>
        
        <label>Imagen del producto
          <input type="file" name="imagen" accept="image/*" style="width:100%; padding:10px; border-radius:10px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb;">
          <small style="color:#9ca3af; display:block; margin-top:4px;">
            Formatos: JPG, PNG, GIF, WEBP (máx. 5MB)
            <?= $action === 'edit' ? ' - Dejar vacío para mantener la imagen actual' : '' ?>
          </small>
        </label>
        
        <label>O URL de imagen (opcional)
          <input type="text" name="imagen_url_manual" placeholder="https://ejemplo.com/imagen.jpg" style="width:100%; padding:10px; border-radius:10px; border:1px solid #2a3042; background:#0f1320; color:#e5e7eb;">
          <small style="color:#9ca3af; display:block; margin-top:4px;">
            Si subes un archivo, esta URL será ignorada
          </small>
        </label>
        
        <label>Stock
          <input type="number" name="stock" value="<?= h($producto['stock'] ?? 0) ?>" required>
        </label>
        <div style="display:flex; gap:10px;">
          <button class="btn" type="submit"><?= $action === 'add' ? 'Agregar' : 'Actualizar' ?></button>
          <a class="btn secondary" href="admin_productos.php">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
<?php endif; ?>

<!-- LISTA DE PRODUCTOS -->
<?php if ($action === 'list'): ?>
  <div style="margin-bottom:20px;">
    <a class="btn" href="admin_productos.php?action=add">➕ Agregar producto</a>
    <a class="btn secondary" href="admin.php">← Volver al panel</a>
  </div>

  <div class="card" style="padding:20px;">
    <table style="width:100%; border-collapse:collapse; color:#e5e7eb;">
      <thead>
        <tr style="border-bottom:2px solid #2a3042;">
          <th style="text-align:left; padding:12px;">Imagen</th>
          <th style="text-align:left; padding:12px;">Nombre</th>
          <th style="text-align:right; padding:12px;">Precio</th>
          <th style="text-align:center; padding:12px;">Stock</th>
          <th style="text-align:center; padding:12px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr style="border-bottom:1px solid #21273a;">
          <td style="padding:12px;">
            <img src="<?= h($p['imagen_url'] ?: 'https://via.placeholder.com/80x80?text=Sin+imagen') ?>" 
                 alt="<?= h($p['nombre_producto']) ?>" 
                 style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
          </td>
          <td style="padding:12px;"><strong><?= h($p['nombre_producto']) ?></strong></td>
          <td style="text-align:right; padding:12px; color:#00d4ff;"><?= money($p['precio_producto']) ?></td>
          <td style="text-align:center; padding:12px;"><?= $p['stock'] ?></td>
          <td style="text-align:center; padding:12px;">
            <a class="btn" href="admin_productos.php?action=edit&id=<?= $p['id'] ?>" style="padding:6px 10px; font-size:0.85rem;">✏️ Editar</a>
            <a class="btn secondary" href="admin_productos.php?action=delete&id=<?= $p['id'] ?>" 
               onclick="return confirm('¿Eliminar este producto?')" style="padding:6px 10px; font-size:0.85rem;">🗑️ Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<!-- CONFIRMACIÓN DE ELIMINACIÓN -->
<?php if ($action === 'delete' && $producto_id > 0): ?>
  <?php
    $stmt = $pdo->prepare("SELECT nombre_producto FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $prod = $stmt->fetch();
  ?>
  <div class="card" style="padding:20px; max-width:500px;">
    <h2>Confirmar eliminación</h2>
    <p>¿Estás seguro de eliminar el producto <strong><?= h($prod['nombre_producto']) ?></strong>?</p>
    <form action="" method="post" style="margin-top:16px;">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <button class="btn secondary" type="submit">Sí, eliminar</button>
      <a class="btn" href="admin_productos.php">Cancelar</a>
    </form>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>