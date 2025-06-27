<?php
require 'db.php';
header('Content-Type: application/json');

// Recibimos los tres parámetros del formulario
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_maquina = $_GET['id_maquina'] ?? 'todas'; // Nuevo parámetro

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']);
    exit;
}

// 1. Construcción dinámica de la consulta SQL
$sql_raw = "
    SELECT 
        DATE(rd.marca_temporal) as dia,
        CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta,
        SUM(rd.total_puntadas) as total_puntadas
    FROM
        registro_diario rd
    INNER JOIN 
        maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE
        DATE(rd.marca_temporal) BETWEEN ? AND ?
";

// Array para los parámetros del bind_param
$params = [$fecha_inicio, $fecha_fin];
$types = "ss"; // Tipos de datos para las fechas (string, string)

// Si el usuario seleccionó una máquina específica, añadimos el filtro
if ($id_maquina !== 'todas' && is_numeric($id_maquina)) {
    $sql_raw .= " AND rd.maquina_operada = ? ";
    $params[] = $id_maquina; // Añadimos el ID de la máquina a los parámetros
    $types .= "i"; // Añadimos el tipo de dato (integer)
}

$sql_raw .= "
    GROUP BY
        dia, maquina_etiqueta
    ORDER BY
        dia, maquina_etiqueta ASC;
";

$stmt = $conn->prepare($sql_raw);
// Usamos el operador "splat" (...) para pasar los parámetros dinámicamente
$stmt->bind_param($types, ...$params); 
$stmt->execute();
$resultado = $stmt->get_result();
$rawData = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

if (empty($rawData)) {
    echo json_encode(['labels' => [], 'datasets' => []]);
    exit;
}

// El resto del código que procesa los datos no necesita cambios
$labels = [];       
$maquinas = [];
$processedData = [];

foreach ($rawData as $row) {
    if (!in_array($row['dia'], $labels)) {
        $labels[] = $row['dia'];
    }
    if (!isset($processedData[$row['maquina_etiqueta']])) {
        $maquinas[] = $row['maquina_etiqueta'];
        $processedData[$row['maquina_etiqueta']] = [];
    }
    $processedData[$row['maquina_etiqueta']][$row['dia']] = (int)$row['total_puntadas'];
}
sort($labels);

$datasets = [];
foreach ($maquinas as $maquina) {
    $dataPoints = [];
    foreach ($labels as $dia) {
        $dataPoints[] = $processedData[$maquina][$dia] ?? 0;
    }
    $datasets[] = [
        'label' => $maquina,
        'data' => $dataPoints
    ];
}

$formattedLabels = array_map(function($date) {
    return date('d M', strtotime($date));
}, $labels);

echo json_encode([
    'labels' => $formattedLabels,
    'datasets' => $datasets
]);
?>