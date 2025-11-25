<?php
// Incluimos la conexión a la BD
include('../includes/db.php');

// Verificamos que se haya pasado un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de reserva no válido.");
}
$reserva_id = $_GET['id'];

// Consulta para obtener todos los datos necesarios para la boleta
$sql = "SELECT 
            r.ReservaID, r.FechaEntrada, r.FechaSalida,
            CASE 
                WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno, ' ', p.Ape_Materno)
                ELSE e.Razon_Social
            END AS NombreCompleto,
            CASE 
                WHEN c.PersonaID IS NOT NULL THEN p.Doc_Identidad
                ELSE e.RUC
            END AS Documento,
            CASE 
                WHEN c.PersonaID IS NOT NULL THEN p.Direccion
                ELSE e.Direccion
            END AS DireccionCliente,
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
    die("No se encontraron datos para la reserva con ID: " . $reserva_id);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Boleta de Venta #<?php echo $data['ReservaID']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 20px; }
        .boleta { border: 1px solid #333; padding: 20px; width: 700px; margin: auto; }
        .header { text-align: center; }
        .header h1, .header p { margin: 2px 0; }
        .details-container { border-top: 1px dashed #333; border-bottom: 1px dashed #333; padding: 10px 0; margin: 15px 0; }
        .details-container p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; }
        th { text-align: left; border-bottom: 1px dashed #333; }
        .total { text-align: right; font-weight: bold; font-size: 1.1em; margin-top: 15px; }
    </style>
</head>
<body onload="window.print()">
    <div class="boleta">
        <div class="header">
            <h1>Hotel Fiorella</h1>
            <p>Av Paracas mz a lote 4, Paracas</p>
            <p>RUC: 20325065266</p>
            <hr>
            <h3>BOLETA DE VENTA ELECTRÓNICA</h3>
            <h4>B001 - N° <?php echo str_pad($data['ReservaID'], 8, '0', STR_PAD_LEFT); ?></h4>
        </div>
        <div class="details-container">
            <p><strong>Fecha de Emisión:</strong> <?php echo date('d/m/Y'); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($data['NombreCompleto']); ?></p>
            <p><strong>DNI/RUC:</strong> <?php echo htmlspecialchars($data['Documento']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($data['DireccionCliente']); ?></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cant.</th>
                    <th>Descripción</th>
                    <th>P. Unit.</th>
                    <th>Importe</th>
                </tr>
            </thead>
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