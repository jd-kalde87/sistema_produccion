<?php
session_start();

// 1. Incluir la conexión a la base de datos
include '../db.php';

// 2. Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 3. Procesar la solicitud solo si es por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que se recibió un ID
    if (!isset($_POST['id_tamaño_pieza']) || empty($_POST['id_tamaño_pieza'])) {
        $_SESSION['error'] = "No se seleccionó ningún tamaño para eliminar.";
        header("Location: ../admin_piezas.php");
        exit;
    }

    $id_tamaño = $_POST['id_tamaño_pieza'];

    // 4. Preparar y ejecutar la consulta de eliminación de forma segura
    $sql = "DELETE FROM tamaño_pieza WHERE id_tamaño_pieza = ?";
    $stmt = $conn->prepare($sql);
    
    // Vincular el parámetro (i para entero)
    $stmt->bind_param("i", $id_tamaño);

    if ($stmt->execute()) {
        // Verificar si alguna fila fue realmente eliminada
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Tamaño eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se encontró el tamaño seleccionado o ya fue eliminado.";
        }
    } else {
        // Manejo de errores específicos (ej. restricción de clave foránea)
        if ($conn->errno == 1451) {
             $_SESSION['error'] = "Error: No se puede eliminar el tamaño porque está siendo utilizado en otros registros.";
        } else {
             $_SESSION['error'] = "Error al eliminar el tamaño: " . $conn->error;
        }
    }

    // Cerrar el statement
    $stmt->close();
}

// 5. Cerrar la conexión y redirigir
$conn->close();
header("Location: ../admin_piezas.php");
exit;
?>