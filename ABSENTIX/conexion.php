<?php
// Archivo: conexion.php
// Conexión a la base de datos 'absentix'

// ----------------------------------------------------
// ⚠️ VERIFICA ESTAS CREDENCIALES ⚠️
// ----------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   
define('DB_PASS', '');       // Contraseña vacía por defecto en XAMPP
define('DB_NAME', 'absentix'); 

// Intentar la conexión
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión y detener la ejecución si falla
if ($conexion->connect_error) {
    // Si la conexión falla, mandamos un error claro
    die("Error FATAL de Conexión a la Base de Datos: " . $conexion->connect_error);
}

// Establecer el juego de caracteres
$conexion->set_charset("utf8mb4");

// Función para cerrar la conexión
function cerrarConexion($conexion) {
    if ($conexion) {
        $conexion->close();
    }
}
?>