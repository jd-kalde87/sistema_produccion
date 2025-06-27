<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Registro de Nuevo Usuario ✨</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<p class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>
        <form action="php/procesar_registro.php" method="POST">
            <div class="form-group">
                <label for="primer_nombre">Primer Nombre</label>
                <input type="text" id="primer_nombre" name="primer_nombre" required>
            </div>
            <div class="form-group">
                <label for="segundo_nombre">Segundo Nombre (Opcional)</label>
                <input type="text" id="segundo_nombre" name="segundo_nombre">
            </div>
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required>
            </div>
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido (Opcional)</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido">
            </div>
            <div class="form-group">
                <label for="tipo_identificacion">Tipo de Identificación</label>
                <select id="tipo_identificacion" name="tipo_identificacion" required>
                    <option value="CC">Cédula de Ciudadanía</option>
                    <option value="CE">Cédula de Extranjería</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_identificacion">Número de Identificación</label>
                <input type="text" id="numero_identificacion" name="numero_identificacion" required minlength="8" maxlength="11" pattern="\d{8,11}">
            </div>
             <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-agregar">Registrarme</button>
        </form>
        <div class="form-links">
            <a href="login.php">Ya tengo una cuenta</a>
        </div>
    </div>
</body>
</html>