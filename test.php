<?php
ini_set('display_errors','1');
error_reporting(E_ALL);

echo "<h1>Test de configuración</h1>";

// 1. Verificar sesión
session_start();
echo "<p>✅ Sesión iniciada</p>";

// 2. Verificar conexión DB
$DB_HOST = '127.0.0.1';
$DB_NAME = 'primeelectrove';
$DB_USER = 'root';
$DB_PASS = '';

try {
  $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
  echo "<p>✅ Conexión a base de datos OK</p>";
} catch (PDOException $e) {
  echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
}

// 3. Verificar que los archivos existen
$archivos = ['config.php', 'helpers.php', 'auth.php', 'csrf.php', 'header.php', 'footer.php', 'index.php', 'register.php', 'login.php'];
foreach ($archivos as $f) {
    if (file_exists(__DIR__ . '/' . $f)) {
        echo "<p>✅ $f existe</p>";
    } else {
        echo "<p>❌ $f NO EXISTE</p>";
    }
}

echo "<p><strong>Ruta actual:</strong> " . __DIR__ . "</p>";