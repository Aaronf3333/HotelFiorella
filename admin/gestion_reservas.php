<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// --- LÓGICA DE FILTRADO Y PAGINACIÓN ---
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// --- LÓGICA DE FILTROS ACTUALIZADA ---
$where_clauses = [];
$params = [];
$filter_query_string = '';

// Filtro por Huésped
if (!empty($_GET['huesped'])) {
    $huesped_filtro = '%' . $_GET['huesped'] . '%';
    $where_clauses[] = "(p.Nombres LIKE ? OR p.Ape_Paterno LIKE ? OR e.Razon_Social LIKE ?)";
    array_push($params, $huesped_filtro, $huesped_filtro, $huesped_filtro);
    $filter_query_string .= '&huesped=' . urlencode($_GET['huesped']);
}

// Filtro por Estado (con la nueva lógica)
if (isset($_GET['estado']) && $_GET['estado'] !== '') {
    $estado_filtro = $_GET['estado'];
    $filter_query_string .= '&estado=' . urlencode($estado_filtro);

    if ($estado_filtro === 'confirmada') {
        // Muestra solo las confirmadas que NO han finalizado.
        $where_clauses[] = "(r.Estado = '1' AND r.FechaSalida >= GETDATE())";
    } elseif ($estado_filtro === 'cancelada') {
        $where_clauses[] = "r.Estado = '0'";
    } elseif ($estado_filtro === 'finalizada') {
        // Muestra las que no fueron canceladas pero su fecha de salida ya pasó.
        $where_clauses[] = "(r.Estado = '1' AND r.FechaSalida < GETDATE())";
    }
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// El resto de la lógica de paginación y la consulta SQL principal no necesitan cambios
$total_sql = "SELECT COUNT(*) FROM Reservas r LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID LEFT JOIN Persona p ON c.PersonaID = p.PersonaID LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID $where_sql";
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql = "SELECT r.ReservaID, r.Estado AS EstadoReserva, CASE WHEN c.PersonaID IS NOT NULL THEN CONCAT(p.Nombres, ' ', p.Ape_Paterno) ELSE e.Razon_Social END as Huesped, r.FechaEntrada, r.FechaSalida, h.NumeroHabitacion, th.N_TipoHabitacion, (DATEDIFF(day, r.FechaEntrada, r.FechaSalida) * h.PrecioPorNoche) AS TotalCalculado FROM Reservas r LEFT JOIN Clientes c ON r.ClienteID = c.ClienteID LEFT JOIN Persona p ON c.PersonaID = p.PersonaID LEFT JOIN Empresa e ON c.EmpresaID = e.EmpresaID LEFT JOIN Habitaciones h ON r.HabitacionID = h.HabitacionID LEFT JOIN TiposHabitacion th ON h.TipoHabitacionID = th.TipoHabitacionID $where_sql ORDER BY r.FechaEntrada DESC OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
$params_paginacion = $params;
$params_paginacion[] = $offset;
$params_paginacion[] = $registros_por_pagina;
$stmt = $pdo->prepare($sql);
$param_index = 1;
foreach ($params_paginacion as &$param_value) {
    $param_type = is_int($param_value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($param_index, $param_value, $param_type);
    $param_index++;
}
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$fecha_actual = new DateTime();
?>

<h2>Gestión de Reservas</h2>

<div class="filter-bar" style="margin-bottom:20px; background-color:#fff; padding:15px; border-radius: 8px;">
    <form action="gestion_reservas.php" method="GET" style="display:flex; gap: 15px; align-items:center;">
        <input type="text" name="huesped" placeholder="Buscar por huésped..." value="<?php echo isset($_GET['huesped']) ? htmlspecialchars($_GET['huesped']) : ''; ?>">
        <select name="estado">
            <option value="">Todos los Estados</option>
            <option value="confirmada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'confirmada') ? 'selected' : ''; ?>>Confirmada</option>
            <option value="cancelada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
            <option value="finalizada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'finalizada') ? 'selected' : ''; ?>>Finalizada</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="gestion_reservas.php" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<table>
    <thead><tr><th>Código</th><th>Huésped</th><th>Fechas</th><th>Habitación</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
        <?php if (count($reservas) > 0): ?>
            <?php foreach ($reservas as $reserva): ?>
                 <?php $fecha_salida_reserva = new DateTime($reserva['FechaSalida']); $esta_vencida = ($fecha_salida_reserva < $fecha_actual); ?>
                <tr>
                    <td>XYZ<?php echo $reserva['ReservaID']; ?></td>
                    <td><?php echo htmlspecialchars($reserva['Huesped']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($reserva['FechaEntrada'])) . " - " . date('d/m/Y', strtotime($reserva['FechaSalida'])); ?></td>
                    <td><?php echo htmlspecialchars($reserva['N_TipoHabitacion']) . " (" . htmlspecialchars($reserva['NumeroHabitacion']) . ")"; ?></td>
                    <td>S/ <?php echo number_format($reserva['TotalCalculado'] ?? 0, 2); ?></td>
                    <td><?php if ($reserva['EstadoReserva'] == '0') { echo '<span style="color:red; font-weight:bold;">Cancelada</span>'; } elseif ($esta_vencida) { echo '<span style="color:grey;">Finalizada</span>'; } else { echo '<span style="color:green;">Confirmada</span>'; } ?></td>
                    <td style="display:flex; flex-direction:column; gap:5px; width:100px;">
                        <a href="ver_reserva.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-primary">Ver</a>
                        <a href="generar_boleta_html.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-success" target="_blank">Boleta</a>
                        <?php if (!$esta_vencida && $reserva['EstadoReserva'] != '0'): ?>
                            <a href="admin_modificar_reserva.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-secondary">Modificar</a>
                            <a href="../actions/admin_cancelar_reserva.php?id=<?php echo $reserva['ReservaID']; ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de cancelar esta reserva?');">Cancelar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
             <tr><td colspan="7" style="text-align:center;">No se encontraron reservas con los filtros aplicados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($total_paginas > 1): ?>
        <?php
            $max_paginas_visibles = 10;
            $inicio = max(1, $pagina_actual - floor($max_paginas_visibles / 2));
            $fin = min($total_paginas, $inicio + $max_paginas_visibles - 1);
            $inicio = max(1, $fin - $max_paginas_visibles + 1);
        ?>
        <?php if ($pagina_actual > 1): ?>
            <a href="?page=1<?php echo $filter_query_string; ?>" class="btn btn-secondary">&laquo;</a>
            <a href="?page=<?php echo $pagina_actual - 1 . $filter_query_string; ?>" class="btn btn-secondary">&lsaquo;</a>
        <?php endif; ?>
        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
            <a href="?page=<?php echo $i . $filter_query_string; ?>" class="btn <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($pagina_actual < $total_paginas): ?>
            <a href="?page=<?php echo $pagina_actual + 1 . $filter_query_string; ?>" class="btn btn-secondary">&rsaquo;</a>
            <a href="?page=<?php echo $total_paginas . $filter_query_string; ?>" class="btn btn-secondary">&raquo;</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

