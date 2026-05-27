<?php
/**
 * reportes.php - Reporte de Ingresos del Día
 * Sistema de Gestión Parqueadero Boyacá
 * SENA CIMM - ADSO 2026
 *
 * Consume el endpoint: GET /api/registros/reporte
 * Muestra el total de ingresos y cantidad de salidas del día actual.
 */

require_once 'config.php';

$reporte = apiRequest('/registros/reporte', 'GET');

$errorConexion = false;
if (empty($reporte) || isset($reporte['error'])) {
    $errorConexion = true;
    $reporte = [
        'totalDia'        => 0.0,
        'cantidadSalidas' => 0,
        'fecha'           => date('Y-m-d')
    ];
}

$totalFormateado     = number_format((float)$reporte['totalDia'], 0, ',', '.');
$fechaFormateada     = isset($reporte['fecha'])
    ? date('d/m/Y', strtotime($reporte['fecha']))
    : date('d/m/Y');
$cantidadSalidas     = (int)($reporte['cantidadSalidas'] ?? 0);
$promedioFormateado  = $cantidadSalidas > 0
    ? number_format($reporte['totalDia'] / $cantidadSalidas, 0, ',', '.')
    : '0';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ingresos - Parqueadero Boyacá</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <div class="logo">🅿️ Parqueadero Boyacá</div>
        <nav>
            <a href="index.php" class="nav-link">🏠 Inicio</a>
            <a href="entrada.php" class="nav-link">⬇️ Registrar Entrada</a>
            <a href="historial.php" class="nav-link">📋 Historial</a>
            <a href="vehiculos.php" class="nav-link">🚗 Vehículos</a>
            <a href="reportes.php" class="nav-link active">📊 Reportes</a>
        </nav>
    </div>
</header>

<main class="container">

    <?php if ($errorConexion): ?>
    <div class="alerta alerta-error">
        ⚠️ <strong>Error de conexión:</strong> No se pudo conectar con el servidor Java (Tomcat).
        Verifique que el backend esté activo en el puerto 8080.
    </div>
    <?php endif; ?>

    <h1 class="titulo-pagina">Reporte de Ingresos del Día</h1>
    
    <div class="seccion" style="margin-bottom: 1rem;">
        <p style="color: #555; font-size: 0.95rem;">Resumen de recaudación correspondiente al <strong><?= htmlspecialchars($fechaFormateada) ?></strong></p>
    </div>

    <div class="no-print" style="margin-bottom: 1.5rem;">
        <a href="reportes.php" class="btn btn-azul">🔄 Actualizar Reporte</a>
    </div>

    <div class="tarjetas-grid">
        <div class="tarjeta tarjeta-verde">
            <div class="tarjeta-icono">💰</div>
            <div class="tarjeta-numero">$<?= $totalFormateado ?></div>
            <div class="tarjeta-label">Total recaudado hoy</div>
        </div>

        <div class="tarjeta tarjeta-naranja">
            <div class="tarjeta-icono">🚗</div>
            <div class="tarjeta-numero"><?= $cantidadSalidas ?></div>
            <div class="tarjeta-label">Vehículos atendidos</div>
        </div>

        <div class="tarjeta tarjeta-azul">
            <div class="tarjeta-icono">📈</div>
            <div class="tarjeta-numero">$<?= $promedioFormateado ?></div>
            <div class="tarjeta-label">Promedio por vehículo</div>
        </div>
    </div>

    <section class="seccion">
        <h2>📋 Resumen detallado</h2>
        
        <div class="tabla-container">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Valor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Fecha del reporte</td>
                        <td><?= htmlspecialchars($fechaFormateada) ?></td>
                        <td><span class="badge badge-moto">✔ Consultado</span></td>
                    </tr>
                    <tr>
                        <td>Vehículos con salida registrada hoy</td>
                        <td><?= $cantidadSalidas ?> vehículo(s)</td>
                        <td><span class="badge badge-moto">✔ Finalizado</span></td>
                    </tr>
                    <tr>
                        <td>Tarifa promedio por vehículo</td>
                        <td>$<?= $promedioFormateado ?> COP</td>
                        <td><span class="badge badge-azul">✔ Calculado</span></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>💰 TOTAL RECAUDADO HOY</strong></td>
                        <td colspan="2" class="text-right"><strong style="color: var(--verde-oscuro); font-size: 1.1rem;">$<?= $totalFormateado ?> COP</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <div class="alerta" style="background:#E3F2FD; border-left: 4px solid var(--azul-claro); color: #0D47A1;">
        ℹ️ <strong>Tarifas vigentes:</strong> 
        Carro $3.000/hora · Moto $1.500/hora · Camión $5.000/hora 
        (mínimo 1 hora, redondeado hacia arriba).
    </div>

</main>

<footer class="footer">
    <p>SENA CIMM · ADSO 228118 · Regional Boyacá · <?= date('Y') ?></p>
    <p class="footer-tech">Frontend: <strong>PHP (Apache)</strong> → Backend: <strong>Java Servlets (Tomcat)</strong></p>
</footer>

<script src="js/app.js"></script>
</body>
</html>