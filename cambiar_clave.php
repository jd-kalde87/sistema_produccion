<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Hola, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h2>
        <p class="alert alert-info">Por seguridad, debes cambiar tu contraseña antes de continuar.</p>
        
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <form action="php/procesar_cambio_clave.php" method="POST">
            <div class="form-group">
                <label for="nueva_clave">Nueva Contraseña</label>
                <input type="password" id="nueva_clave" name="nueva_clave" required>
            </div>
            <div class="form-group">
                <label for="confirmar_clave">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirmar_clave" name="confirmar_clave" required>
            </div>
            <button type="submit" class="btn">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>