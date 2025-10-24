<?php
// Archivo: guardar_justificante_completo.php (VERSIÓN FINAL ANTITRUENOS)

// Muestra errores de PHP para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();

require_once 'conexion.php'; 

if (!$conexion) {
    header("location: JUSTIFICANTES.php?status=error_500&msg=" . urlencode("Fallo de conexión a la BD."));
    exit;
}

$error_msg = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recolección de datos
    $nombre_completo = $conexion->real_escape_string($_POST['studentName'] ?? '');
    $grado = $conexion->real_escape_string($_POST['grade'] ?? '');
    $grupo = $conexion->real_escape_string($_POST['group'] ?? '');
    $especialidad = $conexion->real_escape_string($_POST['specialty'] ?? '');
    $num_control = $conexion->real_escape_string($_POST['idNumber'] ?? '');
    $turno = $conexion->real_escape_string($_POST['shift'] ?? '');
    
    $fechas_ausencia = $conexion->real_escape_string($_POST['absenceDates'] ?? '');
    $motivo = $conexion->real_escape_string($_POST['reason'] ?? '');
    $notas = $conexion->real_escape_string($_POST['notes'] ?? '');

    // 2. Manejo de Archivos (Subida - Omite si no tienes la carpeta 'uploads/documentos')
    $ruta_doc = NULL;
    $upload_dir = 'uploads/documentos/'; 
    if (isset($_FILES['supportDoc']) && $_FILES['supportDoc']['error'] == 0) {
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
            $error_msg = "Advertencia: No se pudo crear la carpeta de subida. ";
        }
        $extension = pathinfo($_FILES['supportDoc']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = $num_control . '_' . time() . '.' . $extension;
        $ruta_completa = $upload_dir . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['supportDoc']['tmp_name'], $ruta_completa)) {
            $ruta_doc = $ruta_completa;
        } else {
            $error_msg .= "Advertencia: El archivo adjunto no se pudo subir. ";
        }
    }


    // ----------------------------------------------------
    // 3. ACTUALIZAR/INSERTAR EN LA TABLA ALUMNOS
    // ----------------------------------------------------
    
    // Intenta actualizar los datos del alumno (por si ya existe)
    $sql_alumno_update = "
        UPDATE alumnos SET 
            nombre_completo = ?, grado = ?, grupo = ?, turno = ?, especialidad = ? 
        WHERE num_control = ?
    ";
    
    if ($stmt_update = $conexion->prepare($sql_alumno_update)) {
        $stmt_update->bind_param("ssssss", $nombre_completo, $grado, $grupo, $turno, $especialidad, $num_control);
        $stmt_update->execute();
        $rows_affected = $stmt_update->affected_rows;
        $stmt_update->close();
        
        if ($rows_affected === 0) {
            // El alumno NO existe, inserta el nuevo registro
            $sql_alumno_insert = "
                INSERT INTO alumnos (num_control, nombre_completo, grado, grupo, turno, especialidad)
                VALUES (?, ?, ?, ?, ?, ?)
            ";
            
            if ($stmt_insert = $conexion->prepare($sql_alumno_insert)) {
                $stmt_insert->bind_param("ssssss", $num_control, $nombre_completo, $grado, $grupo, $turno, $especialidad);
                
                if (!$stmt_insert->execute()) {
                    $error_msg = "Error FATAL al insertar nuevo alumno en ALUMNOS: " . $stmt_insert->error;
                    $stmt_insert->close();
                    goto end_script;
                }
                $stmt_insert->close();
            } else {
                $error_msg = "Error de preparación SQL (Insert Alumno): " . $conexion->error;
                goto end_script;
            }
        }
    } else {
        $error_msg = "Error de preparación SQL (Update Alumno): " . $conexion->error;
        goto end_script;
    }
    
    // ----------------------------------------------------
    // 4. INSERCIÓN EN LA TABLA JUSTIFICANTES (Solo con num_control)
    // ----------------------------------------------------
    
    $sql_justificante = "
        INSERT INTO justificantes (num_control, turno, fechas_ausencia, motivo, notas_adicionales, doc_soporte_ruta) 
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    if ($stmt_j = $conexion->prepare($sql_justificante)) {
        $stmt_j->bind_param("ssssss", $num_control, $turno, $fechas_ausencia, $motivo, $notas, $ruta_doc);
        
        if ($stmt_j->execute()) {
            $last_id = $conexion->insert_id; 
            $stmt_j->close();
            cerrarConexion($conexion);
            
            // Redirección de éxito para generar el PDF (usando el ID)
            header("location: JUSTIFICANTES.php?status=success&id=" . $last_id);
            exit; 
            
        } else {
            $error_msg = "Error al guardar el justificante en BD: " . $stmt_j->error;
            $stmt_j->close();
        }
    } else {
        $error_msg = "Error de preparación SQL (Insert Justificante): " . $conexion->error;
    }
} 

// Manejo de errores final
end_script:

if (!empty($error_msg)) {
    cerrarConexion($conexion);
    header("location: JUSTIFICANTES.php?status=error_500&msg=" . urlencode($error_msg));
    exit;
}

header("location: JUSTIFICANTES.php"); 
exit;
?>