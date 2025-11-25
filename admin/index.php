<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// -- Consultas para el Dashboard --

// 1. Habitaciones Disponibles (EstadoID = 1)
$stmt_disp = $pdo->prepare("SELECT COUNT(*) FROM Habitaciones WHERE Estado_HabitacionID = 1");
$stmt_disp->execute();
$hab_disponibles = $stmt_disp->fetchColumn();

// 2. Habitaciones Ocupadas (EstadoID = 2)
$stmt_ocup = $pdo->prepare("SELECT COUNT(*) FROM Habitaciones WHERE Estado_HabitacionID = 2");
$stmt_ocup->execute();
$hab_ocupadas = $stmt_ocup->fetchColumn();

// 3. Próximas Llegadas (Hoy)
$hoy = date('Y-m-d');
$stmt_llegadas = $pdo->prepare("SELECT COUNT(*) FROM Reservas WHERE CONVERT(date, FechaEntrada) = ?");
$stmt_llegadas->execute([$hoy]);
$llegadas_hoy = $stmt_llegadas->fetchColumn(); // [cite: 23]

// 4. Salidas Programadas (Hoy)
$stmt_salidas = $pdo->prepare("SELECT COUNT(*) FROM Reservas WHERE CONVERT(date, FechaSalida) = ?");
$stmt_salidas->execute([$hoy]);
$salidas_hoy = $stmt_salidas->fetchColumn(); // [cite: 24]

// 5. Ingresos del Mes
$mes_actual = date('m');
$ano_actual = date('Y');
$stmt_ingresos = $pdo->prepare("SELECT SUM(Total) FROM Venta v JOIN Reservas r ON v.ReservaID = r.ReservaID WHERE MONTH(r.FechaEntrada) = ? AND YEAR(r.FechaEntrada) = ?");
$stmt_ingresos->execute([$mes_actual, $ano_actual]);
$ingresos_mes = $stmt_ingresos->fetchColumn(); // [cite: 25]
$ingresos_mes = $ingresos_mes ? $ingresos_mes : 0; // Para mostrar 0 si no hay ingresos
?>

<h2>Dashboard</h2>
<div class="dashboard-cards">
    <div class="card">
        <h4>Habitaciones Disponibles</h4>
        <p><?php echo $hab_disponibles; ?></p>
    </div>
    <div class="card">
        <h4>Habitaciones Ocupadas</h4>
        <p><?php echo $hab_ocupadas; ?></p>
    </div>
    <div class="card">
        <h4>Próximas Llegadas (Hoy)</h4>
        <p><?php echo $llegadas_hoy; ?></p>
    </div>
    <div class="card">
        <h4>Salidas Programadas (Hoy)</h4>
        <p><?php echo $salidas_hoy; ?></p>
    </div>
    <div class="card">
        <h4>Ingresos del Mes</h4>
        <p>S/ <?php echo number_format($ingresos_mes, 2); ?></p>
    </div>
</div>

