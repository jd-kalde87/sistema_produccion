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
    $id_bordado = intval($_POST['id_bordado']);

    // Eliminar
    $sql = "DELETE FROM tipo_bordado WHERE id_bordado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_bordado);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Tipo de bordado eliminado";
        $_SESSION['bordados'] = obtenerTiposBordado($conn); // Actualizar lista
    } else {
        $_SESSION['error'] = "Error al eliminar: " . $conn->error;
    }
}

header("Location: ../admin_bordados.php");
exit;
?>