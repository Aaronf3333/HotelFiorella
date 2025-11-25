<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$empresa_id = $_POST['empresa_id'];
$usuario_id = $_POST['usuario_id'];
$razon_social = $_POST['razon_social'];
$ruc = $_POST['ruc'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$password = $_POST['password'];

try {
    $pdo->beginTransaction();

    // Actualizar tabla Empresa
    $sql_empresa = "UPDATE Empresa SET Razon_Social = ?, RUC = ?, Direccion = ?, Telefono = ? WHERE EmpresaID = ?";
    $pdo->prepare($sql_empresa)->execute([$razon_social, $ruc, $direccion, $telefono, $empresa_id]);

    // Actualizar tabla Usuario
    if (!empty($password)) {
        $sql_usuario = "UPDATE Usuario SET NombreUsuario = ?, Contrasena = ? WHERE UsuarioID = ?";
        $pdo->prepare($sql_usuario)->execute([$correo, $password, $usuario_id]);
    } else {
        $sql_usuario = "UPDATE Usuario SET NombreUsuario = ? WHERE UsuarioID = ?";
        $pdo->prepare($sql_usuario)->execute([$correo, $usuario_id]);
    }

    $pdo->commit();
    header('Location: ../admin/gestion_empresas.php?status=edit_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../admin/edit_empresa.php?id=' . $empresa_id . '&error=' . urlencode($e->getMessage()));
}
?>