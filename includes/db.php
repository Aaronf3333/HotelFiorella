<?php
// Cargar la configuración desde el archivo .ini
$config = parse_ini_file(__DIR__ . '/../config.ini');

$serverName = $config['DB_SERVER'];
$database = $config['DB_DATABASE'];
$uid = $config['DB_USER'];
$pwd = $config['DB_PASSWORD'];

// Cadena de conexión para PDO SQLSRV con parámetros obligatorios de Azure
$connectionString = "sqlsrv:server=$serverName;database=$database;Encrypt=true;TrustServerCertificate=false";

try {
    // Crear una instancia de PDO
    $pdo = new PDO($connectionString, $uid, $pwd);

    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("❌ Error al conectar con la base de datos (Azure SQL): " . $e->getMessage());
}
?>
