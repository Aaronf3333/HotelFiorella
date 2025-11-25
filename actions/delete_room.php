<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

// Seguridad
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}

$habitacion_id = $_GET['id'];

try {
    // En lugar de DELETE, hacemos un UPDATE para desactivar la habitación
    $sql = "UPDATE Habitaciones SET Estado = '0' WHERE HabitacionID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$habitacion_id]);
    header('Location: ../admin/gestion_habitaciones.php?status=ok');
} catch (PDOException $e) {
    header('Location: ../admin/gestion_habitaciones.php?error=delete_failed');
}
?>