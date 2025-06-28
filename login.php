<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>           
    <div class="form-container">
            <h2>Iniciar Sesión 🚀</h2>
            <?php
            // ... (otros mensajes de error)
            if (isset($_GET['success']) && $_GET['success'] == 'registro_exitoso') {
                echo '<p class="alert alert-success">¡Registro exitoso! Tu contraseña para el primer ingreso es tu <strong>número de identificación</strong>.</p>';
            }
            if (isset($_GET['reset']) && $_GET['reset'] == 'exitoso') {
                echo '<p class="alert alert-success">¡Contraseña restablecida! Tu nueva contraseña es tu <strong>número de identificación</strong>. Deberás cambiarla al ingresar.</p>';
            }
            if (isset($_GET['error']) && $_GET['error'] == 'credenciales_invalidas') {
            echo '<p class="alert alert-danger">Usuario o contraseña incorrectos.</p>';
            }       
            ?>
            <form action="php/procesar_login.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-agregar">Ingresar</button>
            </form>
            <div class="form-links">
                <a href="registro.php">Registrar un usuario nuevo</a>
                <a href="recuperar.php">Olvidé mi contraseña</a>
            </div>
        </div>
</body>
</html>