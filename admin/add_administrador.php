<?php
include('../includes/header_admin.php');
include('../includes/db.php');

// Consultas para los menús desplegables
$tipos_documento = $pdo->query("SELECT TipoDocumentoID, N_TipoDocumento FROM TiposDocumento WHERE Estado = '1'")->fetchAll(PDO::FETCH_ASSOC);
$cargos = $pdo->query("SELECT CargoID, NombreCargo FROM Cargos WHERE Estado = '1'")->fetchAll(PDO::FETCH_ASSOC);
$contratos = $pdo->query("SELECT ContratoID, Nom_Contrato FROM Contrato WHERE Estado = '1'")->fetchAll(PDO::FETCH_ASSOC);
$departamentos = $pdo->query("SELECT DepartamentoID, N_Departamento FROM Departamento WHERE Estado = '1' ORDER BY N_Departamento")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: 1 / -1; }
    .form-section-header { 
        grid-column: 1 / -1;
        font-size: 1.2em;
        font-weight: bold;
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 5px;
        margin-top: 20px;
        margin-bottom: 10px;
    }
</style>

<h2>Agregar Nuevo Administrador</h2>
<div class="form-container" style="max-width: 800px; margin: auto;">
    <?php if(isset($_GET['error'])): ?>
        <p style="color: red; text-align:center; background-color: #f8d7da; padding: 10px; border-radius: 5px;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
    <?php endif; ?>

    <form action="../actions/add_administrador_process.php" method="POST">
        <div class="form-grid">
            <h4 class="form-section-header">1. Datos Personales</h4>
            <div class="form-group"><label>Primer Nombre:</label><input type="text" name="nombres" required></div>
            <div class="form-group"><label>Apellido Paterno:</label><input type="text" name="ape_paterno" required></div>
            <div class="form-group"><label>Apellido Materno:</label><input type="text" name="ape_materno" required></div>
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


            <h4 class="form-section-header">2. Datos de Empleado y Usuario</h4>
            <div class="form-group"><label>Correo Electrónico (será el usuario):</label><input type="email" name="correo" required></div>
            <div class="form-group"><label>Contraseña:</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Cargo:</label>
                <select name="cargo_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($cargos as $cargo): ?>
                         <option value="<?php echo $cargo['CargoID']; ?>" <?php echo ($cargo['NombreCargo'] == 'Administrador') ? 'selected' : ''; ?>><?php echo htmlspecialchars($cargo['NombreCargo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Tipo de Contrato:</label>
                <select name="contrato_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($contratos as $contrato): ?>
                        <option value="<?php echo $contrato['ContratoID']; ?>"><?php echo htmlspecialchars($contrato['Nom_Contrato']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Salario (S/):</label><input type="number" step="0.01" name="salario" required></div>
            <div class="form-group"><label>Turno:</label>
                 <select name="turno" required>
                    <option value="Mañana">Mañana</option><option value="Tarde">Tarde</option><option value="Noche">Noche</option>
                </select>
            </div>
            <div class="form-group"><label>Fondo de Pensión:</label>
                <select name="fondo_pension" required>
                    <option value="AFP">AFP</option><option value="ONP">ONP</option>
                </select>
            </div>
            <div class="form-group"><label>Código ESSALUD:</label><input type="text" name="essalud" pattern="[0-9]{8}" title="Debe contener 8 dígitos" required></div>
            <div class="form-group full-width"><label>N° de Hijos:</label><input type="number" name="n_hijos" value="0" required></div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success" style="flex-grow: 1;">Crear Administrador</button>
            <a href="gestion_administradores.php" class="btn btn-secondary" style="flex-grow: 1;">Volver</a>
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