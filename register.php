<?php
// Incluimos el header y la conexión a la BD
include('includes/header_public.php');
include('includes/db.php');

// Consulta para llenar el dropdown de Tipos de Documento
$tipos_doc_sql = "SELECT TipoDocumentoID, N_TipoDocumento FROM TiposDocumento WHERE Estado = '1'";
$stmt_tipos_doc = $pdo->query($tipos_doc_sql);
$tipos_documento = $stmt_tipos_doc->fetchAll(PDO::FETCH_ASSOC);

// --- NUEVO: Consulta para llenar el dropdown de Departamentos ---
$departamentos_sql = "SELECT DepartamentoID, N_Departamento FROM Departamento WHERE Estado = '1' ORDER BY N_Departamento";
$stmt_departamentos = $pdo->query($departamentos_sql);
$departamentos = $stmt_departamentos->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .register-container { display: flex; justify-content: center; padding: 40px 0; }
    .register-box { padding: 30px; background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; width: 800px; }
    .register-box h2 { text-align: center; color: var(--primary-color); margin-bottom: 25px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; }
    .full-width { grid-column: 1 / -1; }
    .btn-register { width: 100%; padding: 12px; font-size: 1.1em; }
</style>

<div class="register-container">
    <div class="register-box">
        <h2>Crear una Cuenta</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <p style="color: red; text-align:center;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="actions/register_process.php" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nombres">Primer Nombre:</label>
                    <input type="text" id="nombres" name="nombres" required>
                </div>
                <div class="form-group">
                    <label for="ape_paterno">Apellido Paterno:</label>
                    <input type="text" id="ape_paterno" name="ape_paterno" required>
                </div>
                <div class="form-group">
                    <label for="ape_materno">Apellido Materno:</label>
                    <input type="text" id="ape_materno" name="ape_materno" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico (será tu usuario):</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <div class="form-group">
                    <label for="tipo_documento">Tipo de Documento:</label>
                    <select id="tipo_documento" name="tipo_documento_id" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($tipos_documento as $tipo): ?>
                            <option value="<?php echo $tipo['TipoDocumentoID']; ?>"><?php echo htmlspecialchars($tipo['N_TipoDocumento']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="doc_identidad">Número de Documento:</label>
                    <input type="text" id="doc_identidad" name="doc_identidad" required>
                </div>
                <div class="form-group">
                    <label for="fec_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fec_nacimiento" name="fec_nacimiento" required>
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="tel" id="celular" name="celular" pattern="[0-9]{9}" title="Debe contener 9 dígitos">
                </div>
                <div class="form-group">
                    <label for="genero">Género:</label>
                    <select id="genero" name="genero" required>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="e_civil">Estado Civil:</label>
                    <select id="e_civil" name="e_civil" required>
                        <option value="Soltero/a">Soltero/a</option>
                        <option value="Casado/a">Casado/a</option>
                        <option value="Viudo/a">Viudo/a</option>
                        <option value="Divorciado/a">Divorciado/a</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="departamento">Departamento:</label>
                    <select id="departamento" name="departamento_id" required>
                        <option value="">Seleccione un Departamento...</option>
                        <?php foreach ($departamentos as $depto): ?>
                            <option value="<?php echo $depto['DepartamentoID']; ?>"><?php echo htmlspecialchars($depto['N_Departamento']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="provincia">Provincia:</label>
                    <select id="provincia" name="provincia_id" required disabled>
                        <option value="">Seleccione una Provincia...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="distrito">Distrito:</label>
                    <select id="distrito" name="distrito_id" required disabled>
                        <option value="">Seleccione un Distrito...</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>

                <div class="form-group full-width">
                    <button type="submit" class="btn btn-primary btn-register">Registrarme</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const departamentoSelect = document.getElementById('departamento');
    const provinciaSelect = document.getElementById('provincia');
    const distritoSelect = document.getElementById('distrito');

    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;
        provinciaSelect.innerHTML = '<option value="">Cargando...</option>';
        distritoSelect.innerHTML = '<option value="">Seleccione un Distrito...</option>';
        provinciaSelect.disabled = true;
        distritoSelect.disabled = true;

        if (departamentoId) {
            fetch('actions/get_locations.php?departamento_id=' + departamentoId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccione una Provincia...</option>';
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
            fetch('actions/get_locations.php?provincia_id=' + provinciaId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Seleccione un Distrito...</option>';
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


<?php
include('includes/footer.php');
?>