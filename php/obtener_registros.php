<?php
include 'db.php';
include 'funciones.php';

$resultado = null;
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

// Si se proporcionan ambas fechas, se ejecuta la consulta
if ($fecha_inicio && $fecha_fin) {
    // Obtiene el resultado de una consulta con innerjoin y filtro de fecha
    $resultado = obtenerRegistrosConJoin($conn, $fecha_inicio, $fecha_fin);
}


// Consulta para tamaños
$_SESSION['tamaño'] = $conn->query("SELECT * FROM tamaño_pieza")->fetch_all(MYSQLI_ASSOC);

// Consulta para máquinas
$_SESSION['maquinas'] = $conn->query("SELECT * FROM maquinas")->fetch_all(MYSQLI_ASSOC);

?>