<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de empresa no válido.");
}
$empresa_id = $_GET['id'];

// Obtener datos de la empresa y su usuario asociado
$sql = "SELECT e.*, u.NombreUsuario AS Correo, u.UsuarioID
        FROM Empresa e
        JOIN Clientes c ON e.EmpresaID = c.EmpresaID
        JOIN Usuario u ON c.ClienteID = u.ClienteID
        WHERE e.EmpresaID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$empresa_id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) {
    die("Empresa no encontrada.");
}
?>

<h2>Editando Empresa: <?php echo htmlspecialchars($empresa['Razon_Social']); ?></h2>
<div class="form-container" style="max-width: 800px; margin: auto;">
    <form action="../actions/edit_empresa_process.php" method="POST">
        <input type="hidden" name="empresa_id" value="<?php echo $empresa['EmpresaID']; ?>">
        <input type="hidden" name="usuario_id" value="<?php echo $empresa['UsuarioID']; ?>">

        <h4>Datos de la Empresa</h4>
        <div class="form-group">
            <label>Razón Social:</label>
            <input type="text" name="razon_social" value="<?php echo htmlspecialchars($empresa['Razon_Social']); ?>" required>
        </div>
        <div class="form-group">
            <label>RUC:</label>
            <input type="text" name="ruc" value="<?php echo htmlspecialchars($empresa['RUC']); ?>" pattern="[0-9]{11}" required>
        </div>
        <div class="form-group">
            <label>Dirección:</label>
            <input type="text" name="direccion" value="<?php echo htmlspecialchars($empresa['Direccion']); ?>" required>
        </div>
        <div class="form-group">
            <label>Teléfono:</label>
            <input type="tel" name="telefono" value="<?php echo htmlspecialchars($empresa['Telefono']); ?>" pattern="[0-9]{9}" required>
        </div>
        
        <hr style="margin: 20px 0;">
        <h4>Datos de Usuario</h4>
        <div class="form-group">
            <label>Correo Electrónico (usuario):</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($empresa['Correo']); ?>" required>
        </div>
        <div class="form-group">
            <label>Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" name="password" placeholder="Ingrese nueva contraseña">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" style="flex-grow: 1;">Guardar Cambios</button>
            <a href="gestion_empresas.php" class="btn btn-secondary" style="flex-grow: 1;">Volver</a>
        </div>
    </form>
</div>