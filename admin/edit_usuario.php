<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de usuario no válido.");
}
$usuario_id = $_GET['id'];

// Obtener datos del usuario a editar
$stmt_user = $pdo->prepare("SELECT NombreUsuario, RolID FROM Usuario WHERE UsuarioID = ?");
$stmt_user->execute([$usuario_id]);
$usuario = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$usuario) { die("Usuario no encontrado."); }

// Obtener todos los roles para el menú desplegable
$roles = $pdo->query("SELECT RolID, NombreRol FROM Rol")->fetchAll();
?>

<h2>Editando Usuario: <?php echo htmlspecialchars($usuario['NombreUsuario']); ?></h2>
<div class="form-container" style="max-width: 600px; margin: auto;">
    <form action="../actions/edit_usuario_process.php" method="POST">
        <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
        
        <div class="form-group">
            <label>Email (Nombre de Usuario):</label>
            <input type="email" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['NombreUsuario']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" name="password" placeholder="Ingrese nueva contraseña">
        </div>

        <div class="form-group">
            <label>Rol:</label>
            <select name="rol_id" required>
                <?php foreach($roles as $rol): ?>
                    <option value="<?php echo $rol['RolID']; ?>" <?php if($rol['RolID'] == $usuario['RolID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($rol['NombreRol']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
