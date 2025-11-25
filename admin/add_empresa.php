<?php
include('../includes/header_admin.php');
include('../includes/db.php');
?>

<h2>Agregar Nueva Empresa</h2>
<div class="form-container" style="max-width: 800px; margin: auto;">
    <?php if(isset($_GET['error'])): ?>
        <p style="color: red; text-align:center; background-color: #f8d7da; padding: 10px; border-radius: 5px;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
    <?php endif; ?>

    <form action="../actions/add_empresa_process.php" method="POST">
        <h4>Datos de la Empresa</h4>
        <div class="form-group">
            <label>Razón Social:</label>
            <input type="text" name="razon_social" required>
        </div>
        <div class="form-group">
            <label>RUC:</label>
            <input type="text" name="ruc" pattern="[0-9]{11}" title="El RUC debe contener 11 dígitos" required>
        </div>
        <div class="form-group">
            <label>Dirección:</label>
            <input type="text" name="direccion" required>
        </div>
        <div class="form-group">
            <label>Teléfono:</label>
            <input type="tel" name="telefono" pattern="[0-9]{9}" title="El teléfono debe contener 9 dígitos" required>
        </div>
        
        <hr style="margin: 20px 0;">
        <h4>Datos de Usuario para la Empresa</h4>
        <div class="form-group">
            <label>Correo Electrónico (será el usuario):</label>
            <input type="email" name="correo" required>
        </div>
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success" style="flex-grow: 1;">Crear Empresa</button>
            <a href="gestion_empresas.php" class="btn btn-secondary" style="flex-grow: 1;">Volver</a>
        </div>
    </form>
</div>