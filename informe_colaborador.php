<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$colaboradores = [];
$sql_colaboradores = "SELECT DISTINCT colaborador FROM registro_diario WHERE colaborador IS NOT NULL AND colaborador != '' ORDER BY colaborador ASC";
$resultado = $conn->query($sql_colaboradores);
if ($resultado) {
    while($fila = $resultado->fetch_assoc()) {
        $colaboradores[] = $fila['colaborador'];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking y Desempeño de Colaboradores</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <script>
        const usuarioReporte = "<?php echo isset($_SESSION['nombre_usuario']) ? addslashes($_SESSION['nombre_usuario']) : 'N/A'; ?>";
        const fechaReporte = "<?php echo date('Y-m-d H:i:s'); ?>";
    </script>
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Ranking y Desempeño de Colaboradores</h2>
            <p>Selecciona los filtros para ver el ranking de producción y el comportamiento diario.</p>
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
                    <div class="form-group">
                        <label for="colaborador">Colaborador:</label>
                        <select id="colaborador" name="colaborador">
                            <option value="todos">-- Todos --</option>
                            <?php foreach ($colaboradores as $colaborador): ?>
                                <option value="<?= htmlspecialchars($colaborador) ?>">
                                    <?= htmlspecialchars(strtoupper($colaborador)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-consultar">Generar Informe</button>
                </div>
            </form>
        </div>

        <div id="contenedor-global-botones" style="margin-bottom: 20px;"></div>


        <div id="resultado-informe">
             <p class="alert alert-info">Por favor, selecciona los filtros y haz clic en "Generar Informe".</p>
        </div>

        <div class="form-links">
            <a href="informes.php">Regresar al menú de informes</a>
        </div>
    </div>

    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <script src="js/logic_informe_colaborador.js"></script>
</body>
</html>