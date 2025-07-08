<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$maquinas = [];
$sql_maquinas = "SELECT id_maquina, marca_maquina, nro_cabezas FROM maquinas ORDER BY marca_maquina";
$resultado_maquinas = $conn->query($sql_maquinas);
if ($resultado_maquinas) {
    while($fila = $resultado_maquinas->fetch_assoc()) {
        $maquinas[] = $fila;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Unidades por Máquina</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    
    <script>
        const usuarioReporte = "<?php echo isset($_SESSION['nombre_usuario']) ? addslashes($_SESSION['nombre_usuario']) : 'N/A'; ?>";
        const fechaReporte = "<?php echo date('Y-m-d H:i:s'); ?>";
    </script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Informe de produccion por maquina (unidades producidas)</h2>
            <p>Filtra para analizar rankings, desgloses mensuales y tendencias de unidades producidas.</p>
        </div>

        <div class="seccion-formulario">
            <form id="filtro-maquinaria-form">
                <div class="filtro-form">
                    <div class="form-group"><label for="fecha_inicio">Fecha Inicio:</label><input type="date" id="fecha_inicio" name="fecha_inicio" required></div>
                    <div class="form-group"><label for="fecha_fin">Fecha Fin:</label><input type="date" id="fecha_fin" name="fecha_fin" required></div>
                    <div class="form-group">
                        <label for="maquina">Máquina:</label>
                        <select id="maquina" name="maquina">
                            <option value="todas">-- Todas --</option>
                            <?php foreach ($maquinas as $maquina): ?>
                                <option value="<?= htmlspecialchars($maquina['id_maquina']) ?>"><?= htmlspecialchars(strtoupper($maquina['marca_maquina'] . ' - ' . $maquina['nro_cabezas'] . ' cabezas')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-consultar">Generar Informe</button>
                </div>
            </form>
        </div>

        <div id="contenedor-global-botones" style="margin-bottom: 20px;"></div>
        <div id="resultado-informe"><p class="alert alert-info">Selecciona los filtros para empezar.</p></div>
        <div class="form-links"><a href="informes.php">Regresar</a></div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/jquery.dataTables.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="js/pdfmake.min.js"></script>
    <script src="js/vfs_fonts.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <script src="js/logic_informe_maquinaria.js"></script>
</body>
</html>