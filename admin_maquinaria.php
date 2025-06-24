<?php
session_start();
include 'db.php';

// Validar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administracion de Maquinaria</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <h2>Administracion de Maquinaria</h2>
        <p>Bienvenido al modulo para administrar las opciones de la maquinaria</p>
        <p>desde aqui podras agregar o eliminar maquinaria registrada en el sistema</p>
        
        <div class="button-grid">
            <a href="agregar_maquinaria.php">AGREGAR NUEVAS MAQUINAS</a>
            <a href="eliminar_maquina.php">ELIMINAR MAQUINAS</a>
        </div>
        <div class="form-links">
            <a href="inicio.php">Regresar al inicio</a>
        </div> 
    </div>
</body>
</html>