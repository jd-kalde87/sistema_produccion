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
            <a class="btn-informes" href="tabla_registro.php">INFOME DIARIO DE PRODUCCION</a>
            <a class="btn-informes" href="informe_colaborador.php">INFORME COLABORADOR CON MAS PRODUCCION</a>
            <a class="btn-informes" href="informe_maquinaria.php">INFORME DE RENDIMIENTO DE MAQUINARIA</a>
            <a class="btn-informes" href="informe_lineas.php">PRODUCCIÓN DIARIA POR MÁQUINA</a>
        </div>
        <div class="form-links">
            <a href="inicio.php">Regresar al inicio</a>
        </div> 
    </div>
</body>
</html>