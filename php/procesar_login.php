<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = trim($_POST['password']);

    // Buscar usuario por email
    $sql = "SELECT id, primer_nombre, primer_apellido, numero_identificacion, password, primer_ingreso FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $usuario['password'])) {
            // Iniciar sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['identificacion'] = $usuario['numero_identificacion'];
            $_SESSION['primer_nombre'] = $usuario['primer_nombre'];
            $_SESSION['primer_apellido'] = $usuario['primer_apellido'];
            $_SESSION['nombre_usuario'] = $usuario['primer_nombre'] . ' ' . $usuario['primer_apellido'];
            $_SESSION['marca_temporal'] = date('Y-m-d H:i:s');

            // Redirigir según si es el primer ingreso
            if ($usuario['primer_ingreso'] == 1) {
                header("Location: ../cambiar_clave.php");
            } else {
                header("Location: ../inicio.php");
            }
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: ../login.php?error=credenciales_invalidas");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: ../login.php?error=credenciales_invalidas");
        exit();
    }

    // Cerrar conexión (opcional, no obligatorio)
    $stmt->close();
    $conn->close();
}
?>
