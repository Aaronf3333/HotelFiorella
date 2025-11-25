<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/db.php');

// --- LÓGICA DE PROCESAMIENTO FINAL ---
if (isset($_POST['final_confirm']) && $_POST['final_confirm'] == '1') {
    
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['cliente_id'])) {
        header('Location: login.php');
        exit();
    }

    $cliente_id = $_SESSION['cliente_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $habitacion_id = $_POST['habitacion_id'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_salida = $_POST['fecha_salida'];
    $metodo_pago_id = $_POST['metodo_pago_id'];
    
    try {
        $pdo->beginTransaction();

        // ===== LÓGICA AUTOMÁTICA DE BOLETA/FACTURA =====
        // 1. Verificamos el tipo de cliente (Persona o Empresa)
        $stmt_cliente_tipo = $pdo->prepare("SELECT PersonaID FROM Clientes WHERE ClienteID = ?");
        $stmt_cliente_tipo->execute([$cliente_id]);
        $es_persona = $stmt_cliente_tipo->fetchColumn();

        // 2. Asignamos el tipo de documento correspondiente
        $tipo_documento = $es_persona ? 'B' : 'F'; // 'B' para Boleta (persona), 'F' para Factura (empresa)

        // 3. Insertamos la reserva con el tipo de documento correcto
        $sql_reserva = "INSERT INTO Reservas (ClienteID, UsuarioID, HabitacionID, FechaEntrada, FechaSalida, MetodoPagoID, TipoDocumento, Estado) VALUES (?, ?, ?, ?, ?, ?, ?, '1')";
        $stmt_reserva = $pdo->prepare($sql_reserva);
        $stmt_reserva->execute([$cliente_id, $usuario_id, $habitacion_id, $fecha_entrada, $fecha_salida, $metodo_pago_id, $tipo_documento]);
        $reserva_id = $pdo->lastInsertId();

        // (El resto de la lógica para Venta y Boleta/Factura no cambia)
        $stmt_precio = $pdo->prepare("SELECT PrecioPorNoche FROM Habitaciones WHERE HabitacionID = ?");
        $stmt_precio->execute([$habitacion_id]);
        $precio_noche = $stmt_precio->fetchColumn();
        $dias = (strtotime($fecha_salida) - strtotime($fecha_entrada)) / 86400;
        $total = $precio_noche * $dias;
        $sql_venta = "INSERT INTO Venta (ReservaID, Total, Estado) VALUES (?, ?, '1')";
        $pdo->prepare($sql_venta)->execute([$reserva_id, $total]);
        
        $numero_comprobante = str_pad($reserva_id, 4, '0', STR_PAD_LEFT);
        if ($tipo_documento == 'B') {
            $sql_comprobante = "INSERT INTO Boleta (ReservaID, Numero, Serie, Estado) VALUES (?, ?, 'B001', '1')";
        } else {
            $sql_comprobante = "INSERT INTO Factura (ReservaID, Numero, Serie, Estado) VALUES (?, ?, 'F001', '1')";
        }
        $pdo->prepare($sql_comprobante)->execute([$reserva_id, $numero_comprobante]);

        $pdo->commit();
        
        header('Location: admin/generar_recibo.php?id=' . $reserva_id);
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        header('Location: reservar.php?error=' . urlencode('Error al procesar la reserva.'));
        exit();
    }
}

// --- LÓGICA PARA MOSTRAR LA PÁGINA DE PAGO ---
$habitacion_id = $_POST['habitacion_id'] ?? null;
$fecha_entrada = $_POST['fecha_entrada'] ?? null;
$fecha_salida = $_POST['fecha_salida'] ?? null;
$metodo_pago_id = $_POST['metodo_pago_id'] ?? null;

if (!$habitacion_id) { die("Error: Faltan datos."); }
include('includes/header_public.php');
?>
<style>
    .payment-container { display: flex; justify-content: center; padding: 40px 0; }
    .payment-box { padding: 30px; background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1); border-radius: 8px; width: 600px; text-align: center; }
    .payment-instruction img { max-width: 300px; margin: 20px 0; }
</style>

<div class="payment-container">
    <div class="payment-box">
        <h2>Instrucciones de Pago</h2>
        <div class="payment-instruction">
            <?php
            switch ($metodo_pago_id) {
                case '1': echo '<p>Escanee el siguiente código para pagar con Yape:</p><img src="img/yape.jpg" alt="Código QR de Yape">'; break;
                case '2': echo '<p>Escanee el siguiente código para pagar con Plin:</p><img src="img/plin.jpg" alt="Código QR de Plin">'; break;
                case '3': echo '<h3>Pago en Recepción</h3><p>Por favor, acérquese a la recepción del hotel para completar el pago.</p>'; break;
            }
            ?>
        </div>
        
        <form action="pago.php" method="POST">
            <input type="hidden" name="habitacion_id" value="<?php echo htmlspecialchars($habitacion_id); ?>">
            <input type="hidden" name="fecha_entrada" value="<?php echo htmlspecialchars($fecha_entrada); ?>">
            <input type="hidden" name="fecha_salida" value="<?php echo htmlspecialchars($fecha_salida); ?>">
            <input type="hidden" name="metodo_pago_id" value="<?php echo htmlspecialchars($metodo_pago_id); ?>">
            <input type="hidden" name="final_confirm" value="1">
            <button type="submit" class="btn btn-success" style="width:100%; margin-top:20px; padding: 12px;">Continuar</button>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>