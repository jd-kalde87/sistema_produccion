<?php
session_start();
include '../db.php';
include 'funciones.php';



// 1. Validar sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanitizar el dato
    $tamaño = strtoupper(trim($conn->real_escape_string($_POST['tamaño_pieza'])));

    // 3. Verificar si el tamaño ya existe
    $existe = false;
    $sql_check = "SELECT id_tamaño_pieza FROM tamaño_pieza WHERE tamaño_pieza = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $tamaño);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $_SESSION['error'] = "El tamaño '$tamaño' ya está registrado";
        $existe = true;
    }
    $stmt_check->close();

    // 4. Si no existe, insertarlo
    if (!$existe) {
        $sql_insert = "INSERT INTO tamaño_pieza (tamaño_pieza) VALUES (?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $tamaño);
        
        if ($stmt_insert->execute()) {
            $_SESSION['success'] = "Tamaño '$tamaño' registrado correctamente";
            // Actualizar la lista en sesión
            $_SESSION['tamaño'] = obtenerTamaños($conn);
        } else {
            $_SESSION['error'] = "Error al registrar: " . $conn->error;
        }
        $stmt_insert->close();
    }
}

// 5. Redireccionar
header("Location: ../admin_piezas.php");
exit;
?>