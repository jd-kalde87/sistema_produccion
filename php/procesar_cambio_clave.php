<?php
session_start();
include '../db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar_clave = $_POST['confirmar_clave'];
    $id_usuario = $_SESSION['id_usuario'];

    if (empty($nueva_clave) || empty($confirmar_clave)) {
        header("Location: ../cambiar_clave.php?error=Ambos campos son requeridos.");
        exit();
    }
    
    if ($nueva_clave !== $confirmar_clave) {
        header("Location: ../cambiar_clave.php?error=Las contraseñas no coinciden.");
        exit();
    }

    // (Opcional) Añadir validación de complejidad de la contraseña aquí

    $clave_hasheada = password_hash($nueva_clave, PASSWORD_DEFAULT);

    // Actualizar la contraseña y la bandera `primer_ingreso`
    $sql = "UPDATE usuarios SET password = ?, primer_ingreso = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $clave_hasheada, $id_usuario);

    if ($stmt->execute()) {
        // Actualizar la variable de sesión y redirigir a inicio
        $_SESSION['primer_ingreso'] = 0;
        header("Location: ../inicio.php?clave_actualizada=1");
    } else {
        header("Location: ../cambiar_clave.php?error=Error al actualizar la contraseña.");
    }
    
    $stmt->close();
    $conn->close();
}
?>