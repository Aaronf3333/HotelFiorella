<?php
include('../includes/header_admin.php');
include('../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de huésped no válido.");
}
$persona_id = $_GET['id'];

// Obtener datos de la persona a editar
$stmt = $pdo->prepare("SELECT * FROM Persona WHERE PersonaID = ?");
$stmt->execute([$persona_id]);
$huesped = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$huesped) {
    die("Huésped no encontrado.");
}
?>

<h2>Editando a <?php echo htmlspecialchars($huesped['Nombres'] . ' ' . $huesped['Ape_Paterno']); ?></h2>
<div class="form-container" style="max-width: 800px; margin: auto;">
    <form action="../actions/edit_huesped_process.php" method="POST">
        <input type="hidden" name="persona_id" value="<?php echo $huesped['PersonaID']; ?>">
        
        <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            <div class="form-group"><label>Primer Nombre:</label><input type="text" name="nombres" value="<?php echo htmlspecialchars($huesped['Nombres']); ?>" required></div>
            <div class="form-group"><label>Apellido Paterno:</label><input type="text" name="ape_paterno" value="<?php echo htmlspecialchars($huesped['Ape_Paterno']); ?>" required></div>
            <div class="form-group"><label>Apellido Materno:</label><input type="text" name="ape_materno" value="<?php echo htmlspecialchars($huesped['Ape_Materno']); ?>" required></div>
            <div class="form-group"><label>Correo Electrónico:</label><input type="email" name="correo" value="<?php echo htmlspecialchars($huesped['Correo']); ?>" required></div>
            <div class="form-group"><label>Número de Documento:</label><input type="text" name="doc_identidad" value="<?php echo htmlspecialchars($huesped['Doc_Identidad']); ?>" required></div>
            <div class="form-group"><label>Fecha de Nacimiento:</label><input type="date" name="fec_nacimiento" value="<?php echo htmlspecialchars($huesped['Fec_Nacimiento']); ?>" required></div>
            <div class="form-group"><label>Celular:</label><input type="tel" name="celular" value="<?php echo htmlspecialchars($huesped['Celular']); ?>" pattern="[0-9]{9}"></div>
            <div class="form-group">
                <label>Estado Civil:</label>
                <select name="e_civil" required>
                    <option value="Soltero/a" <?php if($huesped['E_Civil'] == 'Soltero/a') echo 'selected'; ?>>Soltero/a</option>
                    <option value="Casado/a" <?php if($huesped['E_Civil'] == 'Casado/a') echo 'selected'; ?>>Casado/a</option>
                    <option value="Viudo/a" <?php if($huesped['E_Civil'] == 'Viudo/a') echo 'selected'; ?>>Viudo/a</option>
                    <option value="Divorciado/a" <?php if($huesped['E_Civil'] == 'Divorciado/a') echo 'selected'; ?>>Divorciado/a</option>
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;"><label>Dirección:</label><input type="text" name="direccion" value="<?php echo htmlspecialchars($huesped['Direccion']); ?>" required></div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:15px;">Guardar Cambios</button>
    </form>
</div>

