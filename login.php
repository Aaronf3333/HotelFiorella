<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Si ya hay una sesión, redirigir al panel correspondiente
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol_id'] == 1) { // Administrador
        header('Location: admin/');
    } else { // Cliente
        header('Location: client/');
    }
    exit();
}
include('includes/header_public.php'); 
?>

<div class="login-container">
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        
<?php if(isset($_GET['notice']) && $_GET['notice'] == 'login_required'): ?>
    <p style="color: #0056b3; text-align:center; background-color: #cfe2ff; padding: 10px; border-radius: 5px;">
        Por favor, inicia sesión o regístrate para poder hacer una reserva.
    </p>
<?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <p style="color: red; text-align:center;">El correo o la contraseña son incorrectos.</p>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <p style="color: green; text-align:center;">¡Registro exitoso! Por favor, inicia sesión.</p>
        <?php endif; ?>

        <form action="actions/login_process.php" method="POST">
            <?php if (isset($_GET['redirect_to'])): ?>
            <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_GET['redirect_to']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
        <div class="links">
            <a href="register.php">¿No tienes cuenta? Regístrate aquí</a>
            <a href="https://media.tenor.com/aSkdq3IU0g0AAAAM/laughing-cat.gif">Olvidé mi contraseña</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>