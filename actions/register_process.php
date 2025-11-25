<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirigir si no es una solicitud POST
    header('Location: ../register.php');
    exit();
}

// --- 1. Recolección y Validación de Datos ---
$nombres = $_POST['nombres'];
$ape_paterno = $_POST['ape_paterno'];
$ape_materno = $_POST['ape_materno'];
$correo = $_POST['correo'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$tipo_documento_id = $_POST['tipo_documento_id'];
$doc_identidad = $_POST['doc_identidad'];
$fec_nacimiento = $_POST['fec_nacimiento'];
$celular = $_POST['celular'];
$genero = $_POST['genero'];
$e_civil = $_POST['e_civil'];
$direccion = $_POST['direccion'];
$distrito_id = $_POST['distrito_id']; // <-- ¡NUEVO CAMPO!

// Validación simple
if ($password !== $password_confirm) {
    header('Location: ../register.php?error=Las contraseñas no coinciden.');
    exit();
}
// NOTA: Para un proyecto real, se deben añadir más validaciones (longitud de contraseña, formato de datos, etc.)

try {
    // --- 2. Iniciar Transacción ---
    $pdo->beginTransaction();

    // --- 3. Insertar en la tabla `Persona` (AHORA CON EL DISTRITO CORRECTO) ---
    $sql_persona = "INSERT INTO Persona 
        (DistritoID, TipoDocumentoID, Nombres, Ape_Paterno, Ape_Materno, Fec_Nacimiento, Doc_Identidad, Direccion, Celular, E_Civil, Genero, Correo, Estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
    
    $stmt_persona = $pdo->prepare($sql_persona);
    // Usamos la variable $distrito_id que viene del formulario
    $stmt_persona->execute([$distrito_id, $tipo_documento_id, $nombres, $ape_paterno, $ape_materno, $fec_nacimiento, $doc_identidad, $direccion, $celular, $e_civil, $genero, $correo]);

    // Obtenemos el ID de la persona recién creada
    $persona_id = $pdo->lastInsertId();

    // --- 4. Insertar en la tabla `Clientes` ---
    $sql_cliente = "INSERT INTO Clientes (PersonaID, Estado) VALUES (?, '1')";
    $stmt_cliente = $pdo->prepare($sql_cliente);
    $stmt_cliente->execute([$persona_id]);

    // Obtenemos el ID del cliente recién creado
    $cliente_id = $pdo->lastInsertId();

    // --- 5. Insertar en la tabla `Usuario` ---
    $sql_usuario = "INSERT INTO Usuario (ClienteID, RolID, NombreUsuario, Contrasena, Estado) VALUES (?, 2, ?, ?, '1')";
    $stmt_usuario = $pdo->prepare($sql_usuario);
    $stmt_usuario->execute([$cliente_id, $correo, $password]);

    // --- 6. Confirmar la Transacción ---
    $pdo->commit();

    // Redirigir al login con un mensaje de éxito
    header('Location: ../login.php?success=1');
    exit();

} catch (PDOException $e) {
    // --- 7. Revertir la Transacción en caso de error ---
    $pdo->rollBack();
    
    // Analizar el error para dar un mensaje más útil al usuario
    $error_message = 'Ocurrió un error inesperado. Inténtelo de nuevo.';
    if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 2627) { // Código de error de SQL Server para violación de UNIQUE constraint
        if (strpos($e->getMessage(), 'Doc_Identidad')) {
            $error_message = 'El número de documento ya está registrado.';
        } elseif (strpos($e->getMessage(), 'Correo')) {
            $error_message = 'El correo electrónico ya está registrado.';
        }
    }
    
    // Redirigir a la página de registro con el mensaje de error
    header('Location: ../register.php?error=' . urlencode($error_message));
    exit();
}
?>