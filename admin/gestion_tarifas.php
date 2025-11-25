<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// Consulta para obtener los tipos de habitación y su precio promedio actual
$sql = "SELECT 
            th.TipoHabitacionID, 
            th.N_TipoHabitacion,
            AVG(h.Precio) as PrecioBase,
            AVG(h.PrecioPorNoche) as PrecioNocheBase
        FROM TiposHabitacion th
        LEFT JOIN Habitaciones h ON th.TipoHabitacionID = h.TipoHabitacionID
        WHERE h.Estado = '1'
        GROUP BY th.TipoHabitacionID, th.N_TipoHabitacion";
$stmt = $pdo->query($sql);
$tipos_habitacion = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestión de Tarifas</h2>

<?php if(isset($_GET['status']) && $_GET['status'] == 'ok'): ?>
    <p style="color: green; background-color: #d4edda; padding: 10px; border-radius: 5px;">Tarifas actualizadas correctamente.</p>
<?php endif; ?>

<h4>Tarifas por Tipo de Habitación</h4>
<table>
    <thead>
        <tr>
            <th>Tipo de Habitación</th>
            <th>Precio Base Promedio (S/)</th>
            <th>Precio Noche Promedio (S/)</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tipos_habitacion as $tipo): ?>
        <tr>
            <td><?php echo htmlspecialchars($tipo['N_TipoHabitacion']); ?></td>
            <td>S/ <?php echo number_format($tipo['PrecioBase'], 2); ?></td>
            <td>S/ <?php echo number_format($tipo['PrecioNocheBase'], 2); ?></td>
            <td><a href="edit_tarifa_tipo.php?id=<?php echo $tipo['TipoHabitacionID']; ?>" class="btn btn-primary">Editar</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4 style="margin-top: 40px;">Ajuste de Tarifas por Temporada/Promoción</h4>
<div class="form-container" style="max-width:100%;">
    <form action="../actions/ajustar_tarifas_process.php" method="POST" style="display:flex; gap:20px; align-items:flex-end;">
        <div class="form-group">
            <label>Fecha Inicio:</label>
            <input type="date" name="fecha_inicio">
        </div>
        <div class="form-group">
            <label>Fecha Fin:</label>
            <input type="date" name="fecha_fin">
        </div>
        <div class="form-group">
            <label>Ajuste (%):</label>
            <input type="number" name="ajuste" placeholder="Ej: 10 para +10% o -5" required>
        </div>
        <button type="submit" class="btn btn-success">Aplicar Ajuste</button>
    </form>
</div>

