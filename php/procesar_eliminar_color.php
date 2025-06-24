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
    $id_color = intval($_POST['id_color']);

    // Eliminar
    $sql = "DELETE FROM color WHERE id_color = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_color);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Color eliminado correctamente.";
            $_SESSION['colores'] = obtenerColores($conn); // Actualizar lista en sesión
        } else {
            $_SESSION['error'] = "No se encontró el color seleccionado.";
        }
    } else {
        // Error común: el color está siendo usado en otra tabla (llave foránea)
        if ($conn->errno == 1451) {
            $_SESSION['error'] = "Error: Este color no se puede eliminar porque está asociado a registros de producción.";
        } else {
            $_SESSION['error'] = "Error al eliminar el color: " . $conn->error;
        }
    }
}

header("Location: ../admin_colores.php");
exit;
?>