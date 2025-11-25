<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$razon_social = $_POST['razon_social'];
$ruc = $_POST['ruc'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$password = $_POST['password'];
$rol_id = 2; // Las empresas también son Clientes

try {
    $pdo->beginTransaction();

    $sql_empresa = "INSERT INTO Empresa (RUC, Razon_Social, Direccion, Telefono, Estado) VALUES (?, ?, ?, ?, '1')";
    $stmt_empresa = $pdo->prepare($sql_empresa);
    $stmt_empresa->execute([$ruc, $razon_social, $direccion, $telefono]);
    $empresa_id = $pdo->lastInsertId();

    $sql_cliente = "INSERT INTO Clientes (EmpresaID, Estado) VALUES (?, '1')";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->execute([$empresa_id]);
    $cliente_id = $pdo->lastInsertId();

    $sql_usuario = "INSERT INTO Usuario (ClienteID, RolID, NombreUsuario, Contrasena, Estado) VALUES (?, ?, ?, ?, '1')";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$cliente_id, $rol_id, $correo, $password]);

    $pdo->commit();
    header('Location: ../admin/gestion_empresas.php?status=add_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    $error_message = 'Ocurrió un error. Verifique que el RUC, Razón Social o el correo no estén ya registrados.';
     if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 2627) { // Error de constraint UNIQUE
        if (strpos($e->getMessage(), 'RUC')) {
            $error_message = 'El RUC ingresado ya está registrado.';
        } elseif (strpos($e->getMessage(), 'Razon_Social')) {
            $error_message = 'La Razón Social ingresada ya está registrada.';
        } elseif (strpos($e->getMessage(), 'NombreUsuario')) {
            $error_message = 'El correo electrónico ya está registrado.';
        }
    }
    header('Location: ../admin/add_empresa.php?error=' . urlencode($error_message));
}
?>