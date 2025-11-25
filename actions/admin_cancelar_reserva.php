<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Seguridad: solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}

$reserva_id = $_GET['id'];

try {
    $pdo->beginTransaction();

    $sql_check = "SELECT HabitacionID FROM Reservas WHERE ReservaID = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$reserva_id]);
    $reserva = $stmt_check->fetch();

    if ($reserva) {
        $sql_cancelar = "UPDATE Reservas SET Estado = '0' WHERE ReservaID = ?";
        $pdo->prepare($sql_cancelar)->execute([$reserva_id]);

        $sql_liberar = "UPDATE Habitaciones SET Estado_HabitacionID = 1 WHERE HabitacionID = ?";
        $pdo->prepare($sql_liberar)->execute([$reserva['HabitacionID']]);
    }
    
    $pdo->commit();
    header('Location: ../admin/gestion_reservas.php?success=cancel_ok');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ../admin/gestion_reservas.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>