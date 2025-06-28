<?php
require 'db.php';
header('Content-Type: application/json');

// Recibimos los tres parámetros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$colaborador_filtro = $_GET['colaborador'] ?? 'todos'; // Nuevo parámetro

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']);
    exit;
}

$response = [
    'rankingData' => [],
    'chartData' => ['labels' => [], 'datasets' => []]
];

// --- 1. CONSULTA PARA EL RANKING (modificada) ---
// Se quitó la suma de puntadas y se añadió el filtro condicional por colaborador.
$sql_ranking = "
    SELECT
        colaborador,
        SUM(cantidad_unidades) as total_unidades
    FROM
        registro_diario
    WHERE
        DATE(marca_temporal) BETWEEN ? AND ?
";
$params_ranking = [$fecha_inicio, $fecha_fin];
$types_ranking = "ss";

if ($colaborador_filtro !== 'todos') {
    $sql_ranking .= " AND colaborador = ? ";
    $params_ranking[] = $colaborador_filtro;
    $types_ranking .= "s";
}
$sql_ranking .= " GROUP BY colaborador ORDER BY total_unidades DESC;";

$stmt_ranking = $conn->prepare($sql_ranking);
$stmt_ranking->bind_param($types_ranking, ...$params_ranking);
$stmt_ranking->execute();
$resultado_ranking = $stmt_ranking->get_result();
if ($resultado_ranking) {
    $response['rankingData'] = $resultado_ranking->fetch_all(MYSQLI_ASSOC);
}
$stmt_ranking->close();

// --- 2. CONSULTA PARA EL GRÁFICO (modificada) ---
// También se le añade el filtro condicional por colaborador.
$sql_chart = "
    SELECT 
        DATE(marca_temporal) as dia,
        colaborador,
        SUM(cantidad_unidades) as unidades_dia
    FROM
        registro_diario
    WHERE
        DATE(marca_temporal) BETWEEN ? AND ?
";
$params_chart = [$fecha_inicio, $fecha_fin];
$types_chart = "ss";

if ($colaborador_filtro !== 'todos') {
    $sql_chart .= " AND colaborador = ? ";
    $params_chart[] = $colaborador_filtro;
    $types_chart .= "s";
}
$sql_chart .= " GROUP BY dia, colaborador ORDER BY dia, colaborador ASC;";

$stmt_chart = $conn->prepare($sql_chart);
$stmt_chart->bind_param($types_chart, ...$params_chart);
$stmt_chart->execute();
$resultado_chart = $stmt_chart->get_result();
$rawData = $resultado_chart->fetch_all(MYSQLI_ASSOC);
$stmt_chart->close();

// El resto del código para procesar los datos del gráfico no cambia...
if (!empty($rawData)) {
    $labels = []; $colaboradores_chart = []; $processedData = [];
    foreach ($rawData as $row) {
        if (!in_array($row['dia'], $labels)) $labels[] = $row['dia'];
        if (!isset($processedData[$row['colaborador']])) {
            $colaboradores_chart[] = $row['colaborador'];
            $processedData[$row['colaborador']] = [];
        }
        $processedData[$row['colaborador']][$row['dia']] = (int)$row['unidades_dia'];
    }
    sort($labels);
    foreach ($colaboradores_chart as $col) {
        $dataPoints = [];
        foreach ($labels as $dia) {
            $dataPoints[] = $processedData[$col][$dia] ?? 0;
        }
        $response['chartData']['datasets'][] = ['label' => $col, 'data' => $dataPoints];
    }
    $response['chartData']['labels'] = array_map(fn($date) => date('d M', strtotime($date)), $labels);
}

$conn->close();
echo json_encode($response);
?>