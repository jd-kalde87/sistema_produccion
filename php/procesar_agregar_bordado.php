<?php
session_start();
include '../db.php';
include 'funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_bordado = strtoupper(trim($conn->real_escape_string($_POST['tipo_bordado'])));

    // Verificar si ya existe
    $sql_check = "SELECT id_bordado FROM tipo_bordado WHERE tipo_bordado = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $tipo_bordado);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Este tipo de bordado ya existe";
    } else {
        // Insertar nuevo
        $sql = "INSERT INTO tipo_bordado (tipo_bordado) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $tipo_bordado);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Tipo de bordado registrado";
            $_SESSION['bordados'] = obtenerTiposBordado($conn); // Actualizar lista
        } else {
            $_SESSION['error'] = "Error al registrar: " . $conn->error;
        }
    }
}

header("Location: ../admin_bordados.php");
exit;
?>