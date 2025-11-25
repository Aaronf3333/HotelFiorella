<?php
// Inicia el buffer de salida para evitar errores de "headers already sent"
ob_start();

// Inicia sesión
session_start();

// Verificamos si no hay una sesión de usuario activa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?notice=login_required&redirect_to=reservar.php');
    exit();
}

// Incluimos conexión a la base de datos
include('includes/db.php');

// Obtenemos el nombre de la persona desde la BD
try {
    $sql_nombre = "SELECT p.Nombres 
                   FROM Persona p 
                   JOIN Clientes c ON p.PersonaID = c.PersonaID 
                   WHERE c.ClienteID = ?";
    $stmt_nombre = $pdo->prepare($sql_nombre);
    $stmt_nombre->execute([$_SESSION['cliente_id']]);
    $resultado = $stmt_nombre->fetch(PDO::FETCH_ASSOC);
    $nombre_persona = $resultado ? $resultado['Nombres'] : 'Cliente';
    $_SESSION['nombre_persona'] = $nombre_persona;
} catch (PDOException $e) {
    $nombre_persona = 'Cliente';
}

// Cargamos datos para los menús desplegables
try {
    $habitaciones_sql = "SELECT h.HabitacionID, h.NumeroHabitacion, th.N_TipoHabitacion, h.PrecioPorNoche
                         FROM Habitaciones h 
                         JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID 
                         WHERE h.Estado_HabitacionID = 1";
    $stmt_hab = $pdo->query($habitaciones_sql);
    $habitaciones_disponibles = $stmt_hab->fetchAll(PDO::FETCH_ASSOC);

    $metodos_pago_sql = "SELECT MetodoPagoID, NombreMetodo FROM MetodosPago WHERE Estado = '1'";
    $stmt_mp = $pdo->query($metodos_pago_sql);
    $metodos_pago = $stmt_mp->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $habitaciones_disponibles = [];
    $metodos_pago = [];
}

// Incluimos la cabecera (debe ser AFTER de session_start y antes de HTML)
include('includes/header_public.php');
?>

<div class="form-container">
    <div class="form-box">
        <h2>Realizar una Reserva</h2>
        <p>Estás reservando como: <strong><?php echo htmlspecialchars($nombre_persona); ?></strong></p>
        
        <form action="pago.php" method="POST">
            <div class="form-group">
                <label for="habitacion_id">Selecciona una Habitación:</label>
                <select name="habitacion_id" id="habitacion_id" required>
                    <option value="">-- Habitaciones Disponibles --</option>
                    <?php foreach($habitaciones_disponibles as $hab): ?>
                        <option value="<?php echo $hab['HabitacionID']; ?>">
                            <?php echo htmlspecialchars($hab['N_TipoHabitacion'] . " (#" . $hab['NumeroHabitacion'] . ") - S/ " . number_format($hab['PrecioPorNoche'], 2)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_entrada">Fecha de Entrada:</label>
                <input type="date" id="fecha_entrada" name="fecha_entrada" required>
            </div>

            <div class="form-group">
                <label for="fecha_salida">Fecha de Salida:</label>
                <input type="date" id="fecha_salida" name="fecha_salida" required>
            </div>

            <div class="form-group">
                <label for="metodo_pago_id">Método de Pago:</label>
                <select name="metodo_pago_id" id="metodo_pago_id" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach($metodos_pago as $mp): ?>
                        <option value="<?php echo $mp['MetodoPagoID']; ?>">
                            <?php echo htmlspecialchars($mp['NombreMetodo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn" style="width:100%; margin-top:20px;">Confirmar Reserva</button>
        </form>
    </div>
</div>

<?php
// Incluimos el footer
include('includes/footer.php');

// Enviamos el buffer de salida
ob_end_flush();
?>
