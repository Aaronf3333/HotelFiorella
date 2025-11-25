<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include('../includes/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1 || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit();
}

$empresa_id_a_eliminar = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Obtener IDs de Cliente y Usuario asociados a la Empresa
    $stmt_ids = $pdo->prepare("SELECT u.UsuarioID, c.ClienteID FROM Usuario u JOIN Clientes c ON u.ClienteID = c.ClienteID WHERE c.EmpresaID = ?");
    $stmt_ids->execute([$empresa_id_a_eliminar]);
    $ids = $stmt_ids->fetch(PDO::FETCH_ASSOC);

    if ($ids) {
        $usuario_id = $ids['UsuarioID'];
        $cliente_id = $ids['ClienteID'];

        // 1. Desactivar el Usuario
        $pdo->prepare("UPDATE Usuario SET Estado = '0' WHERE UsuarioID = ?")->execute([$usuario_id]);
        
        // 2. Desactivar el Cliente
        $pdo->prepare("UPDATE Clientes SET Estado = '0' WHERE ClienteID = ?")->execute([$cliente_id]);
        
        // 3. Desactivar la Empresa
        $pdo->prepare("UPDATE Empresa SET Estado = '0' WHERE EmpresaID = ?")->execute([$empresa_id_a_eliminar]);
    }

    $pdo->commit();
    header('Location: ../admin/gestion_empresas.php?status=delete_ok');

} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../admin/gestion_empresas.php?error=delete_failed');
}
?>