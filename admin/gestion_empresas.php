<?php
include('../includes/header_admin.php');
include('../includes/db.php');

$registros_por_pagina = 10;
$pagina_actual = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$params = [];
$filter_query_string = '';
// --- MODIFICADO: Añadido u.Estado = '1' para ocultar empresas eliminadas ---
$where_sql = "WHERE u.RolID = 2 AND c.EmpresaID IS NOT NULL AND u.Estado = '1'";

if (!empty($_GET['q'])) {
    $q_filtro = '%' . $_GET['q'] . '%';
    $where_sql .= " AND (e.Razon_Social LIKE ? OR e.RUC LIKE ? OR u.NombreUsuario LIKE ?)";
    array_push($params, $q_filtro, $q_filtro, $q_filtro);
    $filter_query_string .= '&q=' . urlencode($_GET['q']);
}

$total_sql = "SELECT COUNT(*) FROM Usuario u JOIN Clientes c ON u.ClienteID = c.ClienteID JOIN Empresa e ON c.EmpresaID = e.EmpresaID $where_sql";
$stmt_total = $pdo->prepare($total_sql);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql = "SELECT u.UsuarioID, u.NombreUsuario AS Email, e.EmpresaID, e.Razon_Social, e.RUC, e.Telefono
        FROM Usuario u
        JOIN Clientes c ON u.ClienteID = c.ClienteID
        JOIN Empresa e ON c.EmpresaID = e.EmpresaID
        $where_sql
        ORDER BY e.Razon_Social
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
$empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Gestión de Empresas</h2>
    <a href="add_empresa.php" class="btn btn-success">Agregar Nueva Empresa</a>
</div>

<div class="filter-bar" style="margin-bottom:20px; background-color:#fff; padding:15px; border-radius: 8px; margin-top: 15px;">
    <form action="gestion_empresas.php" method="GET" style="display:flex; gap: 15px; align-items:center;">
        <input type="text" name="q" placeholder="Buscar por Razón Social, RUC o email..." style="flex-grow:1;" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="gestion_empresas.php" class="btn btn-secondary">Limpiar</a>
    </form>
</div>

<table>
    <thead><tr><th>Razón Social</th><th>RUC</th><th>Email (Usuario)</th><th>Teléfono</th><th>Acciones</th></tr></thead>
    <tbody>
        <?php if (count($empresas) > 0): ?>
            <?php foreach ($empresas as $empresa): ?>
            <tr>
                <td><?php echo htmlspecialchars($empresa['Razon_Social']); ?></td>
                <td><?php echo htmlspecialchars($empresa['RUC']); ?></td>
                <td><?php echo htmlspecialchars($empresa['Email']); ?></td>
                <td><?php echo htmlspecialchars($empresa['Telefono']); ?></td>
                <td>
                    <a href="edit_empresa.php?id=<?php echo $empresa['EmpresaID']; ?>" class="btn btn-primary">Editar</a>
                    <a href="../actions/delete_empresa.php?id=<?php echo $empresa['EmpresaID']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar esta empresa y su usuario asociado?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No se encontraron empresas.</td></tr>
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