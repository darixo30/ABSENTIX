<?php
// =======================================================================
// Archivo: procesar_reporte.php
// Recibe los datos, los guarda en la DB y devuelve el JSON completo.
// =======================================================================

require_once 'conexion.php'; 

// Configuramos la respuesta como JSON
header('Content-Type: application/json');

// Campos recibidos del formulario
$id_alumno       = filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT);
$id_profesor     = filter_input(INPUT_POST, 'id_profesor', FILTER_VALIDATE_INT);
$fecha_incidente = filter_input(INPUT_POST, 'fecha_incidente', FILTER_SANITIZE_STRING);
$tipo_falta      = filter_input(INPUT_POST, 'tipo_falta', FILTER_SANITIZE_STRING);
$descripcion     = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);


if ($_SERVER["REQUEST_METHOD"] != "POST" || !$id_alumno || !$id_profesor || empty($tipo_falta) || empty($descripcion) || empty($fecha_incidente)) {
    http_response_code(400); 
    echo json_encode(["status" => "ERROR", "message" => "Faltan datos obligatorios o la solicitud es incorrecta."]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Guardar el nuevo reporte en la tabla 'reportes'
    $sql_insert = "INSERT INTO reportes 
                   (id_alumno, id_profesor, fecha_incidente, tipo_falta, descripcion) 
                   VALUES (?, ?, ?, ?, ?)";
                   
    $stmt = $pdo->prepare($sql_insert);
    $stmt->execute([$id_alumno, $id_profesor, $fecha_incidente, $tipo_falta, $descripcion]);

    $id_reporte_nuevo = $pdo->lastInsertId();

    // 2. Obtener todos los datos necesarios para el PDF (incluyendo nombres y datos del alumno)
    // NOTA: Se asume que tu tabla de alumnos tiene los campos n_control, grado, grupo, especialidad y turno
    $sql_datos = "SELECT 
                    r.*, 
                    a.nombre AS nombre_alumno,
                    a.n_control,
                    a.grado,
                    a.grupo,
                    a.especialidad,
                    a.turno,
                    p.nombre AS nombre_profesor 
                  FROM reportes r
                  JOIN alumnos a ON r.id_alumno = a.id_alumno
                  JOIN profesores p ON r.id_profesor = p.id_profesor
                  WHERE r.id_reporte = ?";
                  
    $stmt_datos = $pdo->prepare($sql_datos);
    $stmt_datos->execute([$id_reporte_nuevo]);
    $datos_reporte = $stmt_datos->fetch();

    $pdo->commit();
    
    if ($datos_reporte) {
         // Devolvemos el JSON con los datos completos para que JavaScript genere el PDF
         echo json_encode(["status" => "SUCCESS", "reporte_info" => $datos_reporte]); 
    } else {
         http_response_code(500); 
         echo json_encode(["status" => "ERROR", "message" => "El reporte se guardó, pero faltan datos de Alumno o Profesor (IDs no encontrados)."]);
    }

} catch (\PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); 
    error_log("Error al guardar reporte: " . $e->getMessage()); 
    echo json_encode(["status" => "ERROR", "message" => "Fallo al guardar en la DB: " . $e->getMessage()]);
}
?>