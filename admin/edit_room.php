<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id'])) { die("ID de habitación no proporcionado."); }
$habitacion_id = $_GET['id'];

// Obtener datos de la habitación a editar
$stmt_room = $pdo->prepare("SELECT * FROM Habitaciones WHERE HabitacionID = ?");
$stmt_room->execute([$habitacion_id]);
$room = $stmt_room->fetch(PDO::FETCH_ASSOC);

if (!$room) { die("Habitación no encontrada."); }

// Obtener datos para los menús desplegables
$tipos = $pdo->query("SELECT TipoHabitacionID, N_TipoHabitacion FROM TiposHabitacion")->fetchAll();
$pisos = $pdo->query("SELECT PisoID, Descripcion FROM Pisos")->fetchAll();
$estados = $pdo->query("SELECT Estado_HabitacionID, Descripcion FROM Estado_Habitacion")->fetchAll(); // <-- NUEVA CONSULTA
?>

<h2>Editar Habitación #<?php echo htmlspecialchars($room['NumeroHabitacion']); ?></h2>
<div class="form-container" style="max-width: 600px; margin: auto;">
    <form action="../actions/edit_room_process.php" method="POST">
        <input type="hidden" name="habitacion_id" value="<?php echo $room['HabitacionID']; ?>">
        
        <div class="form-group">
            <label>Número de Habitación:</label>
            <input type="number" name="numero_habitacion" value="<?php echo htmlspecialchars($room['NumeroHabitacion']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tipo:</label>
            <select name="tipo_habitacion_id" required>
                <?php foreach($tipos as $tipo): ?>
                    <option value="<?php echo $tipo['TipoHabitacionID']; ?>" <?php if($tipo['TipoHabitacionID'] == $room['TipoHabitacionID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($tipo['N_TipoHabitacion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Piso:</label>
            <select name="piso_id" required>
                 <?php foreach($pisos as $piso): ?>
                    <option value="<?php echo $piso['PisoID']; ?>" <?php if($piso['PisoID'] == $room['PisoID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($piso['Descripcion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Estado:</label>
            <select name="estado_habitacion_id" required>
                 <?php foreach($estados as $estado): ?>
                    <option value="<?php echo $estado['Estado_HabitacionID']; ?>" <?php if($estado['Estado_HabitacionID'] == $room['Estado_HabitacionID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($estado['Descripcion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Precio Base (S/):</label>
            <input type="text" name="precio" value="<?php echo htmlspecialchars($room['Precio']); ?>" required>
        </div>
        <div class="form-group">
            <label>Precio Por Noche (S/):</label>
            <input type="text" name="precio_noche" value="<?php echo htmlspecialchars($room['PrecioPorNoche']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
