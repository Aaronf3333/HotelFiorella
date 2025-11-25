<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$tipo_id = $_POST['tipo_habitacion_id'];
$precio = $_POST['precio'];
$precio_noche = $_POST['precio_noche'];

// Validación simple
if (!is_numeric($precio) || !is_numeric($precio_noche)) {
    header('Location: ../admin/gestion_tarifas.php?error=invalid_price');
    exit();
}

try {
    // Actualiza todas las habitaciones que pertenecen a este tipo
    $sql = "UPDATE Habitaciones SET Precio = ?, PrecioPorNoche = ? WHERE TipoHabitacionID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$precio, $precio_noche, $tipo_id]);
    
    header('Location: ../admin/gestion_tarifas.php?status=ok');
} catch (PDOException $e) {
    header('Location: ../admin/gestion_tarifas.php?error=update_failed');
}
?>