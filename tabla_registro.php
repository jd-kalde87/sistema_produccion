<?php
session_start();
// La lógica de obtención de datos ahora se ejecuta primero
include 'php/obtener_registros.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registros de Producción</title>
    
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <link rel="stylesheet" href="css/styles.css">

</head>
<body>
<div class="reporte-container">
    <h2>Registros de Producción</h2>
    <p>En este contenido se encuentra todo lo registrado en el modulo de <b>registro diario</b>.</p>
    
    <form method="GET" action="">
        <label for="fecha_inicio">Desde:</label>
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>" required>

        <label for="fecha_fin">Hasta:</label>
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>" required>

        <button type="submit">Generar Consulta</button>
    </form>
    <br>
    
    <table id="tabla-produccion" class="tabla-registro display">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Colaborador</th>
            <th>Máquina</th>
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
        <?php if ($resultado): ?>
            <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['marca_temporal']) ?></td>
                <td><?= htmlspecialchars($row['colaborador']) ?></td>
                <td><?= strtoupper(htmlspecialchars($row['marca_maquina'])) ?></td>
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
    
    <div class="form-links">
        <a href="informes.php">Regresar al menu anterior</a>
    </div> 
</div>

<script src="js/jquery-3.7.0.min.js"></script>
<script src="js/jquery.dataTables.js"></script>
<script src="js/tabla_logic.js"></script>

</body>
</html>