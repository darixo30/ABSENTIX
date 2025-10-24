<?php
/**
 * Archivo: guardar_reporte_ajax.php
 * Propósito: Recibe los datos del formulario de reporte de conducta, los guarda en la tabla 'reportes_conducta' 
 * y responde en JSON para la solicitud AJAX.
 */

// 1. Establecer el encabezado para que el cliente espere una respuesta JSON
header('Content-Type: application/json');

// Asegúrate de que tu archivo de conexión esté disponible
require_once 'conexion.php'; 

// Inicializar la respuesta en caso de error
$response = ['status' => 'error', 'msg' => 'Solicitud no válida o método incorrecto.'];

// Verifica que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Recolección y saneamiento de datos
    // Uso de '??' para evitar advertencias si un campo no existe (Aunque en AJAX ya están validados)
    $nombre_alumno = $conexion->real_escape_string($_POST['studentName'] ?? '');
    $grado = $conexion->real_escape_string($_POST['grade'] ?? '');
    $grupo = $conexion->real_escape_string($_POST['group'] ?? '');
    $turno = $conexion->real_escape_string($_POST['shift'] ?? '');
    $especialidad = $conexion->real_escape_string($_POST['specialty'] ?? '');
    $num_control = $conexion->real_escape_string($_POST['idNumber'] ?? '');
    $fecha_incidente = $conexion->real_escape_string($_POST['reportDate'] ?? ''); 
    $nombre_reportante = $conexion->real_escape_string($_POST['reporterName'] ?? '');
    $tipo_falta = $conexion->real_escape_string($_POST['misconductType'] ?? '');
    $descripcion = $conexion->real_escape_string($_POST['misconductDetails'] ?? '');

    // Agregar la hora de registro
    $hora_registro = date("H:i:s");

    
    // 3. Inserción en la Base de Datos
    $sql = "INSERT INTO reportes_conducta (nombre_alumno, grado, grupo, turno, especialidad, num_control, fecha_incidente, nombre_reportante, tipo_falta, descripcion_incidente, hora_registro) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conexion->prepare($sql)) {
        // Enlazar 11 parámetros (10 strings de campos + 1 string de hora_registro)
        // Se añade 'hora_registro' a la consulta SQL y a bind_param
        $stmt->bind_param("sssssssssss", 
            $nombre_alumno, 
            $grado, 
            $grupo, 
            $turno, 
            $especialidad, 
            $num_control, 
            $fecha_incidente, 
            $nombre_reportante, 
            $tipo_falta, 
            $descripcion,
            $hora_registro
        );
        
        if ($stmt->execute()) {
            // Éxito: Enviar respuesta JSON
            $response['status'] = 'success';
            $response['msg'] = 'Reporte guardado exitosamente.';
        } else {
            // Error en la inserción. Responder con error JSON.
            $response['msg'] = "Error de base de datos: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Error de preparación de la consulta SQL. Responder con error JSON.
        $response['msg'] = "Error de preparación SQL: " . $conexion->error;
    }
} 

// 4. Enviar la respuesta JSON final
echo json_encode($response);
// No se necesita 'exit' después de echo json_encode() si es la última línea
?>