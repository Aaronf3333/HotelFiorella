<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Recolectar datos
$persona_id = $_POST['persona_id'];
$nombres = $_POST['nombres'];
$ape_paterno = $_POST['ape_paterno'];
$ape_materno = $_POST['ape_materno'];
$correo = $_POST['correo'];
$doc_identidad = $_POST['doc_identidad'];
$fec_nacimiento = $_POST['fec_nacimiento'];
$celular = $_POST['celular'];
$e_civil = $_POST['e_civil'];
$direccion = $_POST['direccion'];

try {
    // Actualizamos tanto la tabla Persona como el NombreUsuario en la tabla Usuario
    $pdo->beginTransaction();

    $sql_persona = "UPDATE Persona SET 
                Nombres = ?, Ape_Paterno = ?, Ape_Materno = ?, Correo = ?, 
                Doc_Identidad = ?, Fec_Nacimiento = ?, Celular = ?, E_Civil = ?, Direccion = ?
            WHERE PersonaID = ?";
    $stmt_persona = $pdo->prepare($sql_persona);
    $stmt_persona->execute([$nombres, $ape_paterno, $ape_materno, $correo, $doc_identidad, $fec_nacimiento, $celular, $e_civil, $direccion, $persona_id]);
    
    // También actualizamos el correo en la tabla de usuarios si cambia
    $sql_usuario = "UPDATE Usuario SET NombreUsuario = ? WHERE ClienteID = (SELECT ClienteID FROM Clientes WHERE PersonaID = ?)";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$correo, $persona_id]);

    $pdo->commit();

    // Redirigir a la gestión de clientes con un mensaje de éxito
    header('Location: ../admin/gestion_clientes.php?status=edit_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../admin/edit_huesped.php?id=' . $persona_id . '&error=edit_failed');
}
?>