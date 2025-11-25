<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}

$usuario_id_a_eliminar = $_GET['id'];

if ($usuario_id_a_eliminar == $_SESSION['usuario_id']) {
    header('Location: ../admin/gestion_clientes.php?error=self_delete');
    exit();
}

try {
    $pdo->beginTransaction();

    // Obtener IDs de Cliente y Persona asociados al Usuario
    $stmt_ids = $pdo->prepare("SELECT ClienteID, PersonaID FROM Clientes WHERE ClienteID = (SELECT ClienteID FROM Usuario WHERE UsuarioID = ?)");
    $stmt_ids->execute([$usuario_id_a_eliminar]);
    $ids = $stmt_ids->fetch(PDO::FETCH_ASSOC);

    if ($ids) {
        $cliente_id = $ids['ClienteID'];
        $persona_id = $ids['PersonaID'];

        // 1. Desactivar el Usuario
        $pdo->prepare("UPDATE Usuario SET Estado = '0' WHERE UsuarioID = ?")->execute([$usuario_id_a_eliminar]);
        
        // 2. Desactivar el Cliente
        $pdo->prepare("UPDATE Clientes SET Estado = '0' WHERE ClienteID = ?")->execute([$cliente_id]);
        
        // 3. Desactivar la Persona
        if ($persona_id) {
            $pdo->prepare("UPDATE Persona SET Estado = '0' WHERE PersonaID = ?")->execute([$persona_id]);
        }
    }

    $pdo->commit();
    header('Location: ../admin/gestion_clientes.php?status=delete_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../admin/gestion_clientes.php?error=delete_failed');
}
?>