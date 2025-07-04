<?php
require 'db.php';
header('Content-Type: application/json');

// Modo de depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    exit(json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']));
}

$response = [
    'turnoData' => [],
    'bordadoData' => [],
    'colorData' => [],
    'tamanoData' => []
];
$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

// --- CONSULTAS CORREGIDAS CON LOS INNER JOIN ESPECÍFICOS ---

// 1. Producción por Turno
$sql_turno = "
    SELECT 
        jl.tipo_jornada, 
        SUM(rd.cantidad_unidades) as total 
    FROM registro_diario rd
    INNER JOIN jornada_laboral jl ON rd.turno = jl.id_jornada
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? 
    GROUP BY jl.tipo_jornada 
    ORDER BY total DESC";
$stmt = $conn->prepare($sql_turno);
$stmt->bind_param($types, ...$params);
$stmt->execute();
if ($resultado = $stmt->get_result()) $response['turnoData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2. Producción por Tipo de Bordado
$sql_bordado = "
    SELECT 
        tb.tipo_bordado, 
        SUM(rd.cantidad_unidades) as total 
    FROM registro_diario rd
    INNER JOIN tipo_bordado tb ON rd.tipo_bordado = tb.id_bordado
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? 
    GROUP BY tb.tipo_bordado 
    ORDER BY total DESC";
$stmt = $conn->prepare($sql_bordado);
$stmt->bind_param($types, ...$params);
$stmt->execute();
if ($resultado = $stmt->get_result()) $response['bordadoData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3. Producción por Color
$sql_color = "
    SELECT 
        c.codigo_color, 
        SUM(rd.cantidad_unidades) as total 
    FROM registro_diario rd
    INNER JOIN color c ON rd.color_realizado = c.id_color
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? 
    GROUP BY c.codigo_color 
    ORDER BY total DESC";
$stmt = $conn->prepare($sql_color);
$stmt->bind_param($types, ...$params);
$stmt->execute();
if ($resultado = $stmt->get_result()) $response['colorData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 4. Producción por Tamaño de Pieza
$sql_tamano = "
    SELECT 
        tp.tamaño_pieza, 
        SUM(rd.cantidad_unidades) as total 
    FROM registro_diario rd
    INNER JOIN tamaño_pieza tp ON rd.tamaño_pieza = tp.id_tamaño_pieza
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? 
    GROUP BY tp.tamaño_pieza 
    ORDER BY total DESC";
$stmt = $conn->prepare($sql_tamano);
$stmt->bind_param($types, ...$params);
$stmt->execute();
if ($resultado = $stmt->get_result()) $response['tamanoData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
echo json_encode($response);
?>