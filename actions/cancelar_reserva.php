<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Seguridad: solo usuarios logueados pueden cancelar
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}

$reserva_id = $_GET['id'];
$cliente_id_sesion = $_SESSION['cliente_id'];

try {
    // Iniciamos una transacción
    $pdo->beginTransaction();

    // Primero, obtenemos el ID de la habitación y nos aseguramos de que la reserva pertenece al usuario
    $sql_check = "SELECT HabitacionID, ClienteID FROM Reservas WHERE ReservaID = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$reserva_id]);
    $reserva = $stmt_check->fetch();

    if (!$reserva || $reserva['ClienteID'] != $cliente_id_sesion) {
        // Si la reserva no existe o no pertenece al usuario, no hacemos nada.
        throw new Exception("Acción no autorizada.");
    }
    
    // Cambiamos el estado de la reserva a '0' (Cancelado)
    $sql_cancelar = "UPDATE Reservas SET Estado = '0' WHERE ReservaID = ?";
    $stmt_cancelar = $pdo->prepare($sql_cancelar);
    $stmt_cancelar->execute([$reserva_id]);

    // Dejamos la habitación como 'Disponible' (Estado_HabitacionID = 1)
    $sql_liberar = "UPDATE Habitaciones SET Estado_HabitacionID = 1 WHERE HabitacionID = ?";
    $stmt_liberar = $pdo->prepare($sql_liberar);
    $stmt_liberar->execute([$reserva['HabitacionID']]);
    
    // Si todo fue bien, confirmamos los cambios
    $pdo->commit();

    header('Location: ../client/index.php?success=cancel_ok');
    exit();

} catch (Exception $e) {
    // Si algo falla, revertimos todos los cambios
    $pdo->rollBack();
    header('Location: ../client/index.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>