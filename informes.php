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
    <title>Opciones de Produccion</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Generacion de informes</h2>
            <p>Bienvenido al modulo para generar informes</p>
            <p>desde aqui podras generar los informes que mas relevancia tengan para la toma de decisiones en la produccion</p>
        </div>
        <div class="button-grid">
            <a href="tabla_registro.php">INFOME DIARIO DE PRODUCCION</a>
            <a href="#">VALUE</a>
            <a href="#">VALUE</a>
        </div>
        <div class="form-links">
            <a href="inicio.php">Regresar al inicio</a>
        </div> 
    </div>
</body>
</html>