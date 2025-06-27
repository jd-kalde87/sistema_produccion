<?php
require 'db.php';
header('Content-Type: application/json');

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Fechas no proporcionadas.']);
    exit;
}

$response = [
    'puntadasData' => [],
    'unidadesData' => []
];

// --- 1. CONSULTA PARA PUNTADAS POR MÁQUINA (CON JOIN Y CONCAT) ---
$sql_puntadas = "
    SELECT
        CONCAT(m.marca_maquina, ' - Nro cabezas - ', m.nro_cabezas) as maquina_etiqueta,
        SUM(rd.total_puntadas) as total_puntadas
    FROM
        registro_diario rd
    INNER JOIN 
        maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE
        DATE(rd.marca_temporal) BETWEEN ? AND ?
    GROUP BY
        maquina_etiqueta
    ORDER BY
        total_puntadas DESC;
";

$stmt_puntadas = $conn->prepare($sql_puntadas);
$stmt_puntadas->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_puntadas->execute();
$resultado_puntadas = $stmt_puntadas->get_result();
if ($resultado_puntadas) {
    $response['puntadasData'] = $resultado_puntadas->fetch_all(MYSQLI_ASSOC);
}
$stmt_puntadas->close();


// --- 2. CONSULTA PARA UNIDADES POR MÁQUINA (CON JOIN Y CONCAT) ---
$sql_unidades = "
    SELECT
        CONCAT(m.marca_maquina, ' - Nro cabezas - ', m.nro_cabezas) as maquina_etiqueta,
        SUM(rd.cantidad_unidades) as total_unidades
    FROM
        registro_diario rd
    INNER JOIN 
        maquinas m ON rd.maquina_operada = m.id_maquina
    WHERE
        DATE(rd.marca_temporal) BETWEEN ? AND ?
    GROUP BY
        maquina_etiqueta
    ORDER BY
        total_unidades DESC;
";

$stmt_unidades = $conn->prepare($sql_unidades);
$stmt_unidades->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt_unidades->execute();
$resultado_unidades = $stmt_unidades->get_result();
if ($resultado_unidades) {
    $response['unidadesData'] = $resultado_unidades->fetch_all(MYSQLI_ASSOC);
}
$stmt_unidades->close();

// =================================================================
// --- FIN DE LA MODIFICACIÓN ---
// =================================================================

$conn->close();

echo json_encode($response);
?>