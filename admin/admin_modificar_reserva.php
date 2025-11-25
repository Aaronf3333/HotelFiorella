<?php
include('../includes/header_admin.php'); // Usamos el header del admin
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: gestion_reservas.php');
    exit();
}

$reserva_id = $_GET['id'];

// Obtenemos los datos actuales de la reserva para pre-llenar el formulario
$sql = "SELECT r.FechaEntrada, r.FechaSalida, h.NumeroHabitacion, th.N_TipoHabitacion,
               CASE WHEN c.PersonaID IS NOT NULL THEN p.Nombres ELSE e.Razon_Social END as Huesped
        FROM Reservas r
        LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID
        LEFT JOIN Persona p ON c.PersonaID = p.PersonaID
        LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID
        WHERE r.ReservaID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$reserva_id]);
$reserva = $stmt->fetch();

if (!$reserva) {
    echo "<p>Reserva no encontrada.</p>";
    include('../includes/footer.php');
    exit();
}
?>
<style>
    .form-container { max-width: 600px; margin: auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; }
</style>

<h2>Modificar Reserva #XYZ<?php echo $reserva_id; ?></h2>
<div class="form-container">
    <p><strong>Huésped:</strong> <?php echo htmlspecialchars($reserva['Huesped']); ?></p>
    <p><strong>Habitación:</strong> <?php echo htmlspecialchars($reserva['N_TipoHabitacion']); ?> (#<?php echo htmlspecialchars($reserva['NumeroHabitacion']); ?>)</p>
    
    <form action="../actions/admin_modificar_process.php" method="POST">
        <input type="hidden" name="reserva_id" value="<?php echo $reserva_id; ?>">
        <div class="form-group">
            <label for="fecha_entrada">Nueva Fecha de Entrada:</label>
            <input type="date" id="fecha_entrada" name="fecha_entrada" value="<?php echo date('Y-m-d', strtotime($reserva['FechaEntrada'])); ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_salida">Nueva Fecha de Salida:</label>
            <input type="date" id="fecha_salida" name="fecha_salida" value="<?php echo date('Y-m-d', strtotime($reserva['FechaSalida'])); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Cambios</button>
        <a href="gestion_reservas.php" class="btn btn-secondary" style="width:100%; margin-top:10px; box-sizing:border-box;">Cancelar</a>
    </form>
</div>

