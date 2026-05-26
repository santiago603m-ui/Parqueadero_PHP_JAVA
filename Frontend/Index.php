<?php
require_once 'config.php';

$registrosActivos = apiRequest('/registros', 'GET');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Parqueadero Boyacá</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/app.js"></script>
</head>
<body>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="entrada.php">Registrar Entrada</a>
        <a href="salida.php">Registrar Salida</a>
        <a href="vehiculos.php">Vehículos</a>
        <a href="historial.php">Historial</a>
    </nav>

    <div class="container">
        <h1>Vehículos en Parqueadero</h1>
        
        <?php if (isset($registrosActivos['error'])): ?>
            <div class="alert error"><?php echo $registrosActivos['error']; ?></div>
        <?php else: ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID Registro</th>
                        <th>ID Vehículo</th>
                        <th>Hora Entrada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrosActivos as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['id']); ?></td>
                        <td><?php echo htmlspecialchars($registro['vehiculoId']); ?></td>
                        <td><?php echo htmlspecialchars($registro['entrada']); ?></td>
                        <td><span class="badge active"><?php echo htmlspecialchars($registro['estado']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>