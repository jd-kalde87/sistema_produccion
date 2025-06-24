<?php
session_start();
include '../db.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Datos automáticos desde la sesión
$marca_temporal = $_SESSION['marca_temporal']; // asignada al iniciar sesión
$colaborador = $_SESSION['identificacion'] . ' - ' . strtoupper($_SESSION['nombre_usuario']);

// Datos del formulario
$maquina = $_POST['maquina'];
$turno = $_POST['turno'];
$orden = strtoupper(trim($_POST['orden']));
$referencia = strtoupper(trim($_POST['referencia']));
$bordado = $_POST['bordado'];
$tamaño = $_POST['tamaño'];
$puntadas = intval($_POST['puntadas']);
$cantidad = intval($_POST['cantidad']);
$total = intval($_POST['total_puntadas']);
$color = $_POST['color'];
$observaciones = isset($_POST['observaciones']) ? strtoupper(trim($_POST['observaciones'])) : null;

// Insertar en la tabla registro_diario
$sql = "INSERT INTO registro_diario (marca_temporal,colaborador,maquina_operada,turno,orden_produccion,referencia,tipo_bordado, 
    tamaño_pieza,puntadas_diseño,cantidad_unidades,total_puntadas,color_realizado,observaciones) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssiiiss",$marca_temporal,$colaborador,$maquina,$turno,$orden,$referencia,$bordado,
    $tamaño,$puntadas,$cantidad,$total,$color,$observaciones);

if ($stmt->execute()) {
    header("Location: ../registro_diairo.php?success=1");
    exit;
} else {
    echo "❌ Error al guardar: " . $stmt->error;
}
?>
