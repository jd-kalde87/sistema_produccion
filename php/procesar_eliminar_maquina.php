<?php
session_start();

// Incluir archivos (ambos están en la misma carpeta php/)
include '../db.php';
include 'funciones.php';

// Validar sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php"); // Sube un nivel para llegar a la raíz
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_maquina = intval($_POST['id_maquina']);
    
    try {
        // Verificar que la máquina existe
        $sql_check = "SELECT id_maquina FROM maquinas WHERE id_maquina = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $id_maquina);
        $stmt_check->execute();
        
        if ($stmt_check->get_result()->num_rows === 0) {
            throw new Exception("La máquina seleccionada no existe");
        }
        
        // Eliminar la máquina
        $sql_delete = "DELETE FROM maquinas WHERE id_maquina = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_maquina);
        
        if (!$stmt_delete->execute()) {
            throw new Exception("Error al ejecutar la eliminación");
        }
        
        // Actualizar la lista en sesión
        $_SESSION['maquinas'] = obtenerMaquinas($conn);
        $_SESSION['success'] = "Máquina eliminada correctamente";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    // Redireccionar (ruta relativa desde php/ a eliminar_maquinaria.php en raíz)
    header("Location: ../eliminar_maquina.php");
    exit;
}
?>