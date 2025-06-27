<?php
session_start();
include 'php/obtener_registros.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registros de Producción</title>
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css"> 
    <link rel="stylesheet" href="css/buttons.dataTables.min.css">
           
</head>
<body>
<div class="seccion-consulta">
            <div class="label_presentacion">
                <h2>Registros de Producción</h2>
                <p>En este contenido se encuentra todo lo registrado en el modulo de <b>registro diario</b>.</p>
            </div>
            <div class="rangofechas">
                <form method="GET" action="" class="filtro-form">
                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>" required>
                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>" required>
                    <button type="submit" class="btn btn-agregar">Generar Consulta</button>
                </form> 
            </div>
        <div id="contenedor-botones-export" class="dt-buttons"></div>
        <div id="contenedor-buscador"></div>

        <div class="tabla-contenedor">
                <table id="tabla-produccion" class="tabla-registro">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Colaborador</th>
                            <th>Máquina</th>
                            <th>Nro Cabezas</th>
                            <th>Turno</th>
                            <th>Orden</th>
                            <th>Referencia</th>
                            <th>Tipo de Bordado</th>
                            <th>Tamaño</th>
                            <th>Puntadas</th>
                            <th>Cantidad</th>
                            <th>Total Puntadas</th>
                            <th>Color</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($resultado) && $resultado): ?>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['marca_temporal']) ?></td>
                                <td><?= htmlspecialchars($row['colaborador']) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['marca_maquina'])) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['nro_cabezas'])) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['tipo_jornada'])) ?></td>
                                <td><?= htmlspecialchars($row['orden_produccion']) ?></td>
                                <td><?= htmlspecialchars($row['referencia']) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['tipo_bordado'])) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['tamaño_pieza'])) ?></td>
                                <td><?= htmlspecialchars($row['puntadas_diseño']) ?></td>
                                <td><?= htmlspecialchars($row['cantidad_unidades']) ?></td>
                                <td><?= htmlspecialchars($row['total_puntadas']) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['codigo_color'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['observaciones'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
        </div>
    <div class="form-links">
        <a href="informes.php">Regresar al menu anterior</a>
    </div>
 </div>
<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/jquery.dataTables.js"></script>
         



<script src="js/dataTables.buttons.min.js"></script>
<script src="js/jszip.min.js"></script>
<script src="js/buttons.html5.min.js"></script>
<script src="js/buttons.print.min.js"></script>
<script src="js/tabla_logic.js"></script>

</body>
</html>