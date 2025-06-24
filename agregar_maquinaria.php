<?php
session_start();
include 'db.php';
include 'php/funciones.php';

// Validar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
// Consulta para máquinas (ejemplo adicional)
$_SESSION['maquinas'] = $conn->query("SELECT * FROM maquinas")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Maquinaria</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <h2>Agregar Maquinaria</h2>
        <p>En esta seccion, podra ver la maquinaria registrada en el sistema</p>
        <p>y podra agregar mas al registro</p>
        <div class="seccion-consulta">
                <h3>Maquinaria Registrada en el sistema</h3>
                <?= generarTabla(
                $_SESSION['maquinas'] ?? [],
                [
                    'id_maquina' => 'ID',
                    'marca_maquina' => 'Marca',
                    'nro_cabezas' => 'N° Cabezas'
                ],
                'No hay máquinas registradas.'
                ) ?>
        </div>
        <!-- en esta parte se define los mensajes de error y validacion si la maquina existe -->
        <div class="seccion-formulario">
            <h3>Agregar Nueva Máquina</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <p class="alert alert-danger"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php elseif (isset($_SESSION['success'])): ?>
                <p class="alert alert-success"><?= $_SESSION['success'] ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <!-- en esta parte se inicia la construccion del formulario, y se enlaza con el archivo encargado de procesar los datos que ingrese el usuario   -->
            <form action="php/procesar_agregar_maquina.php" method="POST">
                <div class="form-group">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required 
                           placeholder="Ej: BROTHER, TAJIMA">
                </div>
                
                <div class="form-group">
                    <label for="cabezas">Número de Cabezas:</label>
                    <input type="number" id="cabezas" name="cabezas" required 
                           min="1" placeholder="Ej: 1, 2, 4...">
                </div>
                
                <button type="submit" class="btn btn-agregar">Registrar Máquina</button>
            </form>
        </div>
        
          <div class="form-links">
            <a href="admin_maquinaria.php">Regresar al menu anterior</a>
        </div> 
    </div>
</body>
</html>