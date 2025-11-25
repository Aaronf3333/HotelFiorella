<?php
include('../includes/header_client.php'); // Usamos el header del cliente
include('../includes/db.php');

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$reserva_id = $_GET['id'];

// Obtenemos los datos actuales de la reserva para pre-llenar el formulario
$sql = "SELECT r.FechaEntrada, r.FechaSalida, h.NumeroHabitacion, th.N_TipoHabitacion 
        FROM Reservas r
        JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID
        JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        WHERE r.ReservaID = ? AND r.ClienteID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$reserva_id, $_SESSION['cliente_id']]);
$reserva = $stmt->fetch();

if (!$reserva) {
    echo "<p>Reserva no encontrada o no tienes permiso para modificarla.</p>";
    include('../includes/footer.php');
    exit();
}
?>
<style>
    .register-container { display: flex; justify-content: center; padding: 40px 0; }
    .register-box { padding: 30px; background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; width: 600px; }
    .register-box h2 { text-align: center; color: var(--primary-color); margin-bottom: 25px; }
</style>

<div class="register-container">
    <div class="register-box">
        <h2>Modificar Reserva</h2>
        <p>Estás modificando la reserva para la habitación <strong>#<?php echo htmlspecialchars($reserva['NumeroHabitacion']); ?> (<?php echo htmlspecialchars($reserva['N_TipoHabitacion']); ?>)</strong>.</p>
        
        <form action="../actions/modificar_process.php" method="POST">
            <input type="hidden" name="reserva_id" value="<?php echo $reserva_id; ?>">
            <div class="form-group">
                <label for="fecha_entrada">Nueva Fecha de Entrada:</label>
                <input type="date" id="fecha_entrada" name="fecha_entrada" value="<?php echo date('Y-m-d', strtotime($reserva['FechaEntrada'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="fecha_salida">Nueva Fecha de Salida:</label>
                <input type="date" id="fecha_salida" name="fecha_salida" value="<?php echo date('Y-m-d', strtotime($reserva['FechaSalida'])); ?>" required>
            </div>
            <p style="font-size: 0.8em; color: #6c757d;">Nota: Por simplicidad, no se está verificando la disponibilidad de las nuevas fechas.</p>
            <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Cambios</button>
        </form>
    </div>
</div>

<?php include('../includes/footer.php'); ?>