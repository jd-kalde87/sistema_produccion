<?php
require 'db.php';
header('Content-Type: application/json');

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_maquina = $_GET['id_maquina'] ?? 'todas'; // Nuevo filtro

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']);
    exit;
}

$response = [
    'puntadasData' => [],
    'unidadesData' => []
];

// --- 1. CONSULTA PARA PUNTADAS POR MÁQUINA (con filtro condicional) ---
$sql_puntadas = "
    SELECT
        CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta,
        SUM(rd.total_puntadas) as total_puntadas
    FROM registro_diario rd
    INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ?
";
$params = [$fecha_inicio, $fecha_fin];
$types = "ss";

if ($id_maquina !== 'todas') {
    $sql_puntadas .= " AND rd.maquina_operada = ? ";
    $params[] = $id_maquina;
    $types .= "i";
}
$sql_puntadas .= " GROUP BY maquina_etiqueta ORDER BY total_puntadas DESC;";

$stmt_puntadas = $conn->prepare($sql_puntadas);
$stmt_puntadas->bind_param($types, ...$params);
$stmt_puntadas->execute();
$resultado_puntadas = $stmt_puntadas->get_result();
if ($resultado_puntadas) {
    $response['puntadasData'] = $resultado_puntadas->fetch_all(MYSQLI_ASSOC);
}
$stmt_puntadas->close();

// --- 2. CONSULTA PARA UNIDADES POR MÁQUINA (con filtro condicional) ---
$sql_unidades = "
    SELECT
        CONCAT(m.marca_maquina, ' - ', m.nro_cabezas, ' cabezas') as maquina_etiqueta,
        SUM(rd.cantidad_unidades) as total_unidades
    FROM registro_diario rd
    INNER JOIN maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE DATE(rd.marca_temporal) BETWEEN ? AND ?
";
// Los parámetros y tipos son los mismos que para la consulta anterior
if ($id_maquina !== 'todas') {
    $sql_unidades .= " AND rd.maquina_operada = ? ";
}
$sql_unidades .= " GROUP BY maquina_etiqueta ORDER BY total_unidades DESC;";

$stmt_unidades = $conn->prepare($sql_unidades);
$stmt_unidades->bind_param($types, ...$params);
$stmt_unidades->execute();
$resultado_unidades = $stmt_unidades->get_result();
if ($resultado_unidades) {
    $response['unidadesData'] = $resultado_unidades->fetch_all(MYSQLI_ASSOC);
}
$stmt_unidades->close();

$conn->close();
echo json_encode($response);
?>