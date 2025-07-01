<?php
require 'db.php';
header('Content-Type: application/json');

// --- Parámetros de entrada ---
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_maquina = $_GET['id_maquina'] ?? 'todas';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']);
    exit;
}

// --- Preparar filtro condicional ---
$filtro_maquina_sql = "";
$params = [$fecha_inicio, $fecha_fin];
$types = "ss";
if ($id_maquina !== 'todas') {
    $filtro_maquina_sql = " AND rd.maquina_operada = ? ";
    $params[] = $id_maquina;
    $types .= "i";
}

$response = [
    'rankingData' => [],
    'monthlyData' => [],
    'chartData' => ['labels' => [], 'datasets' => []]
];

// --- 1. CONSULTA PARA EL RANKING ---
$sql_ranking = "
    SELECT CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta, SUM(rd.total_puntadas) as total_puntadas
    FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql
    GROUP BY maquina_etiqueta ORDER BY total_puntadas DESC;
";
$stmt = $conn->prepare($sql_ranking);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado) $response['rankingData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- 2. CONSULTA PARA DESGLOSE MENSUAL (PIVOT) ---
$sql_monthly = "
    SELECT CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 1 THEN rd.total_puntadas ELSE 0 END) as Ene,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 2 THEN rd.total_puntadas ELSE 0 END) as Feb,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 3 THEN rd.total_puntadas ELSE 0 END) as Mar,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 4 THEN rd.total_puntadas ELSE 0 END) as Abr,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 5 THEN rd.total_puntadas ELSE 0 END) as May,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 6 THEN rd.total_puntadas ELSE 0 END) as Jun,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 7 THEN rd.total_puntadas ELSE 0 END) as Jul,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 8 THEN rd.total_puntadas ELSE 0 END) as Ago,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 9 THEN rd.total_puntadas ELSE 0 END) as Sep,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 10 THEN rd.total_puntadas ELSE 0 END) as Oct,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 11 THEN rd.total_puntadas ELSE 0 END) as Nov,
        SUM(CASE WHEN MONTH(rd.marca_temporal) = 12 THEN rd.total_puntadas ELSE 0 END) as Dic,
        SUM(rd.total_puntadas) as total_general
    FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql
    GROUP BY maquina_etiqueta ORDER BY total_general DESC;
";
$stmt = $conn->prepare($sql_monthly);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado) $response['monthlyData'] = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- 3. CONSULTA PARA EL GRÁFICO DE LÍNEAS (DATOS DIARIOS) ---
$sql_chart = "
    SELECT DATE(rd.marca_temporal) as dia, CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta, SUM(rd.total_puntadas) as total_puntadas
    FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql
    GROUP BY dia, maquina_etiqueta ORDER BY dia, maquina_etiqueta ASC;
";
$stmt = $conn->prepare($sql_chart);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();
$rawData = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- Procesamiento de datos para el gráfico ---
if (!empty($rawData)) {
    $labels = []; $maquinas_chart = []; $processedData = [];
    foreach ($rawData as $row) {
        if (!in_array($row['dia'], $labels)) $labels[] = $row['dia'];
        if (!isset($processedData[$row['maquina_etiqueta']])) {
            $maquinas_chart[] = $row['maquina_etiqueta'];
            $processedData[$row['maquina_etiqueta']] = [];
        }
        $processedData[$row['maquina_etiqueta']][$row['dia']] = (int)$row['total_puntadas'];
    }
    sort($labels);
    foreach ($maquinas_chart as $maq) {
        $dataPoints = [];
        foreach ($labels as $dia) { $dataPoints[] = $processedData[$maq][$dia] ?? 0; }
        $response['chartData']['datasets'][] = ['label' => $maq, 'data' => $dataPoints];
    }
    $response['chartData']['labels'] = array_map(fn($date) => date('d M', strtotime($date)), $labels);
}

$conn->close();
echo json_encode($response);
?>