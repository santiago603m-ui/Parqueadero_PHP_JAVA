<?php
require_once 'config.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro_id'])) {
    $registroId = (int)$_POST['registro_id'];
    
    $respuesta = apiRequest("/registros/$registroId/salida", 'PUT');
    
    if (isset($respuesta['mensaje'])) {
        $mensaje = "Salida procesada. Tarifa calculada correctamente.";
    } else {
        $mensaje = "Error: " . ($respuesta['error'] ?? 'No se pudo procesar la salida');
    }
}

$registrosActivos = apiRequest('/registros', 'GET');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salida - Parqueadero Boyacá</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/app.js"></script>
</head>
<body>
    <div class="container">
        <h1>Registrar Salida</h1>
        
        <?php if ($mensaje): ?>
            <p class="alert"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST" action="salida.php">
            <label>Seleccionar Vehículo a retirar:</label>
            <select name="registro_id" required>
                <option value="">-- Seleccione un vehículo activo --</option>
                <?php 
                if (!isset($registrosActivos['error']) && is_array($registrosActivos)) {
                    foreach ($registrosActivos as $reg) {
                        echo "<option value=\"{$reg['id']}\">Registro #{$reg['id']} - Vehículo ID: {$reg['vehiculoId']}</option>";
                    }
                }
                ?>
            </select>
            <button type="submit" class="btn-danger">Registrar Salida y Cobrar</button>
        </form>
    </div>
</body>
</html>