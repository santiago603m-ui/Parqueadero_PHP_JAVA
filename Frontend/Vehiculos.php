<?php
require_once 'config.php';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    $datosVehiculo = [
        'placa'       => strtoupper(trim($_POST['placa'])),
        'tipo'        => $_POST['tipo'],
        'propietario' => trim($_POST['propietario']),
        'telefono'    => trim($_POST['telefono'])
    ];
    
    $respuesta = apiRequest('/vehiculos', 'POST', $datosVehiculo);
    
    if (isset($respuesta['error'])) {
        $mensaje = "<div class='alert error'>Error al registrar: " . htmlspecialchars($respuesta['error']) . "</div>";
    } else {
        $mensaje = "<div class='alert success'>Vehículo registrado correctamente de forma exitosa.</div>";
    }
}

if (isset($_GET['eliminar'])) {
    $idEliminar = (int)$_GET['eliminar'];
    $respuesta = apiRequest("/vehiculos/$idEliminar", 'DELETE');
    
    if (isset($respuesta['error'])) {
        $mensaje = "<div class='alert error'>Error al eliminar: " . htmlspecialchars($respuesta['error']) . "</div>";
    } else {
        $mensaje = "<div class='alert success'>Vehículo eliminado correctamente.</div>";
    }
}

$vehiculos = apiRequest('/vehiculos', 'GET');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vehículos - Parqueadero Boyacá</title>
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
        <h1>Gestión de Vehículos Registrados</h1>
        
        <?php echo $mensaje; ?>

        <div class="card">
            <h3>Registrar Nuevo Vehículo</h3>
            <form method="POST" action="vehiculos.php">
                <input type="hidden" name="action" value="crear">
                
                <div class="form-group">
                    <label>Placa:</label>
                    <input type="text" name="placa" placeholder="Ej: BOY001" required maxlength="10">
                </div>
                
                <div class="form-group">
                    <label>Tipo de Vehículo:</label>
                    <select name="tipo" required>
                        <option value="CARRO">Carro</option>
                        <option value="MOTO">Moto</option>
                        <option value="CAMION">Camión</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nombre del Propietario:</label>
                    <input type="text" name="propietario" placeholder="Ej: Juan Pérez" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <label>Teléfono de Contacto:</label>
                    <input type="text" name="telefono" placeholder="Ej: 3101234567" maxlength="15">
                </div>
                
                <button type="submit" class="btn-primary">Guardar Vehículo</button>
            </form>
        </div>

        <h2>Listado de Vehículos</h2>
        <?php if (isset($vehiculos['error']) || !is_array($vehiculos)): ?>
            <p class="alert error">No se pudo cargar el listado de vehículos.</p>
        <?php else: ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Tipo</th>
                        <th>Propietario</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vehiculos)): ?>
                        <tr><td colspan="6">No hay vehículos registrados en el sistema.</td></tr>
                    <?php else: ?>
                        <?php foreach ($vehiculos as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($v['placa']); ?></strong></td>
                            <td><?php echo htmlspecialchars($v['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($v['propietario']); ?></td>
                            <td><?php echo htmlspecialchars($v['telefono'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="vehiculos.php?eliminar=<?php echo $v['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('¿Está seguro de eliminar este vehículo? Se borrarán sus registros asociados.');">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>