<?php
// Archivo: guardar_justificante.php (VERSIÓN FINAL Y FUNCIONAL - No. Control: 14 Dígitos)

session_start();

// 🚨 COMPROBACIÓN DE SESIÓN (Si el usuario no está logueado, ¡FUERA!)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: LOGIN.php");
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'conexion.php'; 

// ----------------------------------------------------
// Función de ayuda para manejar errores y redirigir
// ----------------------------------------------------
function redirectWithError($conexion, $msg) {
    // Si la conexión sigue abierta, la cerramos de forma segura
    if (isset($conexion) && function_exists('cerrarConexion')) {
        cerrarConexion($conexion); 
    }
    // Redirigir al formulario de Justificantes, pasando el error.
    header("location: JUSTIFICANTES.php?status=error_500&msg=" . urlencode($msg));
    exit;
}

// 💥 1. ESTABLECER LA CONEXIÓN CRÍTICA 💥
try {
    // Llama a la función definida en conexion.php para crear el objeto $conexion.
    $conexion = conectarBD(); 
} catch (Exception $e) {
    // Manejar el error de conexión fatal.
    redirectWithError(null, "Error al conectar con la BD: " . $e->getMessage());
}

if (!$conexion) {
    // Verificación adicional de objeto nulo.
    redirectWithError(null, "Fallo de conexión a la BD: Objeto de conexión nulo.");
}

$validation_errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Recolección de datos (Alineado con el formulario unificado)
    $nombre_completo = trim($_POST['studentName'] ?? ''); // Campo único de nombre
    $grado = trim($_POST['grade'] ?? '');
    $grupo = trim($_POST['group'] ?? '');
    $especialidad = trim($_POST['specialty'] ?? '');
    $num_control = trim($_POST['idNumber'] ?? '');
    $turno = trim($_POST['shift'] ?? ''); 
    
    $fechas_ausencia = trim($_POST['absenceDates'] ?? ''); // Campo oculto unificado (Desde: X hasta: Y)
    $motivo = trim($_POST['reason'] ?? '');
    $notas_adicionales = trim($_POST['notes'] ?? ''); 
    
    // Manejo básico de archivos (solo placeholder, la lógica de subida real va aquí)
    $ruta_doc = NULL; 
    
    if (isset($_FILES['supportDoc']) && $_FILES['supportDoc']['error'] === UPLOAD_ERR_OK) {
        // En una implementación real, aquí iría la validación de tipo/tamaño 
        // y el código para mover el archivo a una carpeta de 'uploads'.
        // Por ahora, solo ponemos un placeholder si el archivo viene:
        $ruta_doc = "uploads/documento_" . $num_control . "_" . time() . ".pdf"; 
        // NOTA: Reemplaza esto con tu lógica de manejo de archivos real.
    }
    
    // ----------------------------------------------------
    // 3. VALIDACIÓN ESTRICTA
    // ----------------------------------------------------
    
    $alpha_pattern = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/';

    // A. Validación de Número de Control (💥 ¡CORREGIDO A 14 DÍGITOS! 💥)
    if (!preg_match('/^\d{14}$/', $num_control)) {
        $validation_errors['idNumber'] = 'El Número de Control debe ser exactamente de 14 dígitos numéricos.';
    }

    // B. Validación de Nombre Completo
    if (!preg_match($alpha_pattern, $nombre_completo) || strlen($nombre_completo) < 5) {
         $validation_errors['studentName'] = 'El Nombre solo debe contener letras, espacios y puntos, y ser un nombre completo.';
    }
    
    // C. Validación de Especialidad
    if (!preg_match($alpha_pattern, $especialidad) || empty($especialidad)) {
        $validation_errors['specialty'] = 'La Especialidad solo debe contener letras.';
    }

    // D. Validación de Fechas (Solo revisamos que el campo oculto no esté vacío)
    if (empty($fechas_ausencia)) {
        $validation_errors['absenceDates'] = 'Las fechas de inasistencia son obligatorias.';
    }
    
    // E. Validación de campos obligatorios
    if (empty($grado)) $validation_errors['grade'] = 'Grado es obligatorio.';
    if (empty($grupo)) $validation_errors['group'] = 'Grupo es obligatorio.';
    if (empty($turno)) $validation_errors['shift'] = 'Turno es obligatorio.';
    if (empty($motivo)) $validation_errors['reason'] = 'Motivo es obligatorio.';


    // Si hay errores de validación, retornamos inmediatamente.
    if (!empty($validation_errors)) {
        // Almacenar errores y datos de POST para rellenar el formulario
        $_SESSION['validation_errors'] = $validation_errors;
        $_SESSION['post_data'] = $_POST;
        redirectWithError($conexion, "Errores de validación en el formulario.");
    }
    
    // ----------------------------------------------------
    // 4. UPDATE/INSERT EN LA TABLA ALUMNOS (UPSERT)
    // ----------------------------------------------------
    
    // Intenta actualizar los datos del alumno (por si ya existe)
    $sql_alumno_update = "
        UPDATE alumnos SET 
            nombre_completo = ?, grado = ?, grupo = ?, turno = ?, especialidad = ? 
        WHERE num_control = ?
    ";
    
    if ($stmt_update = $conexion->prepare($sql_alumno_update)) {
        // 'ssssss' -> nombre, grado, grupo, turno, especialidad, num_control
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
                // 'ssssss' -> num_control, nombre, grado, grupo, turno, especialidad
                $stmt_insert->bind_param("ssssss", $num_control, $nombre_completo, $grado, $grupo, $turno, $especialidad);
                
                if (!$stmt_insert->execute()) {
                    redirectWithError($conexion, "Error FATAL al insertar nuevo alumno: " . $stmt_insert->error);
                }
                $stmt_insert->close();
            } else {
                redirectWithError($conexion, "Error de preparación SQL (Insert Alumno): " . $conexion->error);
            }
        }
    } else {
        redirectWithError($conexion, "Error de preparación SQL (Update Alumno): " . $conexion->error);
    }
    
    // ----------------------------------------------------
    // 5. INSERCIÓN EN LA TABLA JUSTIFICANTES
    // ----------------------------------------------------
    
    $sql_justificante = "
        INSERT INTO justificantes (num_control, turno, fechas_ausencia, motivo, notas_adicionales, doc_soporte_ruta) 
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    if ($stmt_j = $conexion->prepare($sql_justificante)) {
        // 'ssssss' -> num_control, turno, fechas_ausencia, motivo, notas, ruta_doc
        $stmt_j->bind_param("ssssss", $num_control, $turno, $fechas_ausencia, $motivo, $notas_adicionales, $ruta_doc);
        
        if ($stmt_j->execute()) {
            $last_id = $conexion->insert_id; 
            $stmt_j->close();
            cerrarConexion($conexion);
            
            // Redirección de éxito para generar el PDF (usando el ID)
            header("location: JUSTIFICANTES.php?status=success&id=" . $last_id);
            exit; 
            
        } else {
            redirectWithError($conexion, "Error al guardar el justificante en BD: " . $stmt_j->error);
        }
    } else {
        redirectWithError($conexion, "Error de preparación SQL (Insert Justificante): " . $conexion->error);
    }
} 

// Si llega hasta aquí sin POST, redireccionamos al formulario
header("location: JUSTIFICANTES.php"); 
exit;
?>