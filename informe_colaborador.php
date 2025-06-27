<?php
session_start();
// No se necesita `db.php` aquí, ya que los datos los pediremos a la API.

// Validamos la sesión del usuario, como en todas tus páginas.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Colaborador Destacado</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Colaborador con Mayor Producción</h2>
            <p>Selecciona un rango de fechas para ver quién ha producido más unidades.</p>
        </div>

        <div class="seccion-formulario">
            <form id="filtro-colaborador-form">
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

        <div class="seccion-consulta">
            <h3>Resultado</h3>
            <div id="resultado-informe">
                <p class="alert alert-info">Por favor, selecciona un rango de fechas y haz clic en "Generar Informe".</p>
            </div>
        </div>

        <div class="form-links">
            <a href="informes.php">Regresar al menú de informes</a>
        </div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/logic_informe_colaborador.js"></script>
</body>
</html>