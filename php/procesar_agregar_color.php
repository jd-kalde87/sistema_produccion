<?php
session_start();
require '../db.php';
require 'funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_color = strtoupper(trim($conn->real_escape_string($_POST['codigo_color'])));
    $descripcion_color = trim($conn->real_escape_string($_POST['descripcion_color']));

    // Verificar si el código de color ya existe
    $sql_check = "SELECT id_color FROM color WHERE codigo_color = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $codigo_color);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "El código de color '$codigo_color' ya está registrado.";
    } else {
        // Insertar nuevo color
        $sql = "INSERT INTO color (codigo_color, descripcion_color) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $codigo_color, $descripcion_color);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Color registrado correctamente.";
            $_SESSION['colores'] = obtenerColores($conn); // Actualizar lista en sesión
        } else {
            $_SESSION['error'] = "Error al registrar el color: " . $conn->error;
        }
    }
}

header("Location: ../admin_colores.php");
exit;
?>