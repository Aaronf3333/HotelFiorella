<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

// Seguridad
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Recolección de datos, incluyendo el nuevo campo de estado
$habitacion_id = $_POST['habitacion_id'];
$numero_habitacion = $_POST['numero_habitacion'];
$tipo_habitacion_id = $_POST['tipo_habitacion_id'];
$piso_id = $_POST['piso_id'];
$estado_habitacion_id = $_POST['estado_habitacion_id']; // <-- NUEVO DATO
$precio = $_POST['precio'];
$precio_noche = $_POST['precio_noche'];

try {
    // Actualizamos la consulta SQL para incluir el Estado_HabitacionID
    $sql = "UPDATE Habitaciones SET 
                NumeroHabitacion = ?, 
                TipoHabitacionID = ?, 
                PisoID = ?, 
                Estado_HabitacionID = ?, -- <-- NUEVO CAMPO EN LA CONSULTA
                Precio = ?, 
                PrecioPorNoche = ? 
            WHERE HabitacionID = ?";
    $stmt = $pdo->prepare($sql);
    // Añadimos la nueva variable al array de ejecución
    $stmt->execute([$numero_habitacion, $tipo_habitacion_id, $piso_id, $estado_habitacion_id, $precio, $precio_noche, $habitacion_id]);
    
    header('Location: ../admin/gestion_habitaciones.php?status=ok');
} catch (PDOException $e) {
    header('Location: ../admin/edit_room.php?id=' . $habitacion_id . '&error=edit_failed');
}
?>