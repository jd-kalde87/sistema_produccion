<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Restablecer Contraseña 🔑</h2>
        <p style="margin-bottom: 20px;">Ingresa tus datos para verificar tu identidad. Si son correctos, tu contraseña se restablecerá a tu número de identificación.</p>
        
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <form action="php/procesar_recuperar.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico Registrado</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="numero_identificacion">Número de Identificación</label>
                <input type="text" id="numero_identificacion" name="numero_identificacion" required>
            </div>
             <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <button type="submit" class="btn">Verificar y Restablecer</button>
        </form>
        <div class="form-links">
            <a href="login.php">Volver a inicio</a>
        </div>
    </div>
</body>
</html>