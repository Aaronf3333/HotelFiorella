<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Recolectar datos
$usuario_id = $_POST['usuario_id'];
$nombre_usuario = $_POST['nombre_usuario'];
$rol_id = $_POST['rol_id'];
$password = $_POST['password'];

try {
    // Si se proporcionó una nueva contraseña, la incluimos en la actualización
    if (!empty($password)) {
        $sql = "UPDATE Usuario SET NombreUsuario = ?, RolID = ?, Contrasena = ? WHERE UsuarioID = ?";
        $params = [$nombre_usuario, $rol_id, $password, $usuario_id];
    } else {
        // Si no, actualizamos solo el email y el rol
        $sql = "UPDATE Usuario SET NombreUsuario = ?, RolID = ? WHERE UsuarioID = ?";
        $params = [$nombre_usuario, $rol_id, $usuario_id];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    header('Location: ../admin/gestion_usuarios.php?status=edit_ok');
} catch (PDOException $e) {
    header('Location: ../admin/edit_usuario.php?id=' . $usuario_id . '&error=edit_failed');
}
?>