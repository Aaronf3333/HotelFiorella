<?php
// Incluir la conexión a la base de datos
include('../includes/db.php');

header('Content-Type: application/json');

// Obtener provincias por departamento
if (isset($_GET['departamento_id']) && is_numeric($_GET['departamento_id'])) {
    $departamento_id = $_GET['departamento_id'];
    $sql = "SELECT ProvinciaID, N_Provincia FROM Provincia WHERE DepartamentoID = ? AND Estado = '1' ORDER BY N_Provincia";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$departamento_id]);
    $provincias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($provincias);
    exit();
}

// Obtener distritos por provincia
if (isset($_GET['provincia_id']) && is_numeric($_GET['provincia_id'])) {
    $provincia_id = $_GET['provincia_id'];
    $sql = "SELECT DistritoID, N_Distrito FROM Distrito WHERE ProvinciaID = ? AND Estado = '1' ORDER BY N_Distrito";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$provincia_id]);
    $distritos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($distritos);
    exit();
}

// Si no se proporciona un parámetro válido, devolver un array vacío
echo json_encode([]);
?>