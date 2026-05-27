<?php
/**
 * index.php - Panel principal Parqueadero Boyacá
 * Frontend PHP → consume API REST Java (Tomcat)
 */
require_once 'config.php';

// Obtener vehículos actualmente en el parqueadero
$activos   = apiRequest('/registros');
$vehiculos = apiRequest('/vehiculos');

$errorConexion = isset($activos['error']) || isset($vehiculos['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parqueadero Boyacá - Control de Vehículos</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <div class="header-content">
        <div class="logo">🅿️ Parqueadero Boyacá</div>
        <nav>
            <a href="index.php" class="nav-link active">🏠 Inicio</a>
            <a href="entrada.php" class="nav-link">⬇️ Registrar Entrada</a>
            <a href="historial.php" class="nav-link">📋 Historial</a>
            <a href="vehiculos.php" class="nav-link">🚗 Vehículos</a>
            <a href="reportes.php" class="nav-link">📊 Reportes</a>
        </nav>
    </div>
</header>

<main class="container">

    <?php if ($errorConexion): ?>
    <div class="alerta alerta-error">
        ⚠️ <strong>Error de conexión:</strong> No se pudo conectar con el servidor Java (Tomcat).
        Verifique que Tomcat esté corriendo en el puerto 8080.
    </div>
    <?php endif; ?>

    <h1 class="titulo-pagina">Panel de Control</h1>

    <!-- Tarjetas de resumen -->
    <div class="tarjetas-grid">
        <div class="tarjeta tarjeta-verde">
            <div class="tarjeta-icono">🚗</div>
            <div class="tarjeta-numero"><?= is_array($activos) && !isset($activos['error']) ? count($activos) : 0 ?></div>
            <div class="tarjeta-label">Vehículos adentro</div>
        </div>
        <div class="tarjeta tarjeta-azul">
            <div class="tarjeta-icono">📝</div>
            <div class="tarjeta-numero"><?= is_array($vehiculos) && !isset($vehiculos['error']) ? count($vehiculos) : 0 ?></div>
            <div class="tarjeta-label">Vehículos registrados</div>
        </div>
        <div class="tarjeta tarjeta-naranja">
            <div class="tarjeta-icono">⏰</div>
            <div class="tarjeta-numero"><?= date('H:i') ?></div>
            <div class="tarjeta-label">Hora actual</div>
        </div>
    </div>

    <!-- Vehículos actualmente en el parqueadero -->
    <section class="seccion">
        <h2>🚘 Vehículos en el parqueadero ahora</h2>

        <?php if (!$errorConexion && is_array($activos) && count($activos) > 0): ?>
        <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th>Entrada</th>
                    <th>Tiempo</th>
                    <th class="no-print">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activos as $reg): ?>
                <tr>
                    <td><?= htmlspecialchars($reg['id']) ?></td>
                    <td><strong class="placa"><?= htmlspecialchars($reg['placa']) ?></strong></td>
                    <td>
                        <span class="badge badge-<?= strtolower($reg['tipo']) ?>">
                            <?= htmlspecialchars($reg['tipo']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($reg['entrada']) ?></td>
                    <td class="tiempo" data-entrada="<?= htmlspecialchars($reg['entrada']) ?>">
                        Calculando...
                    </td>
                    <td class="no-print">
                        <form method="POST" action="salida.php">
                            <input type="hidden" name="registroId" value="<?= $reg['id'] ?>">
                            <button type="submit" class="btn btn-rojo">⬆️ Registrar Salida</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <div class="mensaje-vacio">
            🅿️ El parqueadero está vacío actualmente.
            <a href="entrada.php" class="enlace">Registrar primera entrada →</a>
        </div>
        <?php endif; ?>
    </section>

</main>

<footer class="footer">
    <p>SENA CIMM · ADSO 228118 · Regional Boyacá · <?= date('Y') ?></p>
    <p class="footer-tech">Frontend: <strong>PHP (Apache)</strong> → Backend: <strong>Java Servlets (Tomcat)</strong></p>
</footer>

<script src="js/app.js"></script>
</body>
</html>
