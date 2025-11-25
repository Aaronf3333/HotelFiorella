<?php
include('../includes/header_admin.php');
include('../includes/db.php');

$tipos_documento = $pdo->query("SELECT TipoDocumentoID, N_TipoDocumento FROM TiposDocumento WHERE Estado = '1'")->fetchAll(PDO::FETCH_ASSOC);
$departamentos = $pdo->query("SELECT DepartamentoID, N_Departamento FROM Departamento WHERE Estado = '1' ORDER BY N_Departamento")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: 1 / -1; }
</style>

<h2>Agregar Nuevo Cliente</h2>
<div class="form-container" style="max-width: 800px; margin: auto;">
    <?php if(isset($_GET['error'])): ?>
        <p style="color: red; text-align:center; background-color: #f8d7da; padding: 10px; border-radius: 5px;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
    <?php endif; ?>

    <form action="../actions/add_cliente_process.php" method="POST">
        <div class="form-grid">
            <div class="form-group"><label>Primer Nombre:</label><input type="text" name="nombres" required></div>
            <div class="form-group"><label>Apellido Paterno:</label><input type="text" name="ape_paterno" required></div>
            <div class="form-group"><label>Apellido Materno:</label><input type="text" name="ape_materno" required></div>
            <div class="form-group"><label>Correo Electrónico (será el usuario):</label><input type="email" name="correo" required></div>
            <div class="form-group"><label>Contraseña:</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Tipo de Documento:</label>
                <select name="tipo_documento_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tipos_documento as $tipo): ?>
                        <option value="<?php echo $tipo['TipoDocumentoID']; ?>"><?php echo htmlspecialchars($tipo['N_TipoDocumento']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Número de Documento:</label><input type="text" name="doc_identidad" required></div>
            <div class="form-group"><label>Fecha de Nacimiento:</label><input type="date" name="fec_nacimiento" required></div>
            <div class="form-group"><label>Celular:</label><input type="tel" name="celular" pattern="[0-9]{9}"></div>
            <div class="form-group"><label>Género:</label>
                <select name="genero" required><option value="M">Masculino</option><option value="F">Femenino</option></select>
            </div>
            <div class="form-group"><label>Estado Civil:</label>
                <select name="e_civil" required>
                    <option value="Soltero/a">Soltero/a</option><option value="Casado/a">Casado/a</option>
                    <option value="Viudo/a">Viudo/a</option><option value="Divorciado/a">Divorciado/a</option>
                </select>
            </div>
            <div class="form-group"><label>Departamento:</label>
                <select id="departamento" name="departamento_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($departamentos as $depto): ?>
                        <option value="<?php echo $depto['DepartamentoID']; ?>"><?php echo htmlspecialchars($depto['N_Departamento']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Provincia:</label><select id="provincia" name="provincia_id" required disabled><option value="">Seleccione...</option></select></div>
            <div class="form-group"><label>Distrito:</label><select id="distrito" name="distrito_id" required disabled><option value="">Seleccione...</option></select></div>
            <div class="form-group full-width"><label>Dirección:</label><input type="text" name="direccion" required></div>
        </div>
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success" style="flex-grow: 1;">Crear Cliente</button>
            <a href="gestion_clientes.php" class="btn btn-secondary" style="flex-grow: 1;">Volver</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departamentoSelect = document.getElementById('departamento');
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');

    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;
        provinciaSelect.innerHTML = '<option value="">Cargando...</option>';
        distritoSelect.innerHTML = '<option value="">Seleccione...</option>';
        provinciaSelect.disabled = true;
        distritoSelect.disabled = true;

        if (departamentoId) {
            fetch('../actions/get_locations.php?departamento_id=' + departamentoId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccione...</option>';
                    data.forEach(provincia => {
                        options += `<option value="${provincia.ProvinciaID}">${provincia.N_Provincia}</option>`;
                    });
                    provinciaSelect.innerHTML = options;
                    provinciaSelect.disabled = false;
                });
        }
    });

    provinciaSelect.addEventListener('change', function() {
        const provinciaId = this.value;
        distritoSelect.innerHTML = '<option value="">Cargando...</option>';
        distritoSelect.disabled = true;

        if (provinciaId) {
            fetch('../actions/get_locations.php?provincia_id=' + provinciaId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccione...</option>';
                    data.forEach(distrito => {
                        options += `<option value="${distrito.DistritoID}">${distrito.N_Distrito}</option>`;
                    });
                    distritoSelect.innerHTML = options;
                    distritoSelect.disabled = false;
                });
        }
    });
});
</script>