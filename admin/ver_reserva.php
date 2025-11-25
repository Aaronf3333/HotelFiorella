<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// Verificamos que se haya proporcionado un ID numérico en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de reserva no proporcionado o no válido.");
}
$reserva_id = $_GET['id'];

// Consulta para obtener todos los detalles de la reserva específica
$sql = "SELECT 
            r.ReservaID, r.FechaEntrada, r.FechaSalida, r.Estado AS EstadoReserva,
            CASE 
                WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno)
                ELSE e.Razon_Social
            END AS Huesped,
            CONCAT(th.N_TipoHabitacion, ' (#', h.NumeroHabitacion, ')') AS DescripcionHabitacion,
            (DATEDIFF(day, r.FechaEntrada, r.FechaSalida) * h.PrecioPorNoche) AS TotalCalculado,
            mp.NombreMetodo
        FROM Reservas r
        LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID
        LEFT JOIN Persona p ON c.PersonaID = p.PersonaID
        LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID
        LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        LEFT JOIN MetodosPago mp ON r.MetodoPagoID = mp.MetodoPagoID
        WHERE r.ReservaID = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$reserva_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    die("No se encontró la reserva con el ID especificado.");
}

// Lógica para determinar el estado en texto
$fecha_actual = new DateTime();
$fecha_salida_reserva = new DateTime($reserva['FechaSalida']);
$esta_vencida = ($fecha_salida_reserva < $fecha_actual);

if ($reserva['EstadoReserva'] == '0') {
    $estado_texto = 'Cancelada';
} elseif ($esta_vencida) {
    $estado_texto = 'Finalizada';
} else {
    $estado_texto = 'Confirmada';
}
?>

<h2>Detalle de la Reserva #XYZ<?php echo htmlspecialchars($reserva['ReservaID']); ?></h2>
<div class="form-container" style="max-width: 700px; background-color: #f8f9fa; border: 1px solid #ddd;">
    <p><strong>Huésped:</strong> <?php echo htmlspecialchars($reserva['Huesped']); ?></p>
    <p><strong>Fechas:</strong> <?php echo date('d/m/Y', strtotime($reserva['FechaEntrada'])); ?> - <?php echo date('d/m/Y', strtotime($reserva['FechaSalida'])); ?></p>
    <p><strong>Habitación:</strong> <?php echo htmlspecialchars($reserva['DescripcionHabitacion']); ?></p>
    <p><strong>Total Pagado:</strong> S/ <?php echo number_format($reserva['TotalCalculado'], 2); ?></p>
    <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($reserva['NombreMetodo']); ?></p>
    <p><strong>Estado:</strong> <?php echo $estado_texto; ?></p>
    <hr>
    <a href="gestion_reservas.php" class="btn btn-secondary">Volver a la lista</a>
</div>

<?php 
// Ya no se incluye ningún footer para un diseño más limpio en esta vista específica.
?>