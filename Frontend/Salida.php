<?php
/**
 * salida.php - Registrar salida y calcular tarifa
 * PHP llama: PUT /api/registros/{id}/salida → Java calcula tarifa y responde
 */
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['registroId'])) {
    header('Location: index.php');
    exit;
}

$registroId = (int)$_POST['registroId'];
$registro   = apiRequest("/registros/$registroId/salida", 'PUT');
$error      = isset($registro['error']) ? $registro['error'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Salida Registrada - Parqueadero Boyacá</title>
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
            <a href="reportes.php" class="nav-link">📊 Reportes</a>
        </nav>
    </div>
</header>

<main class="container">

    <?php if ($error): ?>
    <div class="alerta alerta-error">❌ <?= htmlspecialchars($error) ?></div>
    <a href="index.php" class="btn btn-azul">← Volver al inicio</a>

    <?php else: ?>
    <div class="recibo">
        <div class="recibo-header">
            <h1>🧾 Comprobante de Salida</h1>
            <p>Parqueadero Boyacá · <?= date('d/m/Y H:i:s') ?></p>
        </div>

        <div class="recibo-body">
            <div class="recibo-fila">
                <span class="recibo-label">Placa</span>
                <span class="recibo-valor placa"><?= htmlspecialchars($registro['placa']) ?></span>
            </div>
            <div class="recibo-fila">
                <span class="recibo-label">Tipo</span>
                <span class="recibo-valor"><?= htmlspecialchars($registro['tipo']) ?></span>
            </div>
            <div class="recibo-fila">
                <span class="recibo-label">Entrada</span>
                <span class="recibo-valor"><?= htmlspecialchars($registro['entrada']) ?></span>
            </div>
            <div class="recibo-fila">
                <span class="recibo-label">Salida</span>
                <span class="recibo-valor"><?= htmlspecialchars($registro['salida']) ?></span>
            </div>
            <div class="recibo-fila recibo-total">
                <span class="recibo-label">💰 Total a pagar</span>
                <span class="recibo-valor recibo-precio">
                    $<?= number_format($registro['tarifa'], 0, ',', '.') ?> COP
                </span>
            </div>
        </div>

        <div class="recibo-footer no-print">
            <p>¡Gracias por usar Parqueadero Boyacá!</p>
            <button type="button" id="btn-imprimir" class="btn btn-azul" onclick="window.print()">🖨️ Imprimir</button>
            <a href="index.php" class="btn btn-azul">← Volver al panel</a>
            <a href="entrada.php" class="btn btn-verde">⬇️ Nueva entrada</a>
        </div>
    </div>
    <?php endif; ?>

</main>

<footer class="footer">
    <p>SENA CIMM · ADSO 228118 · Regional Boyacá</p>
</footer>

<script src="js/app.js"></script>
</body>
</html>
