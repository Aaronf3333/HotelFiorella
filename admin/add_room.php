<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// Obtener tipos y pisos para los menús desplegables
$tipos = $pdo->query("SELECT TipoHabitacionID, N_TipoHabitacion FROM TiposHabitacion WHERE Estado = '1'")->fetchAll();
$pisos = $pdo->query("SELECT PisoID, Descripcion FROM Pisos WHERE Estado = '1'")->fetchAll();
$estados = $pdo->query("SELECT Estado_HabitacionID, Descripcion FROM Estado_Habitacion")->fetchAll();
?>

<h2>Agregar Nueva Habitación</h2>
<div class="form-container" style="max-width: 600px; margin: auto;">
    <form action="../actions/add_room_process.php" method="POST">
        <div class="form-group">
            <label>Número de Habitación:</label>
            <input type="number" name="numero_habitacion" required>
        </div>
        <div class="form-group">
            <label>Tipo:</label>
            <select name="tipo_habitacion_id" required>
                <option value="">Seleccione...</option>
                <?php foreach($tipos as $tipo): ?>
                    <option value="<?php echo $tipo['TipoHabitacionID']; ?>"><?php echo htmlspecialchars($tipo['N_TipoHabitacion']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Piso:</label>
            <select name="piso_id" required>
                <option value="">Seleccione...</option>
                 <?php foreach($pisos as $piso): ?>
                    <option value="<?php echo $piso['PisoID']; ?>"><?php echo htmlspecialchars($piso['Descripcion']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Estado Inicial:</label>
            <select name="estado_habitacion_id" required>
                 <?php foreach($estados as $estado): ?>
                    <option value="<?php echo $estado['Estado_HabitacionID']; ?>"><?php echo htmlspecialchars($estado['Descripcion']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Precio Base (S/):</label>
            <input type="text" name="precio" placeholder="Ej: 150.00" required>
        </div>
        <div class="form-group">
            <label>Precio Por Noche (S/):</label>
            <input type="text" name="precio_noche" placeholder="Ej: 180.00" required>
        </div>
        <button type="submit" class="btn btn-success">Agregar Habitación</button>
    </form>
</div>
