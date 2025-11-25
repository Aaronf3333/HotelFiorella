<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$nombres = $_POST['nombres'];
$ape_paterno = $_POST['ape_paterno'];
$ape_materno = $_POST['ape_materno'];
$correo = $_POST['correo'];
$password = $_POST['password']; 
$tipo_documento_id = $_POST['tipo_documento_id'];
$doc_identidad = $_POST['doc_identidad'];
$fec_nacimiento = $_POST['fec_nacimiento'];
$celular = $_POST['celular'];
$genero = $_POST['genero'];
$e_civil = $_POST['e_civil'];
$distrito_id = $_POST['distrito_id'];
$direccion = $_POST['direccion'];
$rol_id = 2; // Rol de Cliente

try {
    $pdo->beginTransaction();

    $sql_persona = "INSERT INTO Persona (DistritoID, TipoDocumentoID, Nombres, Ape_Paterno, Ape_Materno, Fec_Nacimiento, Doc_Identidad, Direccion, Celular, E_Civil, Genero, Correo, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
    $stmt_persona = $pdo->prepare($sql_persona);
    $stmt_persona->execute([$distrito_id, $tipo_documento_id, $nombres, $ape_paterno, $ape_materno, $fec_nacimiento, $doc_identidad, $direccion, $celular, $e_civil, $genero, $correo]);
    $persona_id = $pdo->lastInsertId();

    $sql_cliente = "INSERT INTO Clientes (PersonaID, Estado) VALUES (?, '1')";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->execute([$persona_id]);
    $cliente_id = $pdo->lastInsertId();

    $sql_usuario = "INSERT INTO Usuario (ClienteID, RolID, NombreUsuario, Contrasena, Estado) VALUES (?, ?, ?, ?, '1')";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$cliente_id, $rol_id, $correo, $password]);

    $pdo->commit();
    header('Location: ../admin/gestion_clientes.php?status=add_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    $error_message = 'Ocurrió un error. Verifique que el DNI o el correo no estén ya registrados.';
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 2627) {
        if (strpos($e->getMessage(), 'Doc_Identidad')) {
            $error_message = 'El número de documento ya está registrado.';
        } elseif (strpos($e->getMessage(), 'Correo')) {
            $error_message = 'El correo electrónico ya está registrado.';
        }
    }
    header('Location: ../admin/add_cliente.php?error=' . urlencode($error_message));
}
?>