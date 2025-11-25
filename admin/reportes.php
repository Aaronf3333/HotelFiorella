<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// Obtenemos la fecha de hoy para las consultas
$hoy = date('Y-m-d');

// --- CONSULTA PARA LLEGADAS DIARIAS ---
$sql_llegadas = "SELECT 
                    r.ReservaID,
                    CASE WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno) ELSE e.Razon_Social END as Huesped,
                    CONCAT(th.N_TipoHabitacion, ' (#', h.NumeroHabitacion, ')') AS Habitacion,
                    r.FechaEntrada
                 FROM Reservas r
                 LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID
                 LEFT JOIN Persona p ON c.PersonaID = p.PersonaID
                 LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID
                 LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
                 LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
                 WHERE CONVERT(date, r.FechaEntrada) = ?";
$stmt_llegadas = $pdo->prepare($sql_llegadas);
$stmt_llegadas->execute([$hoy]);
$llegadas = $stmt_llegadas->fetchAll(PDO::FETCH_ASSOC);


// --- CONSULTA PARA SALIDAS DIARIAS ---
$sql_salidas = "SELECT 
                    r.ReservaID,
                    CASE WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno) ELSE e.Razon_Social END as Huesped,
                    CONCAT(th.N_TipoHabitacion, ' (#', h.NumeroHabitacion, ')') AS Habitacion,
                    r.FechaSalida
                FROM Reservas r
                LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID
                LEFT JOIN Persona p ON c.PersonaID = p.PersonaID
                LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID
                LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
                LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
                WHERE CONVERT(date, r.FechaSalida) = ?";
$stmt_salidas = $pdo->prepare($sql_salidas);
$stmt_salidas->execute([$hoy]);
$salidas = $stmt_salidas->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Reportes</h2>

<h4 style="margin-top: 30px;">Reporte de Llegadas Diarias</h4>
<table>
    <thead>
        <tr>
            <th>Huésped</th>
            <th>Habitación</th>
            <th>Check-in</th>
            <th>Código Reserva</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($llegadas) > 0): ?>
            <?php foreach ($llegadas as $llegada): ?>
                <tr>
                    <td><?php echo htmlspecialchars($llegada['Huesped']); ?></td>
                    <td><?php echo htmlspecialchars($llegada['Habitacion']); ?></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($llegada['FechaEntrada'])); ?></td>
                    <td>XYZ<?php echo $llegada['ReservaID']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay llegadas programadas para hoy.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h4 style="margin-top: 40px;">Reporte de Salidas Programadas</h4>
<table>
    <thead>
        <tr>
            <th>Huésped</th>
            <th>Habitación</th>
            <th>Check-out</th>
            <th>Código Reserva</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($salidas) > 0): ?>
            <?php foreach ($salidas as $salida): ?>
                <tr>
                    <td><?php echo htmlspecialchars($salida['Huesped']); ?></td>
                    <td><?php echo htmlspecialchars($salida['Habitacion']); ?></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($salida['FechaSalida'])); ?></td>
                    <td>XYZ<?php echo $salida['ReservaID']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No hay salidas programadas para hoy.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


