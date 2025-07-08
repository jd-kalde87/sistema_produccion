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
    <title>Dashboard General de Producción</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/all.min.css">
    
    <script>
        const usuarioReporte = "<?php echo isset($_SESSION['nombre_usuario']) ? addslashes($_SESSION['nombre_usuario']) : 'N/A'; ?>";
        const fechaReporte = "<?php echo date('Y-m-d H:i:s'); ?>";
    </script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Dashboard Rorcentajes de Produccion</h2>
            <p>Análisis porcentual de la producción por diferentes categorías.</p>
        </div>

        <div class="seccion-formulario">
            <form id="filtro-dashboard-form">
                <div class="filtro-form">
                    <div class="form-group"><label for="fecha_inicio">Fecha Inicio:</label><input type="date" id="fecha_inicio" name="fecha_inicio" required></div>
                    <div class="form-group"><label for="fecha_fin">Fecha Fin:</label><input type="date" id="fecha_fin" name="fecha_fin" required></div>
                    <button type="submit" class="btn btn-consultar">Generar Dashboard</button>
                </div>
            </form>
        </div>

        <div id="resultado-informe"><p class="alert alert-info">Selecciona un rango de fechas para ver los gráficos.</p></div>
        <div class="form-links"><a href="informes.php">Regresar</a></div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/chartjs-plugin-datalabels.min.js"></script>
    <script src="js/logic_dashboard_general.js"></script>
</body>
</html>