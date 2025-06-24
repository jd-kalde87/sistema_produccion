<?php
session_start();
require 'db.php';
require 'php/funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Consulta para bordados
$_SESSION['bordados'] = obtenerTiposBordado($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Bordado</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <h2>Tipos de Bordado</h2>
        <p>En este modulo puedes consultar los tipos de bordado registrados en el sistema</p>
        <p>y tambien puede agregar o eliminar el que tu elijas</p>

        <!-- Mensajes de éxito/error -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Tabla de bordados existentes -->
        <div class="seccion-consulta">
            <h3>Tipos de Bordado Registrados en el Sistema</h3>
            <div class="tabla-contenedor">
                    <?= generarTabla(
                        $_SESSION['bordados'] ?? [],
                        [
                            'id_bordado' => 'ID',
                            'tipo_bordado' => 'Tipo de Bordado'
                        ],
                        'No hay tipos de bordado registrados.'
                    ) ?>
            </div>
        </div>

        <!-- Formulario para agregar -->
        <div class="seccion-formulario">
            <h3>Agregar Nuevo Tipo de Bordado</h3>
            <form action="php/procesar_agregar_bordado.php" method="POST">
                <div class="form-group">
                    <label for="tipo_bordado">Tipo de bordado:</label>
                    <input type="text" id="tipo_bordado" name="tipo_bordado" required 
                           placeholder="Ej: Puntada cadena, Realce">
                </div>
                <button type="submit" class="btn btn-agregar">Registrar</button>
            </form>
        </div>

        <!-- Formulario para eliminar -->
        <div class="seccion-formulario">
            <h3>Eliminar un Tipo de bordado</h3>
            <form action="php/procesar_eliminar_bordado.php" method="POST" 
                  onsubmit="return confirm('¿Estás seguro de eliminar este tipo de bordado?');">
                <div class="form-group">
                    <label for="id_bordado">Seleccione:</label>
                    <select id="id_bordado" name="id_bordado" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($_SESSION['bordados'] as $bordado): ?>
                            <option value="<?= $bordado['id_bordado'] ?>">
                                <?= htmlspecialchars(strtoupper($bordado['tipo_bordado'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-eliminar">Eliminar</button>
            </form>
        </div>

        <div class="form-links">
            <a href="opciones_produccion.php">Regresar al anterior menu</a>
        </div>
    </div>
</body>
</html>