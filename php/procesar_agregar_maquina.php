<?php
session_start();
include '../db.php';
include 'funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = strtoupper(trim($conn->real_escape_string($_POST['marca'])));
    $cabezas = intval($_POST['cabezas']);

    // Validaciones
    if (empty($marca) || $cabezas <= 0) {
        $_SESSION['error'] = "Todos los campos son obligatorios y el número de cabezas debe ser mayor a 0";
        header("Location: ../agregar_maquinaria.php");
        exit;
    }

    // Verificar si la máquina ya existe
    $sql_check = "SELECT id_maquina FROM maquinas WHERE marca_maquina = ? AND nro_cabezas = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $marca, $cabezas);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "La máquina ya existe";
    } else {
        // Insertar nueva máquina
        $sql = "INSERT INTO maquinas (marca_maquina, nro_cabezas) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $marca, $cabezas);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Máquina agregada correctamente";
            // Actualizar lista en sesión
            $_SESSION['maquinas'] = obtenerMaquinas($conn);
        } else {
            $_SESSION['error'] = "Error al agregar la máquina";
        }
    }
}

header("Location: ../agregar_maquinaria.php");
?>