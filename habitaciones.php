<?php
include('includes/header_public.php');
include('includes/db.php');

$sql = "SELECT 
            h.NumeroHabitacion,
            th.N_TipoHabitacion,
            eh.Descripcion AS EstadoHabitacion,
            h.Precio,
            th.Descripcion as Capacidad
        FROM Habitaciones h
        JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        JOIN Estado_Habitacion eh ON h.Estado_HabitacionID = eh.Estado_HabitacionID
        WHERE h.Estado = '1'
        ORDER BY h.NumeroHabitacion";

$stmt = $pdo->query($sql);
$habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2>Nuestras Habitaciones</h2>
    <div class="habitaciones-container">
        <?php foreach ($habitaciones as $habitacion): ?>
            <div class="habitacion-card">
                <?php
                    // Lógica para obtener el nombre de la imagen según el tipo
                    $tipo_img_nombre = strtolower(explode(' ', $habitacion['N_TipoHabitacion'])[0]);
                    // Se quita 'Habitación' y se deja 'simple', 'doble', o 'triple'.
                    if ($tipo_img_nombre == 'habitación') {
                        $tipo_img_nombre = strtolower(explode(' ', $habitacion['N_TipoHabitacion'])[1]);
                    }
                ?>
                <img src="img/<?php echo $tipo_img_nombre; ?>.jpg" alt="<?php echo htmlspecialchars($habitacion['N_TipoHabitacion']); ?>" style="width:100%; height: 200px; object-fit: cover; border-radius: 5px;">
                

                <h4><?php echo htmlspecialchars($habitacion['N_TipoHabitacion']); ?></h4>
                <p><strong>Número:</strong> <?php echo htmlspecialchars($habitacion['NumeroHabitacion']); ?></p>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($habitacion['Capacidad']); ?></p>
                <p><strong>Precio por día:</strong> S/ <?php echo number_format($habitacion['Precio'], 2); ?></p>
                <p><strong>Estado:</strong> 
                    <span style="color: <?php echo ($habitacion['EstadoHabitacion'] == 'Disponible') ? 'green' : 'red'; ?>;">
                        <?php echo htmlspecialchars($habitacion['EstadoHabitacion']); ?>
                    </span>
                </p>
                <a href="reservar.php" class="btn btn-success" style="width: 100%; box-sizing: border-box;">Reservar</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>