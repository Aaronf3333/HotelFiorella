<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('../includes/db.php');

// Seguridad: verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Lógica para obtener el nombre del administrador
$nombre_usuario = 'Administrador';
try {
    $sql_nombre = "SELECT p.Nombres FROM Persona p JOIN Empleados e ON p.PersonaID = e.PersonaID JOIN Usuario u ON e.EmpleadoID = u.EmpleadoID WHERE u.UsuarioID = ?";
    $stmt_nombre = $pdo->prepare($sql_nombre);
    $stmt_nombre->execute([$_SESSION['usuario_id']]);
    $resultado = $stmt_nombre->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $nombre_usuario = $resultado['Nombres'];
    }
} catch (PDOException $e) {
    $nombre_usuario = 'Administrador';
}

$currentPage = basename($_SERVER['SCRIPT_NAME']);
// --- NUEVO: Array para identificar páginas de gestión de usuarios ---
$userManagementPages = ['gestion_clientes.php', 'gestion_administradores.php', 'gestion_empresas.php'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        .sidebar-dropdown { position: relative; }
        .sidebar-dropdown .dropdown-toggle { display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .sidebar-dropdown .dropdown-menu { display: none; list-style: none; padding: 5px 0; margin-left: 15px; background-color: #495057; border-radius: 5px; }
        /* --- NUEVO: Clase 'open' para mostrar el menú --- */
        .sidebar-dropdown.open > .dropdown-menu { display: block; }
        .sidebar-dropdown.open > .dropdown-toggle .arrow { transform: rotate(90deg); }
        .sidebar-dropdown .dropdown-menu li a { padding: 8px 15px; font-size: 0.9em; }
        .sidebar-dropdown .arrow { transition: transform 0.3s ease; }
    </style>
</head>
<body>
    <header class="panel-header">
        <div class="logo">Hotel Fiorella</div>
        <div class="user-info">
            <span><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($nombre_usuario); ?></span>
            <a href="../actions/logout.php" class="btn btn-danger" style="margin-left: 15px;">Cerrar Sesión</a>
        </div>
    </header>
    <div class="panel-layout">
        <aside class="sidebar">
            <h3>Panel de Administración</h3>
            <ul>
                <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="gestion_reservas.php" class="<?php echo ($currentPage == 'gestion_reservas.php') ? 'active' : ''; ?>">Gestión de Reservas</a></li>
                <li><a href="gestion_habitaciones.php" class="<?php echo ($currentPage == 'gestion_habitaciones.php') ? 'active' : ''; ?>">Gestión de Habitaciones</a></li>
                <li><a href="gestion_huespedes.php" class="<?php echo ($currentPage == 'gestion_huespedes.php') ? 'active' : ''; ?>">Gestión de Huéspedes</a></li>
                <li><a href="reportes.php" class="<?php echo ($currentPage == 'reportes.php') ? 'active' : ''; ?>">Reportes</a></li>
                
                <li class="sidebar-dropdown">
                    <a class="dropdown-toggle">
                        <span>Gráficos POWER BI</span>
                        <span class="arrow">&#9658;</span> </a>
                    <ul class="dropdown-menu">
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiMTE3NjJiZmQtNmQwYS00YjgzLTljOTktMzdmNDk5ZTA2MWE3IiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 1: Ventas Generales</a></li>
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiZDhiNjk1YjAtNjUyMi00YTA2LTg1YTEtMDA2YWUyNmI3MWY1IiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 2: Consumo de Productos</a></li>
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiZjdkNDdmYTgtYTExYy00OTk3LThlOGYtMjVhYjc5NjA4YmVhIiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 3: Analisis Comercial de Ventas</a></li>
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiZjg0ZmMzNGUtMWU3Mi00MzFmLTk2NTUtMTdhMmUxNzc3MTQwIiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 4: Ventas por Ubicacion</a></li>
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiOWYyZGUyZWItYTIzYi00OTk2LWE0MWItMTkxNjEyZmZhOWY0IiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 5: Analisis de Comprobante y Metodo de Pago</a></li>
                        <li><a href="https://app.powerbi.com/view?r=eyJrIjoiYjM0MjhjMzktNDQ4My00ZmM1LWE0NzAtMmE5NDg5ZWE0YjMxIiwidCI6IjEzODQxZDVmLTk2OGQtNDYyNC1hN2RhLWQ2OGE2MDA2YTg0YSIsImMiOjR9" target="_blank">REPORTE 6: Analisis de Habitaciones</a></li>
                    </ul>
                </li>
                
                <li><a href="gestion_tarifas.php" class="<?php echo ($currentPage == 'gestion_tarifas.php') ? 'active' : ''; ?>">Gestión de Tarifas</a></li>
                
                <li class="sidebar-dropdown <?php echo in_array($currentPage, $userManagementPages) ? 'open' : ''; ?>">
                    <a class="dropdown-toggle">
                        <span>Gestión de Usuarios</span>
                        <span class="arrow">&#9658;</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="gestion_clientes.php" class="<?php echo ($currentPage == 'gestion_clientes.php') ? 'active' : ''; ?>">Clientes</a></li>
                        <li><a href="gestion_administradores.php" class="<?php echo ($currentPage == 'gestion_administradores.php') ? 'active' : ''; ?>">Administradores</a></li>
                        <li><a href="gestion_empresas.php" class="<?php echo ($currentPage == 'gestion_empresas.php') ? 'active' : ''; ?>">Empresas</a></li>
                    </ul>
                </li>
                </ul>
        </aside>
        <main class="content">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdowns = document.querySelectorAll('.sidebar-dropdown .dropdown-toggle');
            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('click', function(event) {
                    event.preventDefault();
                    var parent = this.closest('.sidebar-dropdown');
                    parent.classList.toggle('open');
                });
            });
        });
    </script>