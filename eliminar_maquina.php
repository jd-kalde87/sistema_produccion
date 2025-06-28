<?php
session_start();
include 'db.php';
include 'php/funciones.php';

// Validar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Consultar máquinas al cargar la página
$_SESSION['maquinas'] = obtenerMaquinas($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Maquinaria</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Eliminar Maquinaria 📤</h2>
            <p>En esta seccion, podra ver la maquinaria registrada en el sistema</p>
            <p>y podra elimar alguna de ellas si asi lo requiere</p>
        </div>
                <!-- Sección de Consulta -->
        <div class="seccion-consulta">
            <div id="tabla-produccion">
                <h3>Maquinaria Registrada en el sistema</h3>
                <table class="tabla-registro">
                    <thead>
                        <tr>
                            <th>Marca</th>
                            <th>N° Cabezas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['maquinas'] as $maquina): ?>
                        <tr>
                            <td><?= htmlspecialchars($maquina['marca_maquina']) ?></td>
                            <td><?= $maquina['nro_cabezas'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Formulario para eliminar máquina -->
        <div class="seccion-formulario">
            <h3>Seleccionar Máquina a Eliminar</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <p class="alert alert-danger"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php elseif (isset($_SESSION['success'])): ?>
                <p class="alert alert-success"><?= $_SESSION['success'] ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form action="php/procesar_eliminar_maquina.php" method="POST" 
                  onsubmit="return confirm('¿Estás seguro de eliminar esta máquina?');">
                
                <div class="form-group">
                    <label for="id_maquina">Seleccione Máquina:</label>
                    <select id="id_maquina" name="id_maquina" required class="select-maquinas">
                        <option value="">-- Seleccione --</option>
                        <?= generarOpcionesMaquinas($_SESSION['maquinas']) ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-eliminar">Eliminar Máquina</button>
            </form>
        </div>
        

        
        <div class="form-links">
            <a href="admin_maquinaria.php">Regresar al menu anterior</a>
        </div> 
    </div>
</body>
</html>