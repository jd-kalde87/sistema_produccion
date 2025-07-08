<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($conn->real_escape_string($_POST['email']));
    $password = trim($_POST['password']);

    // Traemos el rol del usuario
    $sql = "SELECT id, primer_nombre, primer_apellido, numero_identificacion, password, primer_ingreso, rol FROM usuarios WHERE email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
            // Iniciar sesión
            $_SESSION['loggedin'] = true;
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['primer_nombre'] . ' ' . $usuario['primer_apellido'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirigir si es el primer ingreso
            if ($usuario['primer_ingreso'] == 1) {
                header("Location: ../cambiar_clave.php");
                exit();
            }

            // Redirección final basada en el rol
            if (strtolower($usuario['rol']) === 'administrador') {
                header("Location: ../inicio.php");
            } elseif (strtolower($usuario['rol']) === 'operario') {
                header("Location: ../inicio_operario.php");
            } else {
                header("Location: ../login.php?error=rol_invalido");
            }
            exit();

        } else {
            header("Location: ../login.php?error=credenciales_invalidas");
            exit();
        }
    } else {
        header("Location: ../login.php?error=credenciales_invalidas");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>