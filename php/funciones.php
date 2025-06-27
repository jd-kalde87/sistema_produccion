<?php 

////Consultas para listas desplegables
function obtenerOpciones($conn, $tabla, $campo_id, $campo_nombre) {
    $opciones = "";
    $sql = "SELECT $campo_id, $campo_nombre FROM $tabla ORDER BY $campo_nombre";
    $resultado = $conn->query($sql);
    while ($row = $resultado->fetch_assoc()) {
        $opciones .= "<option value='" . $row[$campo_id] . "'>" . strtoupper($row[$campo_nombre]) . "</option>";
    }
    return $opciones;
}

function obtenerOpciones2($conn, $tabla, $campo_id, $campo_nombre, $campo_numero) {
    $opciones = "";
    $sql = "SELECT $campo_id, $campo_nombre, $campo_numero FROM $tabla ORDER BY $campo_nombre";
    $resultado = $conn->query($sql);
    while ($row = $resultado->fetch_assoc()) {
        $opciones .= "<option value='" . $row[$campo_id] . "'>" . strtoupper($row[$campo_nombre]." - Nro de cabezas"." - ".$row[$campo_numero] ) . "</option>";
    }
    return $opciones;
}

function obtenerOpciones3($conn, $tabla, $campo_id, $campo_nombre, $campo_numero) {
    $opciones = "";
    $sql = "SELECT $campo_id, $campo_nombre, $campo_numero FROM $tabla ORDER BY $campo_id";
    $resultado = $conn->query($sql);
    while ($row = $resultado->fetch_assoc()) {
        $opciones .= "<option value='" . $row[$campo_id] . "'>" . strtoupper($row[$campo_nombre]." - ".$row[$campo_numero] ) . "</option>";
    }
    return $opciones;
}

//// Consulta para obtener máquinas
function obtenerMaquinas($conn) {
    $sql = "SELECT * FROM maquinas ORDER BY marca_maquina";
    $resultado = $conn->query($sql);
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

//// Consulta para obtener tamaños de pieza
function obtenerTamaños($conn) {
    $sql = "SELECT * FROM tamaño_pieza ORDER BY tamaño_pieza";
    $resultado = $conn->query($sql);
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

//// Generar la consulta para tablas
function generarTabla($datos, $columnas, $mensajeVacio = 'No hay registros.') {
    if (empty($datos)) {
        return "<p class='alert alert-info'>$mensajeVacio</p>";
    }

    ob_start(); // Inicia buffer
    ?>
    <table class="tabla-registro">
        <thead>
            <tr>
                <?php foreach ($columnas as $titulo): ?>
                    <th><?= htmlspecialchars($titulo) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $fila): ?>
                <tr>
                    <?php foreach (array_keys($columnas) as $campo): ?>
                        <td><?= htmlspecialchars($fila[$campo] ?? '') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean(); // Retorna el HTML
}

/// Generar opciones para select de máquinas
function generarOpcionesMaquinas($maquinas) {
    $opciones = '';
    foreach ($maquinas as $maquina) {
        $opciones .= "<option value='{$maquina['id_maquina']}'>";
        $opciones .= htmlspecialchars($maquina['marca_maquina']) . " - {$maquina['nro_cabezas']} cabezas";
        $opciones .= "</option>";
    }
    return $opciones;
}

//// Consulta para tipos de bordado
function obtenerTiposBordado($conn) {
    $sql = "SELECT * FROM tipo_bordado ORDER BY tipo_bordado";
    $resultado = $conn->query($sql);
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

//// Consulta para obtener colores
function obtenerColores($conn) {
    $sql = "SELECT * FROM color ORDER BY codigo_color ASC";
    $resultado = $conn->query($sql);
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

function obtenerRegistrosConJoin($conn, $fecha_inicio = null, $fecha_fin = null) {
    $sql = "SELECT
        r.marca_temporal,
        r.colaborador,
        m.marca_maquina,
        m.nro_cabezas,
        j.tipo_jornada,
        r.orden_produccion,
        r.referencia,
        b.tipo_bordado,
        t.tamaño_pieza,
        r.puntadas_diseño,
        r.cantidad_unidades,
        r.total_puntadas,
        c.codigo_color,
        r.observaciones
    FROM registro_diario r
    INNER JOIN maquinas m ON r.maquina_operada = m.id_maquina
    INNER JOIN jornada_laboral j ON r.turno = j.id_jornada
    INNER JOIN tipo_bordado b ON r.tipo_bordado = b.id_bordado
    INNER JOIN tamaño_pieza t ON r.tamaño_pieza = t.id_tamaño_pieza
    INNER JOIN color c ON r.color_realizado = c.id_color";

    if ($fecha_inicio && $fecha_fin) {
        $sql .= " WHERE DATE(r.marca_temporal) BETWEEN ? AND ?";
    }

    $sql .= " ORDER BY r.marca_temporal DESC";

    $stmt = $conn->prepare($sql);

    if ($fecha_inicio && $fecha_fin) {
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    }

    $stmt->execute();
    return $stmt->get_result();
}
?>