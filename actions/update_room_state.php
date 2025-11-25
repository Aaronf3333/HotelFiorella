<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

// Seguridad
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$habitacion_id = $_POST['habitacion_id'];
$estado_id = $_POST['estado_habitacion_id'];

try {
    $sql = "UPDATE Habitaciones SET Estado_HabitacionID = ? WHERE HabitacionID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estado_id, $habitacion_id]);
    header('Location: ../admin/gestion_habitaciones.php?status=ok');
} catch (PDOException $e) {
    // Manejo de errores
    header('Location: ../admin/gestion_habitaciones.php?error=update_failed');
}
?>