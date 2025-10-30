<?php
// Archivo: conexion.php

/**
 * Establece una conexión a la base de datos 'absentix'.
 * Lanza una excepción si la conexión falla.
 * @return mysqli La conexión establecida.
 * @throws Exception Si la conexión falla.
 */
function conectarBD() {
    $servidor = "localhost";
    $usuario = "root";
    $contrasena = ""; // CAMBIA ESTO SOLO SI USAS UNA CONTRASEÑA EN TU XAMPP/WAMP
    $base_datos = "absentix"; 

    // Crear conexión
    $conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

    // Verificar conexión
    if ($conexion->connect_error) {
        // Lanzamos una excepción con el error específico de MySQL
        throw new Exception("Error de conexión a la base de datos: " . $conexion->connect_error);
    }
    
    // Establecer charset
    $conexion->set_charset("utf8mb4");
    
    return $conexion;
}

/**
 * Cierra una conexión a la base de datos.
 * @param mysqli $conexion La conexión a cerrar.
 */
function cerrarConexion($conexion) {
    if ($conexion instanceof mysqli) {
        $conexion->close();
    }
}
// NOTA: No cierres la etiqueta PHP.