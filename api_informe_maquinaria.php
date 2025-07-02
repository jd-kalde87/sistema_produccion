<?php
require 'db.php';
header('Content-Type: application/json');

// --- Parámetros de entrada ---
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_maquina = $_GET['id_maquina'] ?? 'todas';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    exit(json_encode(['status' => 'error']));
}

// --- Preparar filtro condicional ---
$filtro_maquina_sql = "";
$params = [$fecha_inicio, $fecha_fin];
$types = "ss";
if ($id_maquina !== 'todas' && is_numeric($id_maquina)) {
    $filtro_maquina_sql = " AND rd.maquina_operada = ? ";
    $params[] = $id_maquina;
    $types .= "i";
}

$response = [
    'rankingUnidades' => [],
    'monthlyUnidades' => [],
    'lineChartData' => ['labels' => [], 'datasets' => []]
];

// --- 1. Ranking de Unidades (sin cambios) ---
$sql_rank_unidades = "SELECT CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta, SUM(rd.cantidad_unidades) as total_unidades FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql GROUP BY maquina_etiqueta ORDER BY total_unidades DESC;";
$stmt1 = $conn->prepare($sql_rank_unidades);
if ($stmt1) {
    $stmt1->bind_param($types, ...$params);
    $stmt1->execute();
    if ($resultado = $stmt1->get_result()) $response['rankingUnidades'] = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt1->close();
}

// --- 2. Desglose Mensual de Unidades (sin cambios) ---
$sql_monthly_unidades = "SELECT CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta, SUM(CASE WHEN MONTH(rd.marca_temporal) = 1 THEN rd.cantidad_unidades ELSE 0 END) as Ene, SUM(CASE WHEN MONTH(rd.marca_temporal) = 2 THEN rd.cantidad_unidades ELSE 0 END) as Feb, SUM(CASE WHEN MONTH(rd.marca_temporal) = 3 THEN rd.cantidad_unidades ELSE 0 END) as Mar, SUM(CASE WHEN MONTH(rd.marca_temporal) = 4 THEN rd.cantidad_unidades ELSE 0 END) as Abr, SUM(CASE WHEN MONTH(rd.marca_temporal) = 5 THEN rd.cantidad_unidades ELSE 0 END) as May, SUM(CASE WHEN MONTH(rd.marca_temporal) = 6 THEN rd.cantidad_unidades ELSE 0 END) as Jun, SUM(CASE WHEN MONTH(rd.marca_temporal) = 7 THEN rd.cantidad_unidades ELSE 0 END) as Jul, SUM(CASE WHEN MONTH(rd.marca_temporal) = 8 THEN rd.cantidad_unidades ELSE 0 END) as Ago, SUM(CASE WHEN MONTH(rd.marca_temporal) = 9 THEN rd.cantidad_unidades ELSE 0 END) as Sep, SUM(CASE WHEN MONTH(rd.marca_temporal) = 10 THEN rd.cantidad_unidades ELSE 0 END) as Oct, SUM(CASE WHEN MONTH(rd.marca_temporal) = 11 THEN rd.cantidad_unidades ELSE 0 END) as Nov, SUM(CASE WHEN MONTH(rd.marca_temporal) = 12 THEN rd.cantidad_unidades ELSE 0 END) as Dic, SUM(rd.cantidad_unidades) as total_general FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql GROUP BY maquina_etiqueta ORDER BY total_general DESC;";
$stmt2 = $conn->prepare($sql_monthly_unidades);
if ($stmt2) {
    $stmt2->bind_param($types, ...$params);
    $stmt2->execute();
    if ($resultado = $stmt2->get_result()) $response['monthlyUnidades'] = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}

// =================================================================
// ===== INICIO DE LA MODIFICACIÓN =====
// =================================================================

// --- 3. CONSULTA PARA EL GRÁFICO DE LÍNEAS (DATOS DIARIOS) ---
$sql_chart = "
    SELECT DATE(rd.marca_temporal) as dia, CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta, SUM(rd.cantidad_unidades) as unidades_dia
    FROM registro_diario rd INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ? $filtro_maquina_sql
    GROUP BY dia, maquina_etiqueta ORDER BY dia, maquina_etiqueta ASC;
";
$stmt3 = $conn->prepare($sql_chart);
if ($stmt3) {
    $stmt3->bind_param($types, ...$params);
    $stmt3->execute();
    $resultado = $stmt3->get_result();
    $rawData = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();

    // --- Procesamiento de datos para el gráfico (lógica de tiempo dinámica) ---
    if (!empty($rawData)) {
        $labels = []; $maquinas_chart = []; $processedData = [];
        foreach ($rawData as $row) {
            // Se crea una lista de etiquetas solo con los días que tuvieron producción
            if (!in_array($row['dia'], $labels)) $labels[] = $row['dia'];
            if (!isset($processedData[$row['maquina_etiqueta']])) {
                $maquinas_chart[] = $row['maquina_etiqueta'];
                $processedData[$row['maquina_etiqueta']] = [];
            }
            $processedData[$row['maquina_etiqueta']][$row['dia']] = (int)$row['unidades_dia'];
        }
        sort($labels); // Se ordenan los días cronológicamente

        // Se construye cada línea del gráfico
        foreach ($maquinas_chart as $maq) {
            $dataPoints = [];
            foreach ($labels as $dia) { $dataPoints[] = $processedData[$maq][$dia] ?? 0; }
            $response['lineChartData']['datasets'][] = ['label' => $maq, 'data' => $dataPoints];
        }
        // Se formatean las etiquetas para que sean legibles
        $response['lineChartData']['labels'] = array_map(fn($date) => date('d M', strtotime($date)), $labels);
    }
}
// =================================================================
// ===== FIN DE LA MODIFICACIÓN =====
// =================================================================

$conn->close();
echo json_encode($response);
?>