<?php
/**
 * Archivo: validar_login.php
 * Propósito: Valida las credenciales contra la tabla 'usuarios' de la BD.
 */

// ¡CORRECCIÓN CRÍTICA! Debes iniciar la sesión antes de usar $_SESSION
session_start(); 

require_once 'conexion.php'; 

// Verifica que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Saneamiento de los datos de entrada
    $usuario_ingresado = $conexion->real_escape_string($_POST['username'] ?? '');
    $contrasena_ingresada = $_POST['password'] ?? '';

    // Consulta SQL: Utilizamos sentencias preparadas
    $sql = "SELECT id_usuario, contrasena FROM usuarios WHERE usuario = ?"; 
    
    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $usuario_ingresado);
        
        if (!$stmt->execute()) {
             // Manejo de error de ejecución
            $error_msg = "Error de ejecución: " . $stmt->error;
            $stmt->close();
            $conexion->close();
            header("location: LOGIN.php?error=server_exec&msg=" . urlencode($error_msg));
            exit;
        }

        $stmt->store_result();
        
        if ($stmt->num_rows == 1) {
            // Usuario encontrado, vincular los resultados
            $stmt->bind_result($user_id, $db_contrasena);
            $stmt->fetch();

            // Usando la validación de texto plano (Asegúrate de cambiar esto a password_verify)
            if ($contrasena_ingresada === $db_contrasena) {
                
                // Credenciales CORRECTAS.
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['id'] = $user_id; 
                $_SESSION['username'] = $usuario_ingresado;
                
                $stmt->close();
                $conexion->close();
                
                header("location: MENU.php"); 
                exit;

            } else {
                // Contraseña INCORRECTA
                $stmt->close();
                $conexion->close();
                header("location: LOGIN.php?error=pass"); 
                exit;
            }
        } else {
            // Usuario NO encontrado
            $stmt->close();
            $conexion->close();
            header("location: LOGIN.php?error=user"); 
            exit;
        }
    } else {
        // Error de preparación de la consulta SQL.
        $error_msg = "Error de preparación SQL: " . $conexion->error; 
        

        $conexion->close(); 

        header("location: LOGIN.php?error=server_sql&msg=" . urlencode($error_msg));
        exit;
    }
} else {
    // Si se accede directamente a este archivo, redirigir al login
    header("location: LOGIN.php");
    exit;
}
?>