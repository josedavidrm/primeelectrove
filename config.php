<?php
// config.php
ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

$DB_HOST = '127.0.0.1';
$DB_NAME = 'primeelectrove';
$DB_USER = 'root';
$DB_PASS = ''; 

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  $pdo = new PDO(
    "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    $options
  );
} catch (PDOException $e) {
  die('Error de conexión: ' . $e->getMessage());
}