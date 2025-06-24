<?php
session_start();
include 'db.php';
include 'php/funciones.php';


// Validar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Consulta para tamaños
$_SESSION['tamaño'] = $conn->query("SELECT * FROM tamaño_pieza")->fetch_all(MYSQLI_ASSOC);
?>

<!-- diseño de la pagina contenedora de las funciones -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Opciones de Produccion</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <h2>Opciones de Piezas</h2>
        <p>Bienvenido al módulo para administrar las opciones de las piezas.</p>
        <p>Desde aquí podrás agregar o eliminar los tamaños de las piezas.</p>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="alert alert-success"><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="alert alert-danger"><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="seccion-consulta">
                <h3>Tamaños de piezas registrados en el sistema</h3>
                <?= generarTabla(
                $_SESSION['tamaño'] ?? [],
                [
                    'id_tamaño_pieza' => 'ID', 
                    'tamaño_pieza' => 'Tamaño'
                ],
                'No hay tamaños registrados.'
            ) ?>
        </div>
     <div class="seccion-formulario">
        <div class="form-section">
            <h3>Registrar Nuevo Tamaño</h3>
            <form action="php/procesar_agregar_tamaño_pieza.php" method="POST">
                <div class="form-group">
                    <label for="tamaño_pieza">Tamaño de la pieza:</label>
                    <input type="text" id="tamaño_pieza" name="tamaño_pieza" required 
                           placeholder="Ej: L, XL">
                </div>
                <button type="submit" class="btn btn-agregar">Registrar Tamaño</button>
            </form>
        </div>
        </div>
        <div class="seccion-formulario">
            <div class="form-section">
                <h3>Eliminar Tamaño Existente</h3>
                <form action="php/procesar_eliminar_tamaño_pieza.php" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este tamaño? Esta acción no se puede deshacer.');">
                    <div class="form-group">
                        <label for="id_tamaño_pieza">Selecciona el tamaño a eliminar:</label>
                        <select name="id_tamaño_pieza" id="id_tamaño_pieza" required>
                            <option value="">-- Elige un tamaño --</option>
                            <?php
                            // Usamos los datos ya cargados en la sesión para llenar la lista
                            if (!empty($_SESSION['tamaño'])) {
                                foreach ($_SESSION['tamaño'] as $tamaño) {
                                    echo "<option value='" . htmlspecialchars($tamaño['id_tamaño_pieza']) . "'>" . htmlspecialchars(strtoupper($tamaño['tamaño_pieza'])) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-eliminar">Eliminar Tamaño</button>
                </form>
            </div>
            </div>
        
        <div class="form-links">
            <a href="opciones_produccion.php">Regresar al menú anterior</a>
        </div> 
    </div>
</body>
</html>