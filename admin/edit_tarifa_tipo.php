<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de tipo de habitación no válido.");
}
$tipo_id = $_GET['id'];

// Obtener nombre del tipo de habitación para el título
$stmt_tipo = $pdo->prepare("SELECT N_TipoHabitacion FROM TiposHabitacion WHERE TipoHabitacionID = ?");
$stmt_tipo->execute([$tipo_id]);
$tipo_info = $stmt_tipo->fetch();

if (!$tipo_info) { die("Tipo de habitación no encontrado."); }
?>

<h2>Editar Tarifa para: <?php echo htmlspecialchars($tipo_info['N_TipoHabitacion']); ?></h2>
<p>Los precios que establezcas aquí se aplicarán a <strong>todas</strong> las habitaciones de este tipo.</p>

<div class="form-container" style="max-width: 600px; margin: auto;">
    <form action="../actions/edit_tarifa_tipo_process.php" method="POST">
        <input type="hidden" name="tipo_habitacion_id" value="<?php echo $tipo_id; ?>">
        
        <div class="form-group">
            <label>Nuevo Precio Base (S/):</label>
            <input type="text" name="precio" placeholder="Ej: 150.00" required>
        </div>
        <div class="form-group">
            <label>Nuevo Precio Por Noche (S/):</label>
            <input type="text" name="precio_noche" placeholder="Ej: 180.00" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width:100%;">Actualizar Precios</button>
    </form>
</div>

