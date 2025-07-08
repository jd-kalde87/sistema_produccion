<?php
session_start();

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - Administrador</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        // Esta parte está perfecta para los informes
        const usuarioReporte = "<?php echo isset($_SESSION['nombre_usuario']) ? addslashes($_SESSION['nombre_usuario']) : 'N/A'; ?>";
        const fechaReporte = "<?php echo date('Y-m-d H:i:s'); ?>";
    </script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
                <h1>📊 TABLERO PRINCIPAL 📊</h1>
                <p>Bienvenido al panel de administrador, <?= htmlspecialchars($_SESSION['nombre_usuario']) ?>.</p>
                <p>Desde aquí puedes administrar y ejecutar la opción que necesites.</p>
        </div>
        <div class="button-grid">
            <a class="btn-regdiario" href="registro_diairo.php">INGRESAR PRODUCCIÓN DIARIA</a>
            <a class="btn-admaquinaria" href="admin_maquinaria.php">ADMINISTRAR MAQUINARIA</a>
            <a class="btn-oproduccion" href="opciones_produccion.php">OPCIONES DE PRODUCCIÓN</a>
            <a class="btn-informes" href="informes.php">GENERACIÓN DE INFORMES</a>
        </div>
        
        <div class="form-links">
            <p>Usuario en sesión: <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></p>
        </div>
    </div>
    <div class="logout-container">
        <a href="logout.php" class="btn btn-eliminar" id="logout-btn">Cerrar sesión</a>
    </div>
</body>
</html>