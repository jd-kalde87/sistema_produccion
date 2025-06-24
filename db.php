<?php
// Configuración de la base de datos
$servidor = "localhost";
$usuario = "root"; 
$contrasena = ""; 
$basededatos = "produccion";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $contrasena, $basededatos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el charset a UTF-8 para evitar problemas con tildes y eñes
$conn->set_charset("utf8");
?>