<?php
session_start();
// AHORA NECESITAMOS CONECTARNOS A LA BD PARA POBLAR EL MENÚ
require 'db.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Consultamos las máquinas disponibles para el menú desplegable
$maquinas = [];
$sql_maquinas = "SELECT id_maquina, marca_maquina, nro_cabezas FROM maquinas ORDER BY marca_maquina";
$resultado_maquinas = $conn->query($sql_maquinas);
if ($resultado_maquinas) {
    while($fila = $resultado_maquinas->fetch_assoc()) {
        $maquinas[] = $fila;
    }
}
$conn->close(); // Cerramos la conexión, ya no la necesitamos en esta página.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Tendencia por Máquina</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Tendencia de Puntadas por Máquina</h2>
            <p>Compara la producción diaria de puntadas de cada máquina en el rango de fechas que elijas.</p>
        </div>

        <div class="seccion-formulario">
            <form id="filtro-lineas-form">
                <div class="filtro-form">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="maquina">Máquina:</label>
                        <select id="maquina" name="maquina">
                            <option value="todas">-- Todas las Máquinas --</option>
                            <?php foreach ($maquinas as $maquina): ?>
                                <option value="<?= htmlspecialchars($maquina['id_maquina']) ?>">
                                    <?= htmlspecialchars(strtoupper($maquina['marca_maquina'] . ' - ' . $maquina['nro_cabezas'] . ' cabezas')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-consultar">Generar Gráfico</button>
                </div>
            </form>
        </div>

        <div class="seccion-consulta">
            <h3>Resultado del Periodo</h3>
            <div id="resultado-informe">
                <p class="alert alert-info">Por favor, selecciona los filtros y haz clic en "Generar Gráfico".</p>
            </div>
        </div>

        <div class="form-links">
            <a href="informes.php">Regresar al menú de informes</a>
        </div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/logic_lineas.js"></script>
</body>
</html>