<?php
// --- INICIO: CÓDIGO PARA MOSTRAR ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIN: CÓDIGO PARA MOSTRAR ERRORES ---
include '../db.php'; // Incluir la conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y sanear los datos del formulario
    $primer_nombre = strtoupper($conn->real_escape_string(trim($_POST['primer_nombre'])));
    $segundo_nombre = strtoupper($conn->real_escape_string(trim($_POST['segundo_nombre'])));
    $primer_apellido = strtoupper($conn->real_escape_string(trim($_POST['primer_apellido'])));
    $segundo_apellido = strtoupper($conn->real_escape_string(trim($_POST['segundo_apellido'])));
    $tipo_identificacion = strtoupper($conn->real_escape_string(trim($_POST['tipo_identificacion'])));
    $tipo_identificacion = $conn->real_escape_string(trim($_POST['tipo_identificacion']));
    $numero_identificacion = $conn->real_escape_string(trim($_POST['numero_identificacion']));
    $fecha_nacimiento = $conn->real_escape_string(trim($_POST['fecha_nacimiento']));
    $email = $conn->real_escape_string(trim($_POST['email']));

    // 2. Validaciones del lado del servidor (se mantienen igual)
    if (empty($primer_nombre) || empty($tipo_identificacion) || empty($numero_identificacion) || empty($fecha_nacimiento) || empty($email)) {
        header("Location: ../registro.php?error=Todos los campos obligatorios son requeridos.");
        exit();
    }
    // ... (puedes añadir las otras validaciones de longitud, etc.)

    // 3. Verificar si el email o la identificación ya existen (se mantiene igual)
    $sql_check = "SELECT id FROM usuarios WHERE email = ? OR numero_identificacion = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $email, $numero_identificacion);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        header("Location: ../registro.php?error=El correo o número de identificación ya están registrados.");
        exit();
    }
    $stmt_check->close();

    // 4. ***NUEVA LÓGICA***: La contraseña es el número de identificación. La hasheamos.
    $clave_inicial = $numero_identificacion;
    $clave_hasheada = password_hash($clave_inicial, PASSWORD_DEFAULT);

    // 5. Insertar el nuevo usuario en la base de datos
    $sql_insert = "INSERT INTO usuarios (primer_nombre, segundo_nombre,primer_apellido, segundo_apellido, tipo_identificacion, numero_identificacion, fecha_nacimiento, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssssssss", $primer_nombre, $segundo_nombre,$primer_apellido, $segundo_apellido, $tipo_identificacion, $numero_identificacion, $fecha_nacimiento, $email, $clave_hasheada);

    if ($stmt_insert->execute()) {
        // 6. ***NUEVA LÓGICA***: Redirigir al login con un mensaje claro.
        header("Location: ../login.php?success=registro_exitoso");
        exit();
    } else {
        header("Location: ../registro.php?error=Error al crear el usuario.");
        exit();
    }

    $stmt_insert->close();
    $conn->close();
}
?>