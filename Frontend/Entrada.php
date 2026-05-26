<?php
require_once 'config.php';
$mensaje = '';
$vehiculo = null;

if (isset($_GET['placa'])) {
    $placaBuscada = strtoupper($_GET['placa']);
    $resultadoBusqueda = apiRequest('/vehiculos?placa=' . urlencode($placaBuscada), 'GET');
    
    if (!empty($resultadoBusqueda) && !isset($resultadoBusqueda['error'])) {
        $vehiculo = $resultadoBusqueda; 
    } else {
        $mensaje = "Vehículo no encontrado. Debe registrarlo primero en la sección Vehículos.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehiculo_id'])) {
    $datosEntrada = [
        'vehiculoId' => (int)$_POST['vehiculo_id']
    ];
    $respuesta = apiRequest('/registros', 'POST', $datosEntrada);
    
    if (isset($respuesta['mensaje'])) {
        $mensaje = "Entrada registrada con éxito.";
        $vehiculo = null; 
    } else {
        $mensaje = "Error al registrar entrada: " . ($respuesta['error'] ?? 'Desconocido');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrada - Parqueadero Boyacá</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/app.js"></script>
</head>
<body>
    <div class="container">
        <h1>Registrar Entrada</h1>
        
        <?php if ($mensaje): ?>
            <p class="alert"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="GET" action="entrada.php">
            <label>Buscar por Placa:</label>
            <input type="text" name="placa" placeholder="Ej: BOY001" required>
            <button type="submit">Buscar</button>
        </form>

        <?php if ($vehiculo): ?>
            <div class="card">
                <h3>Datos del Vehículo</h3>
                <p><strong>Placa:</strong> <?php echo htmlspecialchars($vehiculo['placa'] ?? ''); ?></p>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($vehiculo['tipo'] ?? ''); ?></p>
                <p><strong>Propietario:</strong> <?php echo htmlspecialchars($vehiculo['propietario'] ?? ''); ?></p>
                
                <form method="POST" action="entrada.php">
                    <input type="hidden" name="vehiculo_id" value="<?php echo htmlspecialchars($vehiculo['id'] ?? ''); ?>">
                    <button type="submit" class="btn-success">Confirmar Entrada</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>