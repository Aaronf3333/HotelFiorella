<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('../includes/db.php');

// 1. Verificación de seguridad
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// 2. Si la seguridad pasa, buscamos el nombre del cliente
$nombre_usuario = 'Cliente'; // Valor por defecto
try {
    $sql_nombre = "SELECT p.Nombres FROM Persona p 
                   JOIN Clientes c ON p.PersonaID = c.PersonaID 
                   WHERE c.ClienteID = ?";
    $stmt_nombre = $pdo->prepare($sql_nombre);
    $stmt_nombre->execute([$_SESSION['cliente_id']]);
    $resultado = $stmt_nombre->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $nombre_usuario = $resultado['Nombres'];
    }
} catch (PDOException $e) {
    $nombre_usuario = 'Cliente';
}

$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - Hotel Fiorella</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="panel-header">
    <div class="logo">Hotel Fiorella</div>
    <div class="user-info">
        
        <a href="../index.php" style="margin-right: 20px; color: #0056b3; font-weight: bold; background-color: transparent" >
            <i class="fas fa-home"></i> Volver al Inicio
        </a>
        <span>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></span>
        <a href="../actions/logout.php" class="btn btn-danger" style="margin-left: 15px;">Cerrar Sesión</a>
    </div>
</header>
    <main class="content">