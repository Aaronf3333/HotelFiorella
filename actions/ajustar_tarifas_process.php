<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$ajuste = $_POST['ajuste'];

// Validación simple
if (!is_numeric($ajuste)) {
    header('Location: ../admin/gestion_tarifas.php?error=invalid_adjustment');
    exit();
}

// Calcular el multiplicador. Ej: 10% -> 1.10, -5% -> 0.95
$multiplicador = 1 + ($ajuste / 100);

try {
    // Actualiza todos los precios multiplicándolos por el factor de ajuste
    $sql = "UPDATE Habitaciones SET Precio = Precio * ?, PrecioPorNoche = PrecioPorNoche * ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$multiplicador, $multiplicador]);
    
    header('Location: ../admin/gestion_tarifas.php?status=ok');
} catch (PDOException $e) {
    header('Location: ../admin/gestion_tarifas.php?error=adjustment_failed');
}
?>
