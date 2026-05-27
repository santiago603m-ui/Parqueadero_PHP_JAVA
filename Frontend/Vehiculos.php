<?php
/**
 * vehiculos.php - Gestión de vehículos registrados
 * PHP consume la API Java para CRUD completo
 */
require_once 'config.php';

$mensaje = null;
$error   = null;

// ─── Eliminar vehículo ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id   = (int)$_POST['eliminar_id'];
    $resp = apiRequest("/vehiculos/$id", 'DELETE');
    if (isset($resp['mensaje'])) {
        $mensaje = "Vehículo eliminado correctamente.";
    } else {
        $error = $resp['error'] ?? 'No se pudo eliminar el vehículo.';
    }
}

// ─── Listar todos ─────────────────────────────────────────────
$vehiculos = apiRequest('/vehiculos');
$errorLista = isset($vehiculos['error']) ? $vehiculos['error'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vehículos - Parqueadero Boyacá</title>
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
            <a href="vehiculos.php" class="nav-link active">🚗 Vehículos</a>
            <a href="reportes.php" class="nav-link">📊 Reportes</a>
        </nav>
    </div>
</header>

<main class="container">
    <h1 class="titulo-pagina">🚗 Vehículos Registrados</h1>

    <?php if ($mensaje): ?>
        <div class="alerta alerta-exito">✅ <?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alerta alerta-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($errorLista): ?>
        <div class="alerta alerta-error">❌ Error al cargar vehículos: <?= htmlspecialchars($errorLista) ?></div>
    <?php else: ?>

    <section class="seccion">
        <?php if (count($vehiculos) > 0): ?>
        <div class="buscador no-print">
            <label for="buscar">🔍 Buscar:</label>
            <input type="text" id="buscar" class="input-texto" placeholder="Placa, propietario o teléfono...">
        </div>
        <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Placa</th>
                    <th>Tipo</th>
                    <th>Propietario</th>
                    <th>Teléfono</th>
                    <th class="no-print">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehiculos as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><strong class="placa"><?= htmlspecialchars($v['placa']) ?></strong></td>
                    <td><span class="badge badge-<?= strtolower($v['tipo']) ?>"><?= $v['tipo'] ?></span></td>
                    <td><?= htmlspecialchars($v['propietario']) ?></td>
                    <td><?= htmlspecialchars($v['telefono']) ?></td>
                    <td class="no-print">
                        <form method="POST" style="display:inline"
                              onsubmit="return confirm('¿Eliminar vehículo <?= htmlspecialchars($v['placa']) ?>?')">
                            <input type="hidden" name="eliminar_id" value="<?= $v['id'] ?>">
                            <button type="submit" class="btn btn-rojo">🗑️ Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <div class="mensaje-vacio">
            🚗 No hay vehículos registrados aún.
            <a href="entrada.php" class="enlace">Registrar el primero →</a>
        </div>
        <?php endif; ?>
    </section>

    <?php endif; ?>
</main>

<footer class="footer">
    <p>SENA CIMM · ADSO 228118 · Regional Boyacá · <?= date('Y') ?></p>
</footer>

<script src="js/app.js"></script>
</body>
</html>
