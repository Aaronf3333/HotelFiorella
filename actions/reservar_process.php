<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['cliente_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Recolección de datos (ahora vienen de los campos ocultos de pago.php)
$cliente_id = $_SESSION['cliente_id'];
$usuario_id = $_SESSION['usuario_id'];
$habitacion_id = $_POST['habitacion_id'];
$fecha_entrada = $_POST['fecha_entrada'];
$fecha_salida = $_POST['fecha_salida'];
$metodo_pago_id = $_POST['metodo_pago_id'];
$tipo_documento = $_POST['tipo_documento'];

// (La validación de fechas no cambia)
if (strtotime($fecha_salida) <= strtotime($fecha_entrada)) {
    header('Location: ../reservar.php?error=' . urlencode('La fecha de salida debe ser posterior a la fecha de entrada.'));
    exit();
}

try {
    $pdo->beginTransaction();

    // (La lógica para insertar en Reservas, Venta y Boleta/Factura no cambia)
    $sql_reserva = "INSERT INTO Reservas (ClienteID, UsuarioID, HabitacionID, FechaEntrada, FechaSalida, MetodoPagoID, TipoDocumento, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, '1')";
    $stmt_reserva = $pdo->prepare($sql_reserva);
    $stmt_reserva->execute([$cliente_id, $usuario_id, $habitacion_id, $fecha_entrada, $fecha_salida, $metodo_pago_id, $tipo_documento]);
    $reserva_id = $pdo->lastInsertId();

    $stmt_precio = $pdo->prepare("SELECT PrecioPorNoche FROM Habitaciones WHERE HabitacionID = ?");
    $stmt_precio->execute([$habitacion_id]);
    $precio_noche = $stmt_precio->fetchColumn();
    $dias = (strtotime($fecha_salida) - strtotime($fecha_entrada)) / 86400;
    $total = $precio_noche * $dias;
    $sql_venta = "INSERT INTO Venta (ReservaID, Total, Estado) VALUES (?, ?, '1')";
    $stmt_venta = $pdo->prepare($sql_venta);
    $stmt_venta->execute([$reserva_id, $total]);
    
    if ($tipo_documento == 'B') {
        $sql_comprobante = "INSERT INTO Boleta (ReservaID, Numero, Serie, Estado) VALUES (?, '0000', 'B001', '1')";
    } else {
        $sql_comprobante = "INSERT INTO Factura (ReservaID, Numero, Serie, Estado) VALUES (?, '0000', 'F001', '1')";
    }
    $pdo->prepare($sql_comprobante)->execute([$reserva_id]);

    // Simulación de envío de correo para pagos con Yape/Plin
    if ($metodo_pago_id == '1' || $metodo_pago_id == '2') {
        $nombre_usuario = $_SESSION['nombre_persona'] ?? 'un cliente';
        $mensaje_simulado = "<i>(Simulación: Email de 'pago realizado por ".htmlspecialchars($nombre_usuario)."' enviado a brayan.mh1087@gmail.com)</i>";
        // Guardamos el mensaje en la sesión para mostrarlo en la siguiente página
        $_SESSION['flash_message'] = $mensaje_simulado;
    }

    $pdo->commit();
    header('Location: ../client/index.php?success=reserva_ok');
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../reservar.php?error=' . urlencode('Error al procesar la reserva: ' . $e->getMessage()));
    exit();
}
?>