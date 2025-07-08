<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['rol'] !== 'operario') {
    header("Location: login.php"); // Si no es operario, lo sacamos
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Operario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre_usuario']) ?></h2>
            <p>Tu rol es: <strong>OPERARIO</strong></p>
            <p>Desde aquí puedes acceder al registro de producción diaria.</p>
        </div>
        
        <div class="button-grid">
            <a href="registro_diairo.php">REGISTRAR PRODUCCIÓN DIARIA</a>
        </div>
        
        <div class="form-links">
            <a href="logout.php">Cerrar Sesión</a>
        </div> 
    </div>
</body>
</html>