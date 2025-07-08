<?php
session_start();
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recibir y limpiar todos los datos del formulario
    $primer_nombre = mb_strtoupper(trim($_POST['primer_nombre']), 'UTF-8');
    $segundo_nombre = mb_strtoupper(trim($_POST['segundo_nombre']), 'UTF-8');
    $primer_apellido = mb_strtoupper(trim($_POST['primer_apellido']), 'UTF-8');
    $segundo_apellido = mb_strtoupper(trim($_POST['segundo_apellido']), 'UTF-8');
    $tipo_identificacion = $_POST['tipo_identificacion'];
    $numero_identificacion = trim($_POST['numero_identificacion']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $email=$_POST['email'];
    $rol = $_POST['rol'];

    // --- LÓGICA DE VALIDACIÓN ---

    // 2. Comprobar si se está intentando crear un administrador
    if ($rol === 'administrador') {
        // Buscamos si ya existe un administrador
        $sql_check_admin = "SELECT CONCAT(primer_nombre, ' ', primer_apellido) as nombre_completo FROM usuarios WHERE rol = 'administrador' LIMIT 1";
        $resultado = $conn->query($sql_check_admin);

        if ($resultado && $resultado->num_rows > 0) {
            $admin_existente = $resultado->fetch_assoc();
            $nombre_admin = $admin_existente['nombre_completo'];
            
            // Creamos el mensaje de error y redirigimos
            $error = "Ya existe un administrador registrado: " . htmlspecialchars(strtoupper($nombre_admin)) . ". Solo se permite un administrador.";
            header("Location: ../registro.php?error=" . urlencode($error));
            exit;
        }
    }
    
    // 3. Validar si el email o la identificación ya existen
    $sql_check_user = "SELECT id FROM usuarios WHERE email = ? OR numero_identificacion = ?";
    $stmt_check = $conn->prepare($sql_check_user);
    $stmt_check->bind_param("ss", $email, $numero_identificacion);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        header("Location: ../registro.php?error=" . urlencode("El correo electrónico o el número de identificación ya están registrados."));
        exit;
    }
    $stmt_check->close();

    // 4. Si todas las validaciones pasan, procedemos con la inserción

    // La contraseña inicial será el número de identificación
    $password_inicial = $numero_identificacion;
    $password_hasheado = password_hash($password_inicial, PASSWORD_DEFAULT);

    // Consulta INSERT completa y segura con todos los campos
    $sql_insert = "INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, tipo_identificacion, numero_identificacion, fecha_nacimiento, email, password, rol, primer_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt_insert = $conn->prepare($sql_insert);
    
    // El bind_param debe coincidir con los campos y tipos: ssssssssss
    $stmt_insert->bind_param("ssssssssss", 
        $primer_nombre, 
        $segundo_nombre, 
        $primer_apellido, 
        $segundo_apellido, 
        $tipo_identificacion, 
        $numero_identificacion, 
        $fecha_nacimiento, 
        $email, 
        $password_hasheado, 
        $rol
    );

    if ($stmt_insert->execute()) {
        // Redirigir a la página de login con un mensaje de éxito
        header("Location: ../login.php?success=registro_exitoso");
    } else {
        // Redirigir con mensaje de error genérico
        header("Location: ../registro.php?error=" . urlencode("Error al registrar el usuario. Inténtalo de nuevo."));
    }

    $stmt_insert->close();
    $conn->close();

} else {
    // Si alguien intenta acceder al archivo directamente
    header("Location: ../registro.php");
    exit;
}
?>