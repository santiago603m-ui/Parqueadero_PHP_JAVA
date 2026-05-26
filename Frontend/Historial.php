<?php
require_once 'config.php';

$historial = apiRequest('/registros?estado=FINALIZADO', 'GET');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - Parqueadero Boyacá</title>
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
        <h1>Historial de Cierres y Salidas</h1>
        <p>A continuación se muestran los servicios completados y las tarifas cobradas:</p>
        
        <?php if (isset($historial['error'])): ?>
            <div class="alert error">
                Error al conectar con el servidor de datos: <?php echo htmlspecialchars($historial['error']); ?>
            </div>
        <?php else: ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID Registro</th>
                        <th>ID Vehículo</th>
                        <th>Fecha/Hora Entrada</th>
                        <th>Fecha/Hora Salida</th>
                        <th>Tarifa Cobrada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historial) || !is_array($historial)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No se registran salidas finalizadas en el sistema de forma reciente.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $totalAcumulado = 0;
                        foreach ($historial as $reg): 
                            $totalAcumulado += (double)$reg['tarifa'];
                        ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($reg['id']); ?></td>
                            <td><?php echo htmlspecialchars($reg['vehiculoId']); ?></td>
                            <td><?php echo htmlspecialchars($reg['entrada']); ?></td>
                            <td><?php echo htmlspecialchars($reg['salida']); ?></td>
                            <td class="txt-price">$<?php echo number_format($reg['tarifa'], 0, ',', '.'); ?> COP</td>
                            <td><span class="badge finished"><?php echo htmlspecialchars($reg['estado']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr style="background-color: #f1f1f1; font-weight: bold;">
                            <td colspan="4" style="text-align: right;">Total Recaudado en Historial:</td>
                            <td colspan="2" style="color: #2e7d32;">$<?php echo number_format($totalAcumulado, 0, ',', '.'); ?> COP</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>