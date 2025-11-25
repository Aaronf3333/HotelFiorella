<?php
// Este archivo ahora manejará su propia seguridad sin incluir un header completo.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// 1. Verificación de ID de reserva
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de reserva no válido.");
}
$reserva_id = $_GET['id'];

// 2. Verificación de Sesión de Usuario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit();
}

// 3. Lógica de Autorización: ¿Quién puede ver este recibo?
try {
    // Obtenemos el ClienteID asociado a esta reserva
    $stmt_owner = $pdo->prepare("SELECT ClienteID FROM Reservas WHERE ReservaID = ?");
    $stmt_owner->execute([$reserva_id]);
    $reserva_owner = $stmt_owner->fetch();

    if (!$reserva_owner) {
        die("Reserva no encontrada.");
    }

    $cliente_id_de_la_reserva = $reserva_owner['ClienteID'];
    
    // Verificamos si el usuario es admin O si es el cliente dueño de la reserva
    $es_admin = ($_SESSION['rol_id'] == 1);
    $es_dueño = ($_SESSION['rol_id'] == 2 && $_SESSION['cliente_id'] == $cliente_id_de_la_reserva);

    if (!$es_admin && !$es_dueño) {
        // Si no es ni admin ni el dueño, no tiene permiso.
        die("No tienes permiso para ver este documento.");
    }

} catch (PDOException $e) {
    die("Error al verificar permisos.");
}


// 4. Si la autorización pasa, obtenemos los datos para mostrar el recibo (esta parte no cambia)
$sql = "SELECT ..."; // La consulta larga para obtener todos los datos
// ... (La consulta SQL que ya tenías para obtener los datos de la boleta/factura está bien)
$sql = "SELECT 
            r.ReservaID, r.FechaEntrada, r.FechaSalida, r.TipoDocumento,
            CASE WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno) ELSE e.Razon_Social END AS NombreCliente,
            CASE WHEN c.PersonaID IS NOT NULL THEN p.Doc_Identidad ELSE e.RUC END AS DocumentoCliente,
            CASE WHEN c.PersonaID IS NOT NULL THEN p.Direccion ELSE e.Direccion END AS DireccionCliente,
            DATEDIFF(day, r.FechaEntrada, r.FechaSalida) AS CantidadDias,
            h.PrecioPorNoche,
            CONCAT(th.N_TipoHabitacion, ' (#', h.NumeroHabitacion, ')') AS DescripcionHabitacion,
            (DATEDIFF(day, r.FechaEntrada, r.FechaSalida) * h.PrecioPorNoche) AS TotalFinal
        FROM Reservas r
        LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID
        LEFT JOIN Persona p ON c.PersonaID = p.PersonaID
        LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID
        LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        WHERE r.ReservaID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$reserva_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("No se encontraron datos para la reserva.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Comprobante #<?php echo $data['ReservaID']; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 20px; background-color: #f4f4f4; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .recibo { border: 1px solid #ccc; padding: 20px; max-width: 800px; margin: 20px auto; background: #fff; color: #000; }
        .recibo-header { text-align: center; border-bottom: 1px solid #333; padding-bottom: 15px; }
        .recibo-header h1, .recibo-header p { margin: 5px 0; }
        .recibo-details { margin: 20px 0; }
        .recibo-details p { margin: 8px 0; }
        .recibo table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .recibo th, .recibo td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .recibo th { background-color: #f2f2f2; }
        .recibo .total { text-align: right; font-weight: bold; font-size: 1.2em; margin-top: 20px; }
        @media print {
            body { margin: 0; }
            .recibo { width: 100%; margin: 0; padding: 5px; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-success">Imprimir Comprobante</button>
        <a href="../client/" class="btn btn-secondary">Volver a Mis Reservas</a>
    </div>

    <div class="recibo">
        <div class="recibo-header">
            <h1>Hotel Fiorella</h1>
            <p>Av Paracas mz a lote 4, Paracas</p>
            <p>RUC: 20325065266</p>
            <hr>
            <h2><?php echo $data['TipoDocumento'] == 'B' ? 'BOLETA DE VENTA ELECTRÓNICA' : 'FACTURA ELECTRÓNICA'; ?></h2>
            <h3><?php echo $data['TipoDocumento'] == 'B' ? 'B001' : 'F001'; ?> - N° <?php echo str_pad($data['ReservaID'], 8, '0', STR_PAD_LEFT); ?></h3>
        </div>
        <div class="recibo-details">
            <p><strong>Fecha de Emisión:</strong> <?php echo date('d/m/Y'); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($data['NombreCliente']); ?></p>
            <p><strong><?php echo $data['TipoDocumento'] == 'B' ? 'DNI' : 'RUC'; ?>:</strong> <?php echo htmlspecialchars($data['DocumentoCliente']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($data['DireccionCliente']); ?></p>
        </div>
        <table>
            <thead><tr><th>Cant.</th><th>Descripción</th><th>P. Unit.</th><th>Importe</th></tr></thead>
            <tbody>
                <tr>
                    <td><?php echo $data['CantidadDias']; ?></td>
                    <td>Servicio de Alojamiento - <?php echo htmlspecialchars($data['DescripcionHabitacion']); ?><br>
                        <small>Del <?php echo date('d/m/Y', strtotime($data['FechaEntrada'])); ?> al <?php echo date('d/m/Y', strtotime($data['FechaSalida'])); ?></small>
                    </td>
                    <td>S/ <?php echo number_format($data['PrecioPorNoche'], 2); ?></td>
                    <td>S/ <?php echo number_format($data['TotalFinal'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        <p class="total">TOTAL: S/ <?php echo number_format($data['TotalFinal'], 2); ?></p>
    </div>
</body>
</html>