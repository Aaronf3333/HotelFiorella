<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

// Seguridad: Solo administradores pueden ejecutar este script
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// --- Recolección de Datos Personales ---
$nombres = $_POST['nombres'];
$ape_paterno = $_POST['ape_paterno'];
$ape_materno = $_POST['ape_materno'];
$tipo_documento_id = $_POST['tipo_documento_id'];
$doc_identidad = $_POST['doc_identidad'];
$fec_nacimiento = $_POST['fec_nacimiento'];
$celular = $_POST['celular'];
$genero = $_POST['genero'];
$direccion = $_POST['direccion'];
$e_civil = $_POST['e_civil'];
$distrito_id = $_POST['distrito_id']; // <-- Recibido del formulario corregido

// --- Recolección de Datos de Empleado y Usuario ---
$correo = $_POST['correo'];
$password = $_POST['password'];
$cargo_id = $_POST['cargo_id'];
$contrato_id = $_POST['contrato_id'];
$salario = $_POST['salario'];
$turno = $_POST['turno'];
$fondo_pension = $_POST['fondo_pension'];
$essalud = $_POST['essalud'];
$n_hijos = $_POST['n_hijos'];
$rol_id = 1; // Rol de Administrador

try {
    $pdo->beginTransaction();

    // 1. Insertar en la tabla Persona
    $sql_persona = "INSERT INTO Persona (DistritoID, TipoDocumentoID, Nombres, Ape_Paterno, Ape_Materno, Fec_Nacimiento, Doc_Identidad, Direccion, Celular, E_Civil, Genero, Correo, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
    $stmt_persona = $pdo->prepare($sql_persona);
    $stmt_persona->execute([$distrito_id, $tipo_documento_id, $nombres, $ape_paterno, $ape_materno, $fec_nacimiento, $doc_identidad, $direccion, $celular, $e_civil, $genero, $correo]);
    $persona_id = $pdo->lastInsertId();

    // 2. Insertar en la tabla Empleados
    $sql_empleado = "INSERT INTO Empleados (PersonaID, ContratoID, CargoID, Salario, Turno, Fondo_Pension, N_Hijos, ESSALUD, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1')";
    $stmt_empleado = $pdo->prepare($sql_empleado);
    $stmt_empleado->execute([$persona_id, $contrato_id, $cargo_id, $salario, $turno, $fondo_pension, $n_hijos, $essalud]);
    $empleado_id = $pdo->lastInsertId();

    // 3. Insertar en la tabla Usuario
    $sql_usuario = "INSERT INTO Usuario (EmpleadoID, RolID, NombreUsuario, Contrasena, Estado) VALUES (?, ?, ?, ?, '1')";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$empleado_id, $rol_id, $correo, $password]);

    // Si todo fue bien, confirmamos los cambios
    $pdo->commit();
    header('Location: ../admin/gestion_administradores.php?status=add_ok');

} catch (PDOException $e) {
    // Si algo falla, revertimos todos los cambios
    $pdo->rollBack();
    $error_message = 'Ocurrió un error. Verifique que el DNI o el correo no estén ya registrados.';
     if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 2627) { // Error de constraint UNIQUE
        if (strpos($e->getMessage(), 'Doc_Identidad')) {
            $error_message = 'El número de documento ya está registrado.';
        } elseif (strpos($e->getMessage(), 'Correo')) {
            $error_message = 'El correo electrónico ya está registrado.';
        }
    }
    header('Location: ../admin/add_administrador.php?error=' . urlencode($error_message));
}
?>