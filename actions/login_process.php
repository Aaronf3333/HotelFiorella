<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

// Consulta súper simple para validar el usuario
$sql = "SELECT UsuarioID, RolID, ClienteID, Contrasena FROM Usuario WHERE NombreUsuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && $password == $usuario['Contrasena']) {
    
    $_SESSION['usuario_id'] = $usuario['UsuarioID'];
    $_SESSION['rol_id'] = $usuario['RolID'];
    
    if ($usuario['RolID'] == 2) {
        $_SESSION['cliente_id'] = $usuario['ClienteID'];
    }

    // --- LÓGICA DE REDIRECCIÓN INTELIGENTE ---
    if (isset($_POST['redirect_to']) && !empty($_POST['redirect_to'])) {
        // Si hay una URL de destino, vamos allí
        header('Location: ../' . htmlspecialchars($_POST['redirect_to']));
    } else {
        // Si no, vamos al panel por defecto según el rol
        if ($usuario['RolID'] == 1) {
            header('Location: ../admin/');
        } else {
            header('Location: ../client/');
        }
    }
    exit();
    // --- FIN DE LA LÓGICA ---

} else {
    header('Location: ../login.php?error=1');
    exit();
}
?>