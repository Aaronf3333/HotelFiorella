<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// --- LÓGICA DE FILTRADO Y PAGINACIÓN ---
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Array para construir las condiciones de la consulta
$where_clauses = ["c.PersonaID IS NOT NULL"]; // Condición base: solo traer clientes que son personas
$params = []; // Array para los parámetros de la consulta preparada
$filter_query_string = ''; // String para mantener los filtros en los enlaces de paginación

// ===== LÓGICA DEL FILTRO DE BÚSQUEDA =====
// 1. Verificamos si se envió un término de búsqueda
if (!empty($_GET['huesped'])) {
    // 2. Preparamos el término para una búsqueda LIKE
    $huesped_filtro = '%' . $_GET['huesped'] . '%';
    // 3. Añadimos la condición a la consulta
    $where_clauses[] = "(p.Nombres LIKE ? OR p.Ape_Paterno LIKE ? OR p.Correo LIKE ?)";
    // 4. Añadimos los parámetros de forma segura
    array_push($params, $huesped_filtro, $huesped_filtro, $huesped_filtro);
    // 5. Mantenemos el filtro en la URL para la paginación
    $filter_query_string .= '&huesped=' . urlencode($_GET['huesped']);
}

// Unimos todas las condiciones para la cláusula WHERE
$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

// Contar total de registros (considerando los filtros)
$total_sql = "SELECT COUNT(*) FROM Persona p JOIN Clientes c ON p.PersonaID = c.PersonaID $where_sql";
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta principal para obtener los huéspedes (considerando filtros y paginación)
$sql_huespedes = "SELECT p.PersonaID, p.Nombres, p.Ape_Paterno, p.Ape_Materno, p.Correo, p.Doc_Identidad 
                  FROM Persona p 
                  JOIN Clientes c ON p.PersonaID = c.PersonaID
                  $where_sql
                  ORDER BY p.Ape_Paterno, p.Nombres
                  OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
                  
$stmt_huespedes = $pdo->prepare($sql_huespedes);
// Combinamos los parámetros del filtro con los de la paginación
$params_paginacion = array_merge($params, [$offset, $registros_por_pagina]);

// Vinculamos todos los parámetros de forma segura
$param_index = 1;
foreach ($params_paginacion as &$param_value) {
    $param_type = is_int($param_value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt_huespedes->bindValue($param_index, $param_value, $param_type);
    $param_index++;
}
$stmt_huespedes->execute();
$huespedes = $stmt_huespedes->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestión de Huéspedes</h2>

<div class="filter-bar" style="margin-bottom:20px; background-color:#fff; padding:15px; border-radius: 8px;">
    <form action="gestion_huespedes.php" method="GET" style="display:flex; gap: 15px; align-items:center;">
        <input type="text" name="huesped" placeholder="Buscar por nombre, apellido o email..." style="flex-grow:1;" value="<?php echo isset($_GET['huesped']) ? htmlspecialchars($_GET['huesped']) : ''; ?>">
        <button type="submit" class="btn btn-primary">Buscar</button>
        <a href="gestion_huespedes.php" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre Completo</th>
            <th>Email</th>
            <th>Documento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($huespedes) > 0): ?>
            <?php foreach ($huespedes as $huesped): ?>
            <tr>
                <td><?php echo htmlspecialchars($huesped['Nombres'] . ' ' . $huesped['Ape_Paterno'] . ' ' . $huesped['Ape_Materno']); ?></td>
                <td><?php echo htmlspecialchars($huesped['Correo']); ?></td>
                <td><?php echo htmlspecialchars($huesped['Doc_Identidad']); ?></td>
                <td>
                    <a href="historial_huesped.php?id=<?php echo $huesped['PersonaID']; ?>" class="btn btn-primary">Ver Historial</a>
                    <a href="edit_huesped.php?id=<?php echo $huesped['PersonaID']; ?>" class="btn btn-secondary">Editar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No se encontraron huéspedes con los criterios de búsqueda.</td></tr>
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

