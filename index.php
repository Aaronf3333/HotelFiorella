<?php
include('includes/header_public.php');
include('includes/db.php');

$sql = "SELECT TOP 3
            h.NumeroHabitacion, h.PrecioPorNoche,
            th.N_TipoHabitacion, th.Descripcion AS Capacidad,
            eh.Descripcion AS Estado
        FROM Habitaciones h
        JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
        JOIN Estado_Habitacion eh ON h.Estado_HabitacionID = eh.Estado_HabitacionID
        LEFT JOIN Reservas r ON h.HabitacionID = r.HabitacionID
        WHERE h.Estado = '1'
        GROUP BY h.NumeroHabitacion, h.PrecioPorNoche, th.N_TipoHabitacion, th.Descripcion, eh.Descripcion
        ORDER BY COUNT(r.HabitacionID) DESC";
$stmt = $pdo->query($sql);
$habitaciones_destacadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!function_exists('getStatusClass')) {
    function getStatusClass($status) {
        switch (strtolower($status)) {
            case 'disponible': return 'available';
            case 'ocupada': return 'occupied';
            case 'limpieza': return 'cleaning';
            case 'mantenimiento': return 'maintenance';
            default: return 'secondary';
        }
    }
}
?>

<section class="hero-section" id="booking-section">
    <div class="hero-content">
        <h1>Bienvenido al Hotel Fiorella</h1>
        <p>Habitaciones con baño privado, agua caliente y TV. Atención las 24 hrs.</p>
    </div>
</section>

<section class="featured-rooms-section container">
    <h2>Nuestras Habitaciones Destacadas</h2>
    <div class="room-grid">
        <?php foreach ($habitaciones_destacadas as $habitacion): ?>
            <div class="room-card">
                <?php
                    $tipo_img_nombre = strtolower(explode(' ', $habitacion['N_TipoHabitacion'])[0]);
                     if ($tipo_img_nombre == 'habitación') {
                        $tipo_img_nombre = strtolower(explode(' ', $habitacion['N_TipoHabitacion'])[1]);
                    }
                ?>
                <img src="img/<?php echo $tipo_img_nombre; ?>.jpg" alt="<?php echo htmlspecialchars($habitacion['N_TipoHabitacion']); ?>">
                
                <h3><?php echo htmlspecialchars($habitacion['N_TipoHabitacion']); ?></h3>
                <p><?php echo htmlspecialchars($habitacion['Capacidad']); ?></p>
                <p>Precio: S/ <?php echo number_format($habitacion['PrecioPorNoche'], 2); ?> / Noche</p>
                <span class="room-status <?php echo getStatusClass($habitacion['Estado']); ?>">
                    <?php echo htmlspecialchars($habitacion['Estado']); ?>
                </span>
                <a href="register.php" class="btn btn-secondary">Ver Detalles y Reservar</a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center">
        <a href="habitaciones.php" class="btn btn-primary">Ver Todas las Habitaciones</a>
    </div>
</section>

<section class="google-maps-section container">
    <h2>Encuéntranos en Google Maps</h2>
    <div id="google-map-placeholder">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15545.98696818165!2d-76.25556244458008!3d-13.843108999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x911062dfda22189b%3A0x3d2745a336de69e!2sParacas%2C%20Ica!5e0!3m2!1ses-419!2spe!4v1720301037759!5m2!1ses-419!2spe" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <p class="map-info">Estamos ubicados en Av Paracas mz a lote 4, Paracas.</p>
</section>
<?php
include('includes/footer.php');
?>