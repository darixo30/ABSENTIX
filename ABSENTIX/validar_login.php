<?php
session_start();
// No se necesita incluir conexion.php porque los usuarios son hardcodeados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Hardcodeo (Mamador) de cuentas
    $users = [
        // Usuario Matutino
        'orientacionmat258' => ['password' => 'orienmat', 'turno' => 'Matutino'], 
        // Usuario Vespertino
        'orientacionves258' => ['password' => 'orienves', 'turno' => 'Vespertino'], 
    ];
    
    // 1. Verificar si el usuario existe
    if (isset($users[$username])) {
        $user_data = $users[$username];
        
        // 2. Verificar la contraseña
        if ($password === $user_data['password']) {
            
            // 3. Inicio de sesión exitoso
            // GUARDAR VARIABLES CRÍTICAS EN SESIÓN
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['turno'] = $user_data['turno']; // ¡CLAVE PARA EL THEME Y EL FORMULARIO!
            
            // Redirigir a la página principal del sistema
            header("Location: MENU.php"); // Asegúrate de que tu menú se llama MENU.php
            exit;
            
        } else {
            // Contraseña incorrecta
            header("Location: LOGIN.php?error=pass");
            exit;
        }
    } else {
        // Usuario no encontrado
        header("Location: LOGIN.php?error=user");
        exit;
    }
} else {
    // Si acceden directamente a validar_login.php
    header("Location: LOGIN.php");
    exit;
}
?>