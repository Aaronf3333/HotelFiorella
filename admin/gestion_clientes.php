<?php
include('../includes/header_admin.php');
include('../includes/db.php');

$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$params = [];
$filter_query_string = '';
// --- MODIFICADO: Añadido u.Estado = '1' para ocultar usuarios eliminados ---
$where_sql = "WHERE u.RolID = 2 AND c.PersonaID IS NOT NULL AND u.Estado = '1'";

if (!empty($_GET['q'])) {
    $q_filtro = '%' . $_GET['q'] . '%';
    $where_sql .= " AND (p.Nombres LIKE ? OR p.Ape_Paterno LIKE ? OR u.NombreUsuario LIKE ?)";
    array_push($params, $q_filtro, $q_filtro, $q_filtro);
    $filter_query_string .= '&q=' . urlencode($_GET['q']);
}

$total_sql = "SELECT COUNT(*) FROM Usuario u JOIN Clientes c ON u.ClienteID = c.ClienteID JOIN Persona p ON c.PersonaID = p.PersonaID $where_sql";
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

// --- MODIFICADO: Añadido p.PersonaID a la selección ---
$sql = "SELECT u.UsuarioID, u.NombreUsuario AS Email, p.PersonaID, p.Nombres, p.Ape_Paterno, p.Ape_Materno
        FROM Usuario u
        JOIN Clientes c ON u.ClienteID = c.ClienteID
        JOIN Persona p ON c.PersonaID = p.PersonaID
        $where_sql
        ORDER BY p.Ape_Paterno, p.Nombres
        OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";

$stmt = $pdo->prepare($sql);
$params_paginacion = array_merge($params, [$offset, $registros_por_pagina]);
$param_index = 1;
foreach ($params_paginacion as &$param_value) {
    $param_type = is_int($param_value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($param_index, $param_value, $param_type);
    $param_index++;
}
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Gestión de Clientes</h2>
    <a href="add_cliente.php" class="btn btn-success">Agregar Nuevo Cliente</a>
</div>

<div class="filter-bar" style="margin-bottom:20px; background-color:#fff; padding:15px; border-radius: 8px; margin-top: 15px;">
    <form action="gestion_clientes.php" method="GET" style="display:flex; gap: 15px; align-items:center;">
        <input type="text" name="q" placeholder="Buscar por nombre, apellido o email..." style="flex-grow:1;" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="gestion_clientes.php" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<table>
    <thead><tr><th>Nombre Completo</th><th>Email (Usuario)</th><th>Acciones</th></tr></thead>
    <tbody>
        <?php if (count($clientes) > 0): ?>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo htmlspecialchars($cliente['Nombres'] . ' ' . $cliente['Ape_Paterno'] . ' ' . $cliente['Ape_Materno']); ?></td>
                <td><?php echo htmlspecialchars($cliente['Email']); ?></td>
                <td>
                    <a href="edit_huesped.php?id=<?php echo $cliente['PersonaID']; ?>" class="btn btn-primary">Editar</a>
                    <a href="../actions/delete_usuario.php?id=<?php echo $cliente['UsuarioID']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No se encontraron clientes.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination" style="margin-top: 20px; text-align: center;">
    <?php if ($total_paginas > 1): ?>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="?page=<?php echo $i . $filter_query_string; ?>" class="btn <?php echo ($i == $pagina_actual) ? 'btn-primary' : 'btn-secondary'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    <?php endif; ?>
</div>