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
            <h2>Opciones de Produccion 🧵</h2>
            <p>Bienvenido al modulo para administrar las opciones de produccion</p>
            <p>desde aqui podras agregar, eliminar o editar las diferentes opciones de produccion como: tamaño de las pieza, color y tipos de bordado</p>
        </div>
        <div class="button-grid">
            <a class="btn-oproduccion" href="admin_piezas.php">TAMAÑO DE PIEZAS</a>
            <a class="btn-oproduccion" href="admin_bordados.php">TIPOS DE BORDADOS</a>
            <a class="btn-oproduccion" href="admin_colores.php">COLORES</a>
        </div>
        <div class="form-links">
            <a href="inicio.php">Regresar al inicio</a>
        </div> 
    </div>
</body>
</html>