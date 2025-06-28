<?php
session_start();
require 'db.php';
require 'php/funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Consulta para colores usando la función
$_SESSION['colores'] = obtenerColores($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Colores 👔</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Administración de Colores</h2>
            <p>En este módulo puedes consultar, agregar o eliminar los colores registrados en el sistema.</p>
        </div>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="seccion-consulta">
            <h3>Colores Registrados en el Sistema</h3>
            <div class="tabla-contenedor">
                <div id="tabla-produccion"> 
                    <?= generarTabla(
                        $_SESSION['colores'] ?? [],
                        [
                            'id_color' => 'ID',
                            'codigo_color' => 'Código de Color',
                            'descripcion_color' => 'Descripción'
                        ],
                        'No hay colores registrados.'
                    ) ?>
                </div>
            </div>
        </div>
        <div class="seccion-formulario">
            <h3>Agregar Nuevo Color</h3>
            <form action="php/procesar_agregar_color.php" method="POST">
                <div class="form-group">
                    <label for="codigo_color">Código del color:</label>
                    <input type="text" id="codigo_color" name="codigo_color" required 
                           placeholder="Ej: 18-3838">
                </div>
                <div class="form-group">
                    <label for="descripcion_color">Descripción del color:</label>
                    <input type="text" id="descripcion_color" name="descripcion_color" required 
                           placeholder="Ej: Ultra Violet">
                </div>
                <button type="submit" class="btn btn-agregar">Registrar Color</button>
            </form>
        </div>
        <div class="seccion-formulario">
            <h3>Eliminar un Color</h3>
            <form action="php/procesar_eliminar_color.php" method="POST" 
                  onsubmit="return confirm('¿Estás seguro de eliminar este color?');">
                <div class="form-group">
                    <label for="id_color_eliminar">Seleccione el color a eliminar:</label>
                    <select id="id_color_eliminar" name="id_color" required>
                        <option value="">-- Seleccione un color --</option>
                        <?php foreach ($_SESSION['colores'] as $color): ?>
                            <option value="<?= $color['id_color'] ?>">
                                <?= htmlspecialchars(strtoupper($color['codigo_color'] . ' - ' . $color['descripcion_color'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-eliminar">Eliminar Color</button>
            </form>
        </div>

        <div class="form-links">
            <a href="opciones_produccion.php">Regresar al menú anterior</a>
        </div>
    </div>
</body>
</html>