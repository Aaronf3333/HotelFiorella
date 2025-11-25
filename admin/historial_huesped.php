<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de huésped no válido.");
}
$persona_id = $_GET['id'];

// Obtener nombre del huésped para el título
$stmt_nombre = $pdo->prepare("SELECT Nombres, Ape_Paterno FROM Persona WHERE PersonaID = ?");
$stmt_nombre->execute([$persona_id]);
$huesped_info = $stmt_nombre->fetch();
$nombre_huesped = $huesped_info ? $huesped_info['Nombres'] . ' ' . $huesped_info['Ape_Paterno'] : 'Desconocido';

// Lógica de Paginación para el historial
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar total de reservas del huésped
$total_sql = "SELECT COUNT(*) FROM Reservas r JOIN Clientes c ON r.ClienteID = c.ClienteID WHERE c.PersonaID = ?";
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute([$persona_id]);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para obtener el historial de reservas del huésped
$sql = "SELECT r.ReservaID, r.FechaEntrada, r.FechaSalida, r.Estado AS EstadoReserva,
               th.N_TipoHabitacion, h.NumeroHabitacion
        FROM Reservas r
        JOIN Clientes c ON r.ClienteID = c.ClienteID
        JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        WHERE c.PersonaID = ?
        ORDER BY r.FechaEntrada DESC
        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $persona_id, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->bindValue(3, $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
$fecha_actual = new DateTime();
?>

<h2>Historial de Reservas de: <?php echo htmlspecialchars($nombre_huesped); ?></h2>
<a href="gestion_huespedes.php" class="btn btn-secondary" style="margin-bottom:20px;">Volver a la lista de huéspedes</a>

<table>
    <thead><tr><th>ID Reserva</th><th>Habitación</th><th>Fechas</th><th>Estado</th></tr></thead>
    <tbody>
        <?php if (count($historial) > 0): ?>
            <?php foreach ($historial as $reserva): ?>
                 <?php
                    $fecha_salida_reserva = new DateTime($reserva['FechaSalida']);
                    if ($reserva['EstadoReserva'] == '0') {
                        $estado_texto = '<span style="color:red; font-weight:bold;">Cancelada</span>';
                    } elseif ($fecha_salida_reserva < $fecha_actual) {
                        $estado_texto = '<span style="color:grey;">Finalizada</span>';
                    } else {
                        $estado_texto = '<span style="color:green;">Confirmada</span>';
                    }
                ?>
                <tr>
                    <td>XYZ<?php echo $reserva['ReservaID']; ?></td>
                    <td><?php echo htmlspecialchars($reserva['N_TipoHabitacion'] . ' #' . $reserva['NumeroHabitacion']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reserva['FechaEntrada'])) . " - " . date('d/m/Y', strtotime($reserva['FechaSalida'])); ?></td>
                    <td><?php echo $estado_texto; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">Este huésped no tiene reservas en su historial.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($total_paginas > 1): ?>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="?id=<?php echo $persona_id; ?>&page=<?php echo $i; ?>" class="btn <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    <?php endif; ?>
</div>

