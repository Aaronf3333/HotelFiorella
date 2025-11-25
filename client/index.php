<?php
include('../includes/header_client.php');
include('../includes/db.php');

$cliente_id = $_SESSION['cliente_id'];

// --- LÓGICA DE PAGINACIÓN ---
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$total_reservas_sql = "SELECT COUNT(*) FROM Reservas WHERE ClienteID = ?";
$stmt_total = $pdo->prepare($total_reservas_sql);
$stmt_total->execute([$cliente_id]);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);
// --- FIN LÓGICA DE PAGINACIÓN ---


// --- CONSULTA SQL COMPLETA ---
$sql = "SELECT 
            r.ReservaID, r.FechaEntrada, r.FechaSalida, r.TipoDocumento,
            h.NumeroHabitacion, th.N_TipoHabitacion,
            r.Estado AS EstadoReserva,
            (DATEDIFF(day, r.FechaEntrada, r.FechaSalida) * h.PrecioPorNoche) AS TotalCalculado
        FROM Reservas r
        LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        WHERE r.ClienteID = ?
        ORDER BY r.FechaEntrada DESC
        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $cliente_id, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->bindValue(3, $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fecha_actual = new DateTime();
?>

<h2>Mis Reservas</h2>
<p>Desde aquí puedes consultar tu historial de reservas y descargar tus comprobantes.</p>

<table>
    <thead>
        <tr>
            <th>ID Reserva</th>
            <th>Habitación</th>
            <th>Fechas</th>
            <th>Total (S/)</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($reservas) > 0): ?>
            <?php foreach ($reservas as $reserva): ?>
                <?php
                    $fecha_salida_reserva = new DateTime($reserva['FechaSalida']);
                    $esta_vencida = ($fecha_salida_reserva < $fecha_actual) || ($reserva['EstadoReserva'] == '0');
                ?>
                <tr>
                    <td>XYZ<?php echo $reserva['ReservaID']; ?></td>
                    <td><?php echo htmlspecialchars($reserva['N_TipoHabitacion']) . " (#" . htmlspecialchars($reserva['NumeroHabitacion']) . ")"; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reserva['FechaEntrada'])) . " - " . date('d/m/Y', strtotime($reserva['FechaSalida'])); ?></td>
                    <td><?php echo number_format($reserva['TotalCalculado'] ?? 0, 2); ?></td>
                    <td>
                        <?php 
                            if ($reserva['EstadoReserva'] == '0') {
                                echo '<span style="color:red; font-weight:bold;">Cancelada</span>';
                            } elseif ($fecha_salida_reserva < $fecha_actual) {
                                echo '<span style="color:grey;">Finalizada</span>';
                            } else {
                                echo '<span style="color:green;">Confirmada</span>';
                            }
                        ?>
                    </td>
                    <td style="display:flex; flex-direction:column; gap:5px; width:150px;">
                        <a href="../admin/generar_recibo.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-success" target="_blank">
                            <?php echo ($reserva['TipoDocumento'] == 'B') ? 'Descargar Boleta' : 'Descargar Factura'; ?>
                        </a>
                        <?php if (!$esta_vencida): ?>
                            <a href="modificar_reserva.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-primary">Modificar</a>
                            <a href="../actions/cancelar_reserva.php?id=<?php echo $reserva['ReservaID']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('¿Estás seguro de que quieres cancelar esta reserva?');">
                               Cancelar
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">No tienes ninguna reserva registrada.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($total_paginas > 1): ?>
        <?php
            $max_paginas_visibles = 10;
            $inicio = max(1, $pagina_actual - floor($max_paginas_visibles / 2));
            $fin = min($total_paginas, $inicio + $max_paginas_visibles - 1);
            $inicio = max(1, $fin - $max_paginas_visibles + 1);
        ?>
        <?php if ($pagina_actual > 1): ?>
            <a href="?page=1" class="btn btn-secondary">&laquo;</a>
            <a href="?page=<?php echo $pagina_actual - 1; ?>" class="btn btn-secondary">&lsaquo;</a>
        <?php endif; ?>
        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="btn <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($pagina_actual < $total_paginas): ?>
            <a href="?page=<?php echo $pagina_actual + 1; ?>" class="btn btn-secondary">&rsaquo;</a>
            <a href="?page=<?php echo $total_paginas; ?>" class="btn btn-secondary">&raquo;</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

