<?php
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger los 3 datos del formulario
    $email = $conn->real_escape_string(trim($_POST['email']));
    $numero_identificacion = $conn->real_escape_string(trim($_POST['numero_identificacion']));
    $fecha_nacimiento = $conn->real_escape_string(trim($_POST['fecha_nacimiento']));

    // 2. Buscar un usuario que coincida EXACTAMENTE con los 3 datos
    $sql = "SELECT id FROM usuarios WHERE email = ? AND numero_identificacion = ? AND fecha_nacimiento = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $numero_identificacion, $fecha_nacimiento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // ¡Coincidencia encontrada!
        $usuario = $result->fetch_assoc();
        $id_usuario = $usuario['id'];

        // 3. Restablecer la contraseña al número de identificación
        $nueva_clave = $numero_identificacion;
        $clave_hasheada = password_hash($nueva_clave, PASSWORD_DEFAULT);

        // 4. Actualizar la contraseña y forzar el cambio en el próximo login (primer_ingreso = 1)
        $sql_update = "UPDATE usuarios SET password = ?, primer_ingreso = 1 WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $clave_hasheada, $id_usuario);
        
        if ($stmt_update->execute()) {
            // Redirigir al login con mensaje de éxito
            header("Location: ../login.php?reset=exitoso");
            exit();
        } else {
            header("Location: ../recuperar.php?error=Ocurrió un error al actualizar la contraseña.");
            exit();
        }
        $stmt_update->close();

    } else {
        // No se encontró ningún usuario que coincida con los 3 datos
        header("Location: ../recuperar.php?error=Los datos proporcionados no coinciden con nuestros registros.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>