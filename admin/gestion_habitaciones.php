<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// --- LÓGICA DE FILTROS ---
$where_clauses = ["h.Estado = '1'"]; // Empezamos con el filtro base para no mostrar eliminadas
$params = [];
$filter_query_string = '';

// Filtro por Tipo de Habitación
if (!empty($_GET['tipo_habitacion'])) {
    $where_clauses[] = "h.TipoHabitacionID = ?";
    $params[] = $_GET['tipo_habitacion'];
    $filter_query_string .= '&tipo_habitacion=' . urlencode($_GET['tipo_habitacion']);
}

// Filtro por Estado de Habitación
if (!empty($_GET['estado_habitacion'])) {
    $where_clauses[] = "h.Estado_HabitacionID = ?";
    $params[] = $_GET['estado_habitacion'];
    $filter_query_string .= '&estado_habitacion=' . urlencode($_GET['estado_habitacion']);
}

$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// --- CONSULTA PRINCIPAL CON FILTROS ---
$sql_rooms = "SELECT h.HabitacionID, h.NumeroHabitacion, h.Precio, h.Estado_HabitacionID,
                     th.N_TipoHabitacion, th.Descripcion as Capacidad, eh.Descripcion as EstadoActual
              FROM Habitaciones h
              JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID
              JOIN Estado_Habitacion eh ON h.Estado_HabitacionID = eh.Estado_HabitacionID
              $where_sql
              ORDER BY h.NumeroHabitacion";
$stmt_rooms = $pdo->prepare($sql_rooms);
$stmt_rooms->execute($params);
$rooms = $stmt_rooms->fetchAll(PDO::FETCH_ASSOC);

// Consultas para llenar los menús de los filtros
$tipos_filtro = $pdo->query("SELECT TipoHabitacionID, N_TipoHabitacion FROM TiposHabitacion")->fetchAll();
$estados_filtro = $pdo->query("SELECT Estado_HabitacionID, Descripcion FROM Estado_Habitacion")->fetchAll();
?>

<h2>Gestión de Habitaciones</h2>

<div class="filter-bar" style="margin-bottom:20px; background-color:#fff; padding:15px; border-radius: 8px;">
    <form action="gestion_habitaciones.php" method="GET" style="display:flex; gap: 15px; align-items:center;">
        <select name="tipo_habitacion">
            <option value="">-- Filtrar por Tipo --</option>
            <?php foreach($tipos_filtro as $tipo): ?>
                <option value="<?php echo $tipo['TipoHabitacionID']; ?>" <?php echo (isset($_GET['tipo_habitacion']) && $_GET['tipo_habitacion'] == $tipo['TipoHabitacionID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo['N_TipoHabitacion']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="estado_habitacion">
            <option value="">-- Filtrar por Estado --</option>
             <?php foreach($estados_filtro as $estado): ?>
                <option value="<?php echo $estado['Estado_HabitacionID']; ?>" <?php echo (isset($_GET['estado_habitacion']) && $_GET['estado_habitacion'] == $estado['Estado_HabitacionID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($estado['Descripcion']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="gestion_habitaciones.php" class="btn btn-secondary">Limpiar Filtros</a>
    </form>
</div>

<div style="margin-bottom: 20px;">
    <a href="add_room.php" class="btn btn-success">Agregar Nueva Habitación</a>
</div>

<div class="habitaciones-container">
    <?php foreach($rooms as $room): ?>
        <div class="habitacion-card">
            <h4><?php echo htmlspecialchars($room['N_TipoHabitacion']) . " " . htmlspecialchars($room['NumeroHabitacion']); ?></h4>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($room['N_TipoHabitacion']); ?></p>
            <p><strong>Capacidad:</strong> <?php echo htmlspecialchars($room['Capacidad']); ?></p>
            <p><strong>Precio Base:</strong> S/ <?php echo number_format($room['Precio'], 2); ?></p>
            <p><strong>Estado Actual:</strong> <?php echo htmlspecialchars($room['EstadoActual']); ?></p>
            
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <a href="edit_room.php?id=<?php echo $room['HabitacionID']; ?>" class="btn btn-primary">Editar</a>
                <a href="../actions/delete_room.php?id=<?php echo $room['HabitacionID']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar esta habitación?');">Eliminar</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

