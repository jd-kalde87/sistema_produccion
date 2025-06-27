<?php
require 'db.php'; // Tu conexión a la BD
header('Content-Type: application/json');

// 1. Validar las fechas de entrada
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo json_encode(['status' => 'error', 'message' => 'Por favor, proporciona ambas fechas.']);
    exit;
}

// 2. Preparar y ejecutar la consulta SQL
// Esta consulta suma las unidades producidas, las agrupa por colaborador,
// las ordena de mayor a menor y se queda solo con el primer resultado (LIMIT 1).
$sql = "
    SELECT
        colaborador,
        SUM(cantidad_unidades) as total_unidades
    FROM
        registro_diario
    WHERE
        DATE(marca_temporal) BETWEEN ? AND ?
    GROUP BY
        colaborador
    ORDER BY
        total_unidades DESC
    LIMIT 1;
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error al preparar la consulta.']);
    exit;
}

$stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
$stmt->execute();
$resultado = $stmt->get_result();

// 3. Devolver el resultado en formato JSON
if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    echo json_encode(['status' => 'success', 'data' => $fila]);
} else {
    echo json_encode(['status' => 'no_data', 'message' => 'No se encontraron registros de producción en el rango de fechas seleccionado.']);
}

$stmt->close();
$conn->close();
?>