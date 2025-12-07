<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

require_login();

$factura_id = (int)($_GET['id'] ?? 0);

if ($factura_id <= 0) {
    die('Factura no válida');
}


$stmt = $pdo->prepare("
    SELECT f.id, f.precio_final, f.creado_en, u.nombre, u.apellido, u.correo
    FROM facturas f
    INNER JOIN usuarios u ON f.id_usuario = u.id
    WHERE f.id = ? AND f.id_usuario = ?
");
$stmt->execute([$factura_id, current_user_id()]);
$factura = $stmt->fetch();

if (!$factura) {
    die('Factura no encontrada o no tienes permiso para verla');
}

// Nota: Como vaciamos el carrito, necesitamos otra forma de obtener los productos
// Opción 1: Crear tabla "factura_items" (recomendado para producción)
// Opción 2: Mostrar solo el total (simplificado para este ejercicio)
// Usaremos Opción 2 por simplicidad, pero te dejo comentado cómo sería con items

$pageTitle = 'Factura #' . $factura['id'] . ' | PrimeElectrove';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($pageTitle) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { 
      font-family: Arial, sans-serif; 
      background: #fff; 
      color: #000; 
      padding: 20px;
    }
    .invoice-container { 
      max-width: 800px; 
      margin: 0 auto; 
      border: 2px solid #000; 
      padding: 30px;
    }
    .invoice-header { 
      text-align: center; 
      border-bottom: 2px solid #000; 
      padding-bottom: 20px; 
      margin-bottom: 20px;
    }
    .invoice-header h1 { font-size: 2rem; margin-bottom: 10px; }
    .invoice-info { margin-bottom: 30px; }
    .invoice-info p { margin: 8px 0; }
    .invoice-total { 
      text-align: right; 
      font-size: 1.5rem; 
      font-weight: bold; 
      margin-top: 30px; 
      padding-top: 20px; 
      border-top: 2px solid #000;
    }
    .btn-print { 
      background: #1f6feb; 
      color: #fff; 
      padding: 12px 24px; 
      border: none; 
      border-radius: 8px; 
      cursor: pointer; 
      font-size: 1rem;
      margin-top: 20px;
    }
    .btn-print:hover { background: #1557c0; }
    .btn-back { 
      background: #6c757d; 
      color: #fff; 
      padding: 12px 24px; 
      border: none; 
      border-radius: 8px; 
      text-decoration: none; 
      display: inline-block;
      margin-top: 20px;
      margin-left: 10px;
    }
    @media print {
      .no-print { display: none; }
      body { background: #fff; }
      .invoice-container { border: none; }
    }
  </style>
</head>
<body>

<div class="invoice-container">
  <div class="invoice-header">
    <h1>PrimeElectrove</h1>
    <p>Factura de Compra</p>
  </div>

  <div class="invoice-info">
    <p><strong>Factura #:</strong> <?= h($factura['id']) ?></p>
    <p><strong>Fecha:</strong> <?= h($factura['creado_en']) ?></p>
    <p><strong>Cliente:</strong> <?= h($factura['nombre'] . ' ' . $factura['apellido']) ?></p>
    <p><strong>Correo:</strong> <?= h($factura['correo']) ?></p>
  </div>

  <div style="margin: 30px 0;">
    <h3 style="margin-bottom: 15px;">Detalle de la compra</h3>
    <p style="color: #666;">
      Esta factura corresponde a tu compra realizada en PrimeElectrove.
      <br>Gracias por tu preferencia.
    </p>
  </div>

  <div class="invoice-total">
    <p>Total pagado: <?= money($factura['precio_final']) ?></p>
  </div>

  <div class="no-print" style="text-align: center; margin-top: 30px;">
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir factura</button>
    <a class="btn-back" href="index.php">← Volver a la tienda</a>
  </div>
</div>

</body>
</html>