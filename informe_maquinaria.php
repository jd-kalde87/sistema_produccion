<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Rendimiento de Maquinaria</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Rendimiento de Maquinaria</h2>
            <p>Selecciona un rango de fechas para comparar el total de puntadas y unidades producidas por cada máquina.</p>
        </div>

        <div class="seccion-formulario">
            <form id="filtro-maquinaria-form">
                <div class="filtro-form">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <button type="submit" class="btn btn-consultar">Generar Informe</button>
                </div>
            </form>
        </div>

        <div id="resultado-informe">
            </div>

        <div class="form-links">
            <a href="informes.php">Regresar al menú de informes</a>
        </div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/logic_informe_maquinaria.js"></script>
</body>
</html>