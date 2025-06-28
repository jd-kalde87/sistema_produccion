<?php
session_start();
include 'db.php';
include 'php/funciones.php';

// Validar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
$maquinas = obtenerOpciones2($conn, 'maquinas', 'id_maquina', 'marca_maquina', 'nro_cabezas');
$turnos = obtenerOpciones3($conn, 'jornada_laboral', 'id_jornada', 'tipo_jornada', 'horario');
$bordados = obtenerOpciones($conn, 'tipo_bordado', 'id_bordado', 'tipo_bordado');
$tamaños = obtenerOpciones($conn, 'tamaño_pieza', 'id_tamaño_pieza', 'tamaño_pieza');
$colores = obtenerOpciones($conn, 'color', 'id_color', 'codigo_color');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar Información Diaria</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="main-container">
        <div class="label_presentacion">
            <h2>Ingresar Información Diaria 📋</h2>
            <p>Bienvenido al modulo para ingresar la informacion diaria</p>
            <p>Por favor ingrese la informacion de produccion que usted realizo</p>
        </div>
        <?php if (isset($_GET['success'])): ?>
        <p class="alert alert-success">✅ Información registrada exitosamente.</p>
        <?php endif; ?>
        <form action="php/procesar_informacion_diaria.php" method="POST">

            <div class="form-group">
                <label for="maquina">Máquina:</label>
                <select name="maquina" required>
                    <option value="">Selecciona una máquina</option>
                    <?= $maquinas ?>
                </select>
            </div>

            <div class="form-group">
                <label for="turno">Turno (Jornada):</label>
                <select name="turno" required>
                    <option value="">Selecciona un turno</option>
                    <?= $turnos ?>
                </select>
            </div>

            <div class="form-group">
                <label for="orden">Número de Orden de Producción:</label>
                <input type="text" name="orden" required>
            </div>

            <div class="form-group">
                <label for="referencia">Número de Referencia:</label>
                <input type="text" name="referencia" required>
            </div>

            <div class="form-group">
                <label for="bordado">Tipo de Bordado:</label>
                <select name="bordado" required>
                    <option value="">Selecciona un tipo de bordado</option>
                    <?= $bordados ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tamaño">Tamaño de la Pieza:</label>
                <select name="tamaño" required>
                    <option value="">Selecciona un tamaño</option>
                    <?= $tamaños ?>
                </select>
            </div>

            <div class="form-group">
                <label for="puntadas">Número de Puntadas:</label>
                <input type="number" name="puntadas" id="puntadas" required>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad de Unidades Realizadas:</label>
                <input type="number" name="cantidad" id="cantidad" required>
            </div>

            <div class="form-group">
                <label for="total_puntadas">Total de Puntadas:</label>
                <input type="number" id="total_puntadas" name="total_puntadas" readonly>
            </div>

            <div class="form-group">
                <label for="color">Color:</label>
                <select name="color" required>
                    <option value="">Selecciona un color</option>
                    <?= $colores ?>
                </select>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones:</label>
                <textarea name="observaciones" rows="4" placeholder="coloque una o varias observaciones si al momento de realizar la orden de produccion se genero alguna"></textarea>
            </div>

            <button type="submit" class="btn btn-agregar">Guardar Información</button>
        </form>
         
        <div class="form-links">
            <a href="inicio.php">Regresar al inicio</a>
        </div>
    </div>
    
    <script src="js/calculos.js"></script>
</body>
</html>
