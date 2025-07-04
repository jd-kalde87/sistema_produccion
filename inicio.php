<?php
session_start();

// Si no hay sesión iniciada, redirigir al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        // Creamos variables de JavaScript con los datos de PHP
        const usuarioReporte = "<?php echo isset($_SESSION['nombre_usuario']) ? addslashes($_SESSION['nombre_usuario']) : 'N/A'; ?>";
        const fechaReporte = "<?php echo date('Y-m-d H:i:s'); ?>";
    </script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
                <h1>📊 TABLERO PRINCIPAL REGISTRO DIARIO DE PRODUCCION 📊</h1>
                <p>Bienvenido a la pagina principal del Sistema de Produccion</p>
                <p>desde aqui podras administrar y ejecutar la opcion que necesites</p>
        </div>
        <div class="button-grid">
            <a class="btn-regdiario" href="registro_diairo.php">INGRESAR INFORMACION DIARIA</a>
            <a class="btn-admaquinaria" href="admin_maquinaria.php">ADMINISTRAR MAQUINARIA</a>
            <a class="btn-oproduccion" href="opciones_produccion.php">OPCIONES DE PRODUCCION</a>
            <a class="btn-informes" href="informes.php">GENERACION DE INFORMES</a>
        </div>
        
        <div class="form-links">
            <p>Usuario en sesion: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></p>
        </div>
    </div>
 <div class="logout-container">
        <a href="logout.php" class="btn btn-eliminar" id="logout-btn">Cerrar sesión</a>
    </div>
</body>
</html>