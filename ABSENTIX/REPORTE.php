<?php
// Archivo: REPORTE.php (Registro de Reportes de Conducta - Versión FINAL con Descarga Manual)

session_start();

// 🚨 COMPROBACIÓN DE SESIÓN (Redirección si no está logueado)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: LOGIN.php"); 
    exit;
}

// Obtener el turno de la sesión y definir el tema
$user_turno = $_SESSION['turno'] ?? 'Vespertino'; 
$current_theme = strtolower($user_turno);

// =================================================================
// 🚨 CONEXIÓN A LA BASE DE DATOS
// Asegúrate de que este archivo contiene la función cerrarConexion
require_once 'conexion.php'; 

$success_message = $_SESSION['success_message'] ?? "";
$error_message = $_SESSION['error_message'] ?? "";
$post_data = $_SESSION['post_data'] ?? []; 
$reporte_data = null; // Para almacenar los datos si se debe generar el PDF

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
unset($_SESSION['post_data']);

// =================================================================
// LÓGICA DE RECUPERACIÓN DE DATOS PARA EL PDF (Bloque JS PDF)
// Se activa si se acaba de guardar un reporte y el ID está en la sesión.
// =================================================================
$id_reporte_a_descargar = null;
if (isset($_SESSION['last_reporte_id'])) {
    $id_reporte_a_descargar = intval($_SESSION['last_reporte_id']);
    unset($_SESSION['last_reporte_id']); // Limpiar la sesión para evitar descargas dobles
    
    // Si la conexión no está activa, la inicializamos
    if (!isset($conexion) && function_exists('conectarBD')) {
        $conexion = conectarBD();
    }
}

if ($id_reporte_a_descargar) {
    // Asumiendo que $conexion está disponible
    if (isset($conexion)) {
         $sql_fetch = "
             SELECT 
                 *
             FROM reportes_conducta 
             WHERE id_reporte = ?
           ";
         
         if ($stmt = $conexion->prepare($sql_fetch)) {
             $stmt->bind_param("i", $id_reporte_a_descargar);
             $stmt->execute();
             $result = $stmt->get_result();
             
             if ($result->num_rows == 1) {
                 $reporte_data = $result->fetch_assoc(); // 🚨 Esta variable pasa al JS
             }
             $stmt->close();
         }
         // Si la conexión se abrió aquí solo para el fetch, la cerramos
         if (function_exists('cerrarConexion')) {
             cerrarConexion($conexion);
             unset($conexion); 
         }
    }
}


// =================================================================
// LÓGICA DE ENVÍO DEL FORMULARIO
// =================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recolección de datos
    $num_control = trim($_POST['num_control'] ?? '');
    $nombre_s = trim($_POST['nombre_s'] ?? ''); 
    $apellido_paterno = trim($_POST['apellido_paterno'] ?? ''); 
    $apellido_materno = trim($_POST['apellido_materno'] ?? ''); 
    $grado = trim($_POST['grado'] ?? '');
    $grupo = trim($_POST['grupo'] ?? '');
    $turno = trim($_POST['turno'] ?? ''); 
    $especialidad = trim($_POST['especialidad'] ?? '');
    $fecha_incidente = trim($_POST['fecha_incidente'] ?? '');
    $nombre_reportante = trim($_POST['nombre_reportante'] ?? $_SESSION['user_name'] ?? 'Orientador');
    $tipo_falta = trim($_POST['tipo_falta'] ?? '');
    $descripcion_incidente = trim($_POST['descripcion_incidente'] ?? '');

    $nombre_alumno_db = "$nombre_s $apellido_paterno $apellido_materno";
    
    // Vuelve a incluir la conexión si se cerró antes o si no estaba definida (solo para POST)
    if (!isset($conexion) && function_exists('conectarBD')) {
        $conexion = conectarBD();
    }

    // 2. Revalidación simple en backend 
    if (empty($num_control) || empty($nombre_s) || empty($apellido_paterno) || empty($apellido_materno) || empty($grado) || empty($grupo) || empty($turno) || empty($especialidad) || empty($fecha_incidente) || empty($tipo_falta) || empty($descripcion_incidente)) {
        $_SESSION['error_message'] = "Por favor, complete todos los campos obligatorios del alumno y el incidente.";
        $_SESSION['post_data'] = $_POST;
    } 
    
    // 3. Lógica para verificar e insertar/actualizar la tabla 'alumnos'
    // El código de alumnos (insert/update) va aquí, es el mismo que el anterior.
    if (empty($_SESSION['error_message']) && isset($conexion)) {
        $sql_check_alumno = "SELECT num_control FROM alumnos WHERE num_control = ?";
        if ($stmt_check = $conexion->prepare($sql_check_alumno)) {
            $stmt_check->bind_param("s", $num_control);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows == 0) {
                // El alumno NO existe, se procede a insertarlo
                $sql_insert_alumno = "
                    INSERT INTO alumnos (num_control, nombre_completo, grado, grupo, turno, especialidad, estatus)
                    VALUES (?, ?, ?, ?, ?, ?, 'Activo')
                ";
                
                if ($stmt_insert = $conexion->prepare($sql_insert_alumno)) {
                    $stmt_insert->bind_param("ssssss", 
                        $num_control, $nombre_alumno_db, $grado, $grupo, $turno, $especialidad
                    );
                    
                    if (!$stmt_insert->execute()) {
                        error_log("Error al insertar alumno $num_control: " . $stmt_insert->error);
                    }
                    $stmt_insert->close();
                } 
            } else {
                // El alumno ya existe. Actualizamos sus datos por si cambiaron.
                $sql_update_alumno = "
                    UPDATE alumnos 
                    SET nombre_completo = ?, grado = ?, grupo = ?, turno = ?, especialidad = ? 
                    WHERE num_control = ?
                ";
                if ($stmt_update = $conexion->prepare($sql_update_alumno)) {
                    $stmt_update->bind_param("ssssss", 
                        $nombre_alumno_db, $grado, $grupo, $turno, $especialidad, $num_control
                    );
                    if (!$stmt_update->execute()) {
                         error_log("Error al actualizar datos del alumno $num_control: " . $stmt_update->error);
                    }
                    $stmt_update->close();
                }
            }
            $stmt_check->close();
        } 
    }
    
    // 4. Inserción en la tabla 'reportes_conducta'
    $sql_insert = "
        INSERT INTO reportes_conducta (
            nombre_alumno, grado, grupo, turno, especialidad, num_control, 
            fecha_incidente, nombre_reportante, tipo_falta, descripcion_incidente
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $reporte_insertado = false;
    if (empty($_SESSION['error_message']) && isset($conexion) && $stmt = $conexion->prepare($sql_insert)) {
        $stmt->bind_param("ssssssssss", 
            $nombre_alumno_db, $grado, $grupo, $turno, $especialidad, $num_control, 
            $fecha_incidente, $nombre_reportante, $tipo_falta, $descripcion_incidente
        );
        
        if ($stmt->execute()) {
            $reporte_insertado = true;
            $last_id = $conexion->insert_id; // 🚨 Obtener el ID del reporte insertado
        } else {
            $_SESSION['error_message'] = "Error al guardar el reporte: " . $stmt->error;
            $_SESSION['post_data'] = $_POST;
        }
        $stmt->close();
    } elseif (empty($_SESSION['error_message'])) {
        $_SESSION['error_message'] = "Error de conexión/preparación SQL (Insert Reporte).";
        $_SESSION['post_data'] = $_POST;
    }

    // 5. MUESTRA MENSAJE Y HABILITA DESCARGA MANUAL
    if ($reporte_insertado) {
        // 🚨 MENSAJE CORTO SIN LA ADVERTENCIA OBSOLETA
        $_SESSION['success_message'] = "✅ Reporte de conducta registrado con éxito. ¡Listo para descargar!";
        $_SESSION['last_reporte_id'] = $last_id; // 🚨 Guardar el ID para el botón
        
        // Cierre de la conexión
        if (isset($conexion) && function_exists('cerrarConexion')) {
            cerrarConexion($conexion);
        }
        
        // Redirección SIN los parámetros GET (Limpia la URL)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // 6. Cierre de conexión y redirección (si hubo error y no se insertó)
    if (isset($conexion) && function_exists('cerrarConexion')) {
        cerrarConexion($conexion);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Cierre de conexión final para peticiones GET (si aún está abierta)
if (isset($conexion) && function_exists('cerrarConexion')) {
     // Esto asegura que se limpie si se abrió al inicio y no se usó
     // cerrarConexion($conexion); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* -------------------------------------------------------------------------- */
        /* Variables y Temas (Dark Glassmorphism) */
        /* -------------------------------------------------------------------------- */
        :root {
            --color-primary-red: #9d5353;
            --color-error-dark: #ffcccc;
            --font-logo: 'Playwrite US Modern', cursive;
            --font-main: 'Alan Sans', sans-serif;
            --bg-gradient: linear-gradient(to top, #1c1e26, #263248);
            --bg-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 30px rgba(255, 255, 255, 0.3);
            --glass-border: 1px solid rgba(255, 255, 255, 0.18);
            --input-bg: rgba(255, 255, 255, 0.1);
            --input-border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .theme-vespertino, .theme-matutino {
            color: #fff; background: var(--bg-gradient); background-image: var(--bg-image); background-size: cover; background-position: center; background-attachment: fixed;
        }
        .theme-vespertino .page-container, .theme-matutino .page-container { background: rgba(0, 0, 0, 0.4); }
        .theme-vespertino .logo-text i, .theme-matutino .logo-text i { color: var(--color-primary-red); }
        .theme-vespertino .section-title, .theme-matutino .section-title { border-bottom: 2px solid var(--color-primary-red); color: #ffffffff; }
        .theme-vespertino .form-section, .theme-matutino .form-section { background: var(--glass-bg); box-shadow: var(--glass-shadow); border: var(--glass-border); backdrop-filter: blur(15px); }
        .theme-vespertino input:focus, .theme-vespertino select:focus, .theme-vespertino textarea:focus, 
        .theme-matutino input:focus, .theme-matutino select:focus, .theme-matutino textarea:focus { 
            border-color: var(--color-primary-red); 
            background: rgba(255, 255, 255, 0.15);
        }
        .theme-vespertino input, .theme-vespertino select, .theme-vespertino textarea, .theme-matutino input, .theme-matutino select, .theme-matutino textarea { background: var(--input-bg); color: #fff; border: var(--input-border); }
        .theme-vespertino .submit-button, .theme-matutino .submit-button { background: var(--color-primary-red); }
        .theme-vespertino .submit-button:hover, .theme-matutino .submit-button:hover { background: #7a4242; }

        /* 💥 CORRECCIÓN: Estilos Forzados para <option> 💥 */
        .theme-vespertino select option, .theme-matutino select option { 
            background-color: #1c1e26 !important; /* Fondo muy oscuro forzado */
            color: #fff !important; /* Texto blanco forzado */
        }
        /* 💥 FIN DE CORRECCIÓN 💥 */


        /* -------------------------------------------------------------------------- */
        /* Estilos Comunes y Layout */
        /* -------------------------------------------------------------------------- */
        body { margin: 0; font-family: var(--font-main); min-height: 100vh; display: flex; flex-direction: column; }
        .page-container { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 20px 0; opacity: 1; }
        header { width: 100%; max-width: 700px; display: flex; justify-content: space-between; align-items: center; padding: 20px 0; margin-bottom: 20px; }
        .logo-text { font-family: var(--font-logo); font-size: 28px; margin: 0; }
        .back-link { text-decoration: none; font-size: 16px; transition: 0.3s; color: #ccc; }
        .back-link:hover { color: #fff; text-shadow: 0 0 5px rgba(255, 255, 255, 0.5); }
        .form-section { border-radius: 20px; padding: 40px; width: 100%; max-width: 700px; }
        .section-title { font-family: var(--font-logo); font-size: 32px; margin-bottom: 30px; text-align: center; padding-bottom: 10px; display: block; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-row .form-group { flex: 1; }
        label { margin-bottom: 8px; font-weight: 600; font-size: 14px; display: block; }
        input, select, textarea { padding: 12px 15px; border-radius: 8px; font-size: 16px; transition: border-color 0.3s, background 0.3s; outline: none; box-sizing: border-box; width: 100%; }
        .submit-button { padding: 15px; border: none; border-radius: 8px; color: white; font-size: 18px; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: background 0.3s, transform 0.1s; margin-top: 10px; }
        
        /* Alertas y Errores */
        .alert-box {
            padding: 15px 20px; 
            margin-bottom: 20px; 
            border: 1px solid transparent; 
            border-radius: 8px; 
            width: 100%;
            max-width: 700px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .alert-success { background-color: rgba(76, 175, 80, 0.2); color: #81c784; border-color: #4CAF50; }
        .alert-error { background-color: rgba(244, 67, 54, 0.2); color: var(--color-error-dark); border-color: #f44336; }
        .error-message { color: var(--color-error-dark); font-size: 13px; margin-top: 5px; padding-left: 5px; }
        .form-group.error input, .form-group.error select, .form-group.error textarea { border: 2px solid var(--color-primary-red) !important; }
        
        /* Estilo para Select */
        select {
            appearance: none; 
            background-repeat: no-repeat;
            background-position: right 15px top 50%;
            background-size: 0.65em auto;
            background-image: url('data:image/svg+xml;charset=UTF8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white"><path d="M5.516 7.548l4.484 4.484 4.484-4.484z"/></svg>');
        }
        
        /* Responsividad */
        @media (max-width: 600px) {
            header { flex-direction: column; text-align: center; gap: 15px; }
            .form-section { padding: 25px; margin: 0 10px; }
            .form-row { flex-direction: column; gap: 15px; }
        }
    </style>
</head>
<body class="theme-<?php echo $current_theme; ?>">
    <div class="page-container" id="reportes-page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-file-alt"></i> ABSENTIX</h1>
            <a href="MENU.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al Menú
            </a>
        </header>

        <?php if ($success_message): ?>
            <div class="alert-box alert-success" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
                <?php if ($reporte_data): // Si tenemos la data del reporte recién guardado, mostramos el botón ?>
                    <button id="downloadPdfBtn" class="submit-button" 
                        style="width: auto; margin: 0; padding: 8px 15px; font-size: 14px; background-color: #5cb85c;">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert-box alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <main class="form-section">
            <h2 class="section-title">Registro de Reporte de Conducta</h2>
            
            <form id="reporteForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="num_control"><i class="fas fa-id-card"></i> Número de Control *</label>
                        <input type="text" id="num_control" name="num_control" required maxlength="14" 
                               placeholder="Ej: 21101000123456"
                               value="<?php echo htmlspecialchars($post_data['num_control'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="shift_display">Turno </label>
                        <input type="text" id="shift_display" value="<?php echo $user_turno; ?>" readonly style="opacity: 0.7; cursor: not-allowed !important;">
                        <input type="hidden" name="turno" value="<?php echo $user_turno; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre_s">Nombre(s) *</label>
                        <input type="text" id="nombre_s" name="nombre_s" required 
                            placeholder="Nombre(s)"
                            value="<?php echo htmlspecialchars($post_data['nombre_s'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="apellido_paterno">Apellido Paterno *</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno" required 
                            placeholder="Apellido P."
                            value="<?php echo htmlspecialchars($post_data['apellido_paterno'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="apellido_materno">Apellido Materno *</label>
                        <input type="text" id="apellido_materno" name="apellido_materno" required 
                            placeholder="Apellido M."
                            value="<?php echo htmlspecialchars($post_data['apellido_materno'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grado">Grado *</label>
                         <input type="text" id="grado" name="grado" required maxlength="1" 
                            placeholder="Ej: 3"
                            value="<?php echo htmlspecialchars($post_data['grado'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <label for="grupo">Grupo *</label>
                        <input type="text" id="grupo" name="grupo" required maxlength="1" style="text-transform: uppercase;"
                            placeholder="Ej: C"
                            value="<?php echo htmlspecialchars($post_data['grupo'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                    
                    <div class="form-group" style="flex: 2;">
                        <label for="especialidad">Especialidad *</label>
                        <input type="text" id="especialidad" name="especialidad" required 
                            placeholder="Ej: Programación, Electrónica"
                            value="<?php echo htmlspecialchars($post_data['especialidad'] ?? ''); ?>">
                        <div class="error-message"></div>
                    </div>
                </div>
                
                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 30px 0;">

                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="nombre_reportante"><i class="fas fa-user-tie"></i> Nombre del Reportante *</label>
                        <input type="text" id="nombre_reportante" name="nombre_reportante" required 
                            value="<?php echo htmlspecialchars($post_data['nombre_reportante'] ?? $_SESSION['user_name'] ?? 'Orientador'); ?>" placeholder="Tu Nombre">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="fecha_incidente"><i class="fas fa-calendar-alt"></i> Fecha del Incidente *</label>
                        <input type="date" id="fecha_incidente" name="fecha_incidente" required 
                            value="<?php echo htmlspecialchars($post_data['fecha_incidente'] ?? date('Y-m-d')); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="tipo_falta"><i class="fas fa-exclamation-triangle"></i> Tipo de Falta *</label>
                    <select id="tipo_falta" name="tipo_falta" required>
                        <option value="">Selecciona la gravedad...</option>
                        <option value="Leve" <?php echo (isset($post_data['tipo_falta']) && $post_data['tipo_falta'] == 'Leve') ? 'selected' : ''; ?>>Falta Leve</option>
                        <option value="Moderada" <?php echo (isset($post_data['tipo_falta']) && $post_data['tipo_falta'] == 'Moderada') ? 'selected' : ''; ?>>Falta Moderada</option>
                        <option value="Grave" <?php echo (isset($post_data['tipo_falta']) && $post_data['tipo_falta'] == 'Grave') ? 'selected' : ''; ?>>Falta Grave</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion_incidente"><i class="fas fa-book-open"></i> Descripción Detallada del Incidente *</label>
                    <textarea id="descripcion_incidente" name="descripcion_incidente" rows="5" required 
                        placeholder="Describe claramente el incidente, qué pasó, cuándo y dónde."><?php echo htmlspecialchars($post_data['descripcion_incidente'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-save"></i> Registrar Reporte de Conducta
                </button>
            </form>
        </main>
    </div>
    
    
    <script>
    
    // ------------------------------------------------------------------
    // LÓGICA JAVASCRIPT: PDF Y VALIDACIÓN
    // ------------------------------------------------------------------
    
    // 🚨 Pasar los datos del reporte recuperados en PHP al script
    const REPORTE_DATA = <?php echo json_encode($reporte_data); ?>;
    
    /**
     * Función para generar el PDF (adaptada de Justificantes.php)
     */
    function generatePDF(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4'); // Modo vertical para reporte
        
        // Data mapping
        const studentName = data.nombre_alumno; 
        const idNumber = data.num_control;
        const grado = data.grado;
        const grupo = data.grupo;
        const turno = data.turno; 
        const especialidad = data.especialidad;
        const fechaIncidente = data.fecha_incidente; 
        const tipoFalta = data.tipo_falta; 
        const reportante = data.nombre_reportante;
        const descripcion = data.descripcion_incidente;
        const submissionDate = new Date().toLocaleDateString('es-MX');

        let y = 20; 
        const marginX = 20;
        const lineHeight = 8;
        const pageCenter = 105;
        
        // 1. Título
        doc.setFontSize(24);
        doc.text("REPORTE DE CONDUCTA ESCOLAR", pageCenter, y, null, null, "center"); 
        doc.line(marginX, y + 2, 190, y + 2);
        y += 15;
        
        // 2. Datos del Alumno
        doc.setFontSize(14);
        doc.text("DATOS DEL ALUMNO", marginX, y); y += 6;
        doc.setFontSize(12);
        doc.text(`Nombre completo: ${studentName}`, marginX, y); 
        doc.text(`Matrícula (Control): ${idNumber}`, 120, y); y += lineHeight;

        doc.text(`Grado: ${grado}`, marginX, y); 
        doc.text(`Grupo: ${grupo}`, 60, y);
        doc.text(`Especialidad: ${especialidad}`, 100, y);
        doc.text(`Turno: ${turno}`, 160, y); 
        y += lineHeight + 5;

        // 3. Detalles del Incidente
        doc.setFontSize(14);
        doc.text("DETALLES DEL INCIDENTE", marginX, y); y += 6;
        doc.setFontSize(12);
        doc.text(`Fecha del Incidente: ${fechaIncidente}`, marginX, y); 
        doc.text(`Tipo de Falta: ${tipoFalta.toUpperCase()}`, 120, y); y += lineHeight;
        doc.text(`Reportado por: ${reportante}`, marginX, y); y += lineHeight + 5;
        
        // 4. Descripción
        doc.setFontSize(14);
        doc.text("DESCRIPCIÓN DETALLADA", marginX, y); y += 6;
        doc.setFontSize(12);
        
        // Usar splitTextToSize para manejar el texto largo
        const descriptionLines = doc.splitTextToSize(descripcion, 170); 
        doc.text(descriptionLines, marginX, y); 
        y += (descriptionLines.length * 6) + 20;
        
        // 5. Secciones de Firmas (simplificadas)
        doc.line(pageCenter - 40, y, pageCenter + 40, y);
        doc.text("Firma del Reportante (Orientador)", pageCenter, y + 5, null, null, "center");
        
        // Footer
        doc.setFontSize(8);
        doc.text(`Reporte generado el ${submissionDate}`, pageCenter, 290, null, null, "center");

        // Generar el PDF y forzar la descarga
        doc.save(`Reporte_Conducta_${idNumber}_${fechaIncidente}.pdf`);
        alert("✅ PDF generado y descargado con éxito. ¡Revisa tu carpeta de descargas!");
    }

    
    document.addEventListener('DOMContentLoaded', () => {

        // 🚨 1. Ejecución del PDF por botón MANUAL
        const downloadBtn = document.getElementById('downloadPdfBtn');
        if (downloadBtn && REPORTE_DATA) {
             downloadBtn.addEventListener('click', () => {
                 generatePDF(REPORTE_DATA);
             });
        }
        
        // 🚨 2. CONFIGURACIÓN DE VALIDACIÓN
        const inputsToValidate = [
            { id: 'nombre_s', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/, msg: 'Solo letras (Nombre).', type: 'alpha' },
            { id: 'apellido_paterno', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/, msg: 'Solo letras (Apellido Paterno).', type: 'alpha' },
            { id: 'apellido_materno', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/, msg: 'Solo letras (Apellido Materno).', type: 'alpha' },
            { id: 'especialidad', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/, msg: 'Solo letras (Especialidad).', type: 'alpha' },
            { id: 'grado', pattern: /^[1-6]$/, msg: 'Solo un número del 1 al 6.', type: 'grade' },
            { id: 'grupo', pattern: /^[A-Z]$/i, msg: 'Solo una letra (A-Z).', type: 'group' }, 
            { id: 'num_control', pattern: /^\d{14}$/, msg: 'Debe contener EXACTAMENTE 14 números.', type: 'numeric14' }
        ];

        // --------------------------------------------------------------------------------------------------
        // Lógica de validación dinámica (Asegúrate de que estas funciones existan si el código depende de ellas)
        // --------------------------------------------------------------------------------------------------
        
        // Asumiendo que las funciones displayError y clearError están definidas.
        function displayError(inputElement, message) {
            const formGroup = inputElement.closest('.form-group');
            formGroup.classList.add('error');
            formGroup.querySelectorAll('.error-message').forEach(el => el.remove()); 
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            formGroup.appendChild(errorDiv);
        }
        
        function clearError(inputElement) {
            const formGroup = inputElement.closest('.form-group');
            if (formGroup) {
                formGroup.classList.remove('error');
                formGroup.querySelectorAll('.error-message').forEach(el => el.remove()); 
            }
        }
        
        inputsToValidate.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                const validate = () => {
                    const value = input.value.trim();
                    if (value === '') { clearError(input); return; }
                    
                    let isValid = field.pattern.test(value);
                    let errorMsg = field.msg;

                    if (field.type === 'numeric14') {
                        if (/[a-zA-Z]/.test(value)) {
                            isValid = false; errorMsg = 'Se detectaron letras. Solo se permiten números.';
                        } else if (value.length !== 14) { 
                            isValid = false; errorMsg = 'Debe tener exactamente 14 dígitos numéricos.';
                        }
                    } else if (field.type === 'group') {
                            input.value = input.value.toUpperCase(); 
                            if (input.value.length !== 1 || !/^[A-Z]$/.test(input.value)) {
                                isValid = false; errorMsg = 'Solo se permite UNA ÚNICA letra mayúscula (A, B, C...).';
                            }
                    } else if (field.type === 'grade') {
                            if (input.value.length !== 1 || !/^[1-6]$/.test(input.value)) {
                                isValid = false; errorMsg = 'Solo se permite UN ÚNICO dígito numérico (1, 2, 3...).';
                            }
                    }
                    
                    if (!isValid) { displayError(input, errorMsg); } else { clearError(input); }
                };

                input.addEventListener('input', validate);
                input.addEventListener('blur', validate);
            }
        });
        
        document.getElementById('reporteForm').addEventListener('submit', function(event) {
            let hasVisualError = false;

            inputsToValidate.forEach(field => {
                const input = document.getElementById(field.id);
                if (input && input.closest('.form-group').classList.contains('error')) {
                     hasVisualError = true;
                }
            });
            
            if (hasVisualError) { 
                event.preventDefault(); 
                alert('⚠️ Por favor, corrige los errores del formulario antes de continuar.');
            }
        });

    });
    
    </script>
</body>
</html>