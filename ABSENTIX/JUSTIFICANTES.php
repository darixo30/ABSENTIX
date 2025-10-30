<?php
// Archivo: JUSTIFICANTES.php (Diseño Dark Glassmorphism UNIFICADO y No. Control a 14 Dígitos)
session_start(); 

// 🚨 1. COMPROBACIÓN DE SESIÓN
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: LOGIN.php");
    exit;
}
// Obtener el turno de la sesión. 
$user_turno = $_SESSION['turno'] ?? 'Vespertino'; 
$current_theme = strtolower($user_turno); // 'vespertino' o 'matutino'

// Asumiendo que estos archivos y funciones existen en tu entorno:
require_once 'conexion.php'; 

// 💡 CONEXIÓN: Se establece la conexión que debe estar definida en conexion.php
// Usamos try/catch para manejar la excepción de conexión si falla
try {
    $conexion = conectarBD(); 
} catch (Exception $e) {
    // Si la conexión falla, establecemos $conexion a null y mostramos el error.
    $conexion = null;
    $error_from_server = "Error al conectar con la BD: " . $e->getMessage();
}


// 🚨 2. MANEJO DE ERRORES DE VALIDACIÓN DEL BACKEND
$validation_errors = $_SESSION['validation_errors'] ?? [];
$post_data = $_SESSION['post_data'] ?? []; 
unset($_SESSION['validation_errors']);
unset($_SESSION['post_data']);

// LÓGICA DE RECUPERACIÓN DE DATOS PARA EL PDF (Mantenida)
$justificante_data = null;
$error_from_server = $error_from_server ?? null; // Si ya falló la conexión, mantenemos ese error.

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success' && isset($_GET['id']) && $conexion) {
        $id_justificante = intval($_GET['id']);
        
        $sql_fetch = "
            SELECT 
                j.fechas_ausencia, j.motivo, j.notas_adicionales, j.num_control,
                a.nombre_completo, a.grado, a.grupo, a.especialidad, a.turno 
            FROM justificantes j
            JOIN alumnos a ON j.num_control = a.num_control
            WHERE j.id_justificante = ?
        ";
        
        if ($stmt = $conexion->prepare($sql_fetch)) {
            $stmt->bind_param("i", $id_justificante);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $justificante_data = $result->fetch_assoc();
            }
            $stmt->close();
        } else {
            // Manejo de error si la preparación falla
            $error_from_server = "Error de preparación de consulta SQL: " . $conexion->error;
        }
        // Aseguramos el cierre de la conexión después de usarla
        if (function_exists('cerrarConexion')) {
            cerrarConexion($conexion);
        }

    } else if ($_GET['status'] === 'error_500' && isset($_GET['msg'])) {
        $error_from_server = urldecode($_GET['msg']);
    }
} else {
    // Si no hay redirección, solo cerramos la conexión después de la inicialización
    if (isset($conexion) && function_exists('cerrarConexion')) {
         cerrarConexion($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX </title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
/* -------------------------------------------------------------------------- */
/* Variables de Diseño Globales */
/* -------------------------------------------------------------------------- */
:root {
    --color-primary-red: #9d5353;
    --color-text-dark: #333;
    --color-error-dark: #ffcccc; /* Error claro sobre fondo oscuro */
    --font-logo: 'Playwrite US Modern', cursive;
    --font-main: 'Alan Sans', sans-serif;
    /* Estilos del diseño Dark Glassmorphism para reuso */
    --bg-gradient: linear-gradient(to top, #1c1e26, #263248);
    --bg-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
    --glass-bg: rgba(255, 255, 255, 0.05);
    --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 30px rgba(255, 255, 255, 0.3);
    --glass-border: 1px solid rgba(255, 255, 255, 0.18);
    --input-bg: rgba(255, 255, 255, 0.1);
    --input-border: 1px solid rgba(255, 255, 255, 0.3);
}

/* -------------------------------------------------------------------------- */
/* 🌙 TEMA VESPERTINO (DARK GLASSMORPHISM) - Base */
/* -------------------------------------------------------------------------- */
.theme-vespertino {
    color: #fff;
    background: var(--bg-gradient);
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}
.theme-vespertino .page-container {
    background: rgba(0, 0, 0, 0.4);
}
/* Estilos específicos del tema oscuro */
.theme-vespertino .logo-text i { color: var(--color-primary-red); }
.theme-vespertino .section-title { border-bottom: 2px solid var(--color-primary-red); }
.theme-vespertino .form-section {
    background: var(--glass-bg);
    box-shadow: var(--glass-shadow);
    border: var(--glass-border);
}
.theme-vespertino label { color: #ccc; }
.theme-vespertino input, .theme-vespertino select, .theme-vespertino textarea {
    background: var(--input-bg);
    color: #fff;
    border: var(--input-border);
}
.theme-vespertino input:focus, .theme-vespertino select:focus, .theme-vespertino textarea:focus {
    border-color: var(--color-primary-red); 
    background: rgba(255, 255, 255, 0.15);
}
.theme-vespertino select option { background: #263248; color: #fff; }
.theme-vespertino .submit-button { background: var(--color-primary-red); }
.theme-vespertino .submit-button:hover { background: #7a4242; }
.theme-vespertino .error-message { color: var(--color-error-dark); }
.theme-vespertino .form-group.error input, .theme-vespertino .form-group.error select, .theme-vespertino .form-group.error textarea { 
    border: 2px solid var(--color-primary-red) !important; 
}


/* -------------------------------------------------------------------------- */
/* ☀️ TEMA MATUTINO (FORZADO A DARK GLASSMORPHISM) - ¡Mismo diseño! */
/* -------------------------------------------------------------------------- */
.theme-matutino {
    /* MISMO FONDO */
    color: #fff;
    background: var(--bg-gradient);
    background-image: var(--bg-image);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}
.theme-matutino .page-container {
    /* MISMO EFECTO DE CUBRIMIENTO */
    background: rgba(0, 0, 0, 0.4);
}
/* MISMOS ESTILOS */
.theme-matutino .logo-text i { color: var(--color-primary-red); }
.theme-matutino .section-title { color: #ffffffff; border-bottom: 2px solid var(--color-primary-red); }
.theme-matutino .form-section {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    box-shadow: var(--glass-shadow);
    border: var(--glass-border);
}
.theme-matutino label { color: #ccc; }
.theme-matutino input, .theme-matutino select, .theme-matutino textarea {
    background: var(--input-bg);
    color: #fff;
    border: var(--input-border);
}
.theme-matutino input:focus, .theme-matutino select:focus, .theme-matutino textarea:focus {
    border-color: var(--color-primary-red); 
    background: rgba(255, 255, 255, 0.15);
}
.theme-matutino select option { background: #263248; color: #fff; }
.theme-matutino .submit-button { background: var(--color-primary-red); }
.theme-matutino .submit-button:hover { background: #7a4242; }
.theme-matutino .error-message { color: var(--color-error-dark); }
.theme-matutino .form-group.error input, .theme-matutino .form-group.error select, .theme-matutino .form-group.error textarea { 
    border: 2px solid var(--color-primary-red) !important; 
}


/* -------------------------------------------------------------------------- */
/* ESTILOS COMUNES DE LAYOUT Y TRANSICIÓN */
/* -------------------------------------------------------------------------- */
body {
    margin: 0;
    font-family: var(--font-main);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.page-container {
    flex: 1; 
    display: flex;
    flex-direction: column;
    align-items: center; 
    padding: 20px 0; 
    opacity: 0; 
    transition: opacity 0.5s ease-in-out; 
}
.page-container.fade-in { opacity: 1; }
.page-container.fade-out { opacity: 0; pointer-events: none; }

header {
    width: 100%;
    max-width: 700px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 20px;
}
.logo-text {
    font-family: var(--font-logo);
    font-size: 28px;
    margin: 0;
}
.back-link {
    text-decoration: none;
    font-size: 16px;
    transition: 0.3s;
    color: #ccc; /* Ajuste para el tema oscuro */
}
.back-link:hover {
    color: #fff;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}
.back-link i { margin-right: 5px; }

.form-section {
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 700px; 
}

.section-title {
    font-family: var(--font-logo);
    font-size: 32px;
    margin-bottom: 30px;
    text-align: center;
    padding-bottom: 10px;
    display: block; 
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}
.form-row .form-group { flex: 1; }
label {
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
}
input, select, textarea {
    padding: 12px 15px;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s, background 0.3s;
    outline: none;
    box-sizing: border-box;
    width: 100%;
}
#shift_display {
    opacity: 0.7;
    cursor: not-allowed !important;
}

/* Estilo para Select (flecha) - Siempre blanca para el tema oscuro unificado */
select {
    appearance: none; 
    background-repeat: no-repeat;
    background-position: right 15px top 50%;
    background-size: 0.65em auto;
    /* SVG blanco para el tema oscuro */
    background-image: url('data:image/svg+xml;charset=UTF8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white"><path d="M5.516 7.548l4.484 4.484 4.484-4.484z"/></svg>');
}

.submit-button {
    padding: 15px;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    transition: background 0.3s, transform 0.1s;
    margin-top: 10px;
}
.submit-button i { margin-right: 8px; }

.alert-box {
    padding: 15px 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    font-weight: 700;
    font-size: 16px;
    border: 1px solid transparent;
    width: 100%;
    max-width: 700px;
    box-sizing: border-box;
}
.alert-error-server {
    background-color: rgba(157, 83, 83, 0.2); 
    color: var(--color-error-dark);
    border-color: var(--color-primary-red);
}

/* Media Queries (Responsividad) */
@media (max-width: 600px) {
    header { flex-direction: column; text-align: center; gap: 15px; }
    .form-section { padding: 25px; margin: 0 10px; }
    .form-row { flex-direction: column; gap: 15px; }
    .logo-text { font-size: 24px; }
    .page-container { padding: 10px; }
}
</style>
</head>
<body class="theme-<?php echo $current_theme; ?>">
    <div class="page-container" id="justificante-page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-file-invoice"></i> ABSENTIX </h1>
            <a href="MENU.php" class="back-link" onclick="handlePageTransition(event, this.href)">
                <i class="fas fa-arrow-left"></i> Volver al Inicio 
            </a>
        </header>
        
        <?php if ($error_from_server): ?>
            <div class="alert-box alert-error-server">
                <i class="fas fa-exclamation-circle"></i> Error de Servidor: <?php echo htmlspecialchars($error_from_server); ?>
            </div>
        <?php endif; ?>

        <main class="form-section">
            <h2 class="section-title">Generar Justificante Escolar</h2>
            
            <form id="justificanteForm" action="guardar_justificante.php" method="POST" enctype="multipart/form-data" class="justificante-form"> 
                
                <div class="form-row">
                    <div class="form-group" style="flex: 2; <?php echo isset($validation_errors['studentName']) ? 'error' : ''; ?>">
                        <label for="studentName">Nombre Completo (Incluyendo Apellidos)</label>
                        <input type="text" id="studentName" name="studentName" placeholder="Ej: Juan Antonio Pérez García" required value="<?php echo htmlspecialchars($post_data['studentName'] ?? ''); ?>">
                        <?php if(isset($validation_errors['studentName'])): ?>
                            <div class="error-message"><?php echo $validation_errors['studentName']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?php echo isset($validation_errors['idNumber']) ? 'error' : ''; ?>" style="flex: 1;">
                        <label for="idNumber">Número de Control (14 Dígitos)</label>
                        <input type="text" id="idNumber" name="idNumber" placeholder="Ej: 01234567890123" required maxlength="14" value="<?php echo htmlspecialchars($post_data['idNumber'] ?? ''); ?>">
                        <?php if(isset($validation_errors['idNumber'])): ?>
                            <div class="error-message"><?php echo $validation_errors['idNumber']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group <?php echo isset($validation_errors['grade']) ? 'error' : ''; ?>">
                        <label for="grade">Grado </label>
                        <input type="text" id="grade" name="grade" placeholder="Ej: 3" required value="<?php echo htmlspecialchars($post_data['grade'] ?? ''); ?>" maxlength="1">
                         <?php if(isset($validation_errors['grade'])): ?>
                            <div class="error-message"><?php echo $validation_errors['grade']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?php echo isset($validation_errors['group']) ? 'error' : ''; ?>">
                        <label for="group">Grupo </label>
                        <input type="text" id="group" name="group" placeholder="Ej: C" required value="<?php echo htmlspecialchars($post_data['group'] ?? ''); ?>" maxlength="1" style="text-transform: uppercase;">
                        <?php if(isset($validation_errors['group'])): ?>
                            <div class="error-message"><?php echo $validation_errors['group']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group <?php echo isset($validation_errors['specialty']) ? 'error' : ''; ?>">
                        <label for="specialty">Especialidad </label>
                        <input type="text" id="specialty" name="specialty" placeholder="Ej: Programación" required value="<?php echo htmlspecialchars($post_data['specialty'] ?? ''); ?>">
                        <?php if(isset($validation_errors['specialty'])): ?>
                            <div class="error-message"><?php echo $validation_errors['specialty']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="shift_display">Turno (Fijo) </label>
                        <input type="text" id="shift_display" value="<?php echo $user_turno; ?>" readonly>
                        <input type="hidden" name="shift" value="<?php echo $user_turno; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="absenceDateStart">Fecha de Inicio de Ausencia</label>
                        <input type="date" id="absenceDateStart" name="absenceDateStart" required value="<?php echo htmlspecialchars($post_data['absenceDateStart'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="absenceDateEnd">Fecha de Fin de Ausencia</label>
                        <input type="date" id="absenceDateEnd" name="absenceDateEnd" required value="<?php echo htmlspecialchars($post_data['absenceDateEnd'] ?? ''); ?>">
                    </div>
                    <input type="hidden" id="absenceDates" name="absenceDates" value=""> 
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="reason">Motivo de la Ausencia</label>
                        <select id="reason" name="reason" required>
                            <option value="">Selecciona un motivo...</option>
                            <option value="medico" <?php echo (isset($post_data['reason']) && $post_data['reason'] == 'medico') ? 'selected' : ''; ?>>Cita/Enfermedad Médica</option>
                            <option value="familiar" <?php echo (isset($post_data['reason']) && $post_data['reason'] == 'familiar') ? 'selected' : ''; ?>>Motivo Familiar (Defunción/Evento)</option>
                            <option value="oficial" <?php echo (isset($post_data['reason']) && $post_data['reason'] == 'oficial') ? 'selected' : ''; ?>>Trámite Oficial/Legal</option>
                            <option value="otro" <?php echo (isset($post_data['reason']) && $post_data['reason'] == 'otro') ? 'selected' : ''; ?>>Otro (Especificar en notas)</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="supportDoc">Documento de Soporte (Opcional)</label>
                        <input type="file" id="supportDoc" name="supportDoc" accept=".pdf, .jpg, .png">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notas Adicionales (Opcional)</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Detalles extra sobre el justificante..."><?php echo htmlspecialchars($post_data['notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-file-pdf"></i> Generar y Guardar Justificante
                </button>
            </form>
        </main>
    </div>
    
    
    <script>
    
    // ------------------------------------------------------------------
    // LÓGICA JAVASCRIPT: TRANSICIÓN, VALIDACIÓN Y PDF
    // ------------------------------------------------------------------
    
    const JUSTIFICANTE_DATA = <?php echo json_encode($justificante_data); ?>;
    const URL_STATUS = new URLSearchParams(window.location.search).get('status');
    
    /**
     * Función para la transición de página (fade-out)
     */
    function handlePageTransition(event, url) {
        event.preventDefault(); 
        const pageContainer = document.querySelector('.page-container');
        if (pageContainer) {
            pageContainer.classList.add('fade-out');
            setTimeout(() => {
                window.location.href = url;
            }, 500); 
        } else {
            window.location.href = url;
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Transición Fade-in al cargar
        const container = document.getElementById('justificante-page-container');
        if (container) {
            container.classList.remove('fade-out'); 
            container.classList.add('fade-in'); 
        }

        // 2. Manejo de PDF/Errores 
        if (URL_STATUS === 'success' && JUSTIFICANTE_DATA) {
            generatePDF(JUSTIFICANTE_DATA);
        }
        if (URL_STATUS) {
            // Limpia la URL sin recargar para evitar descargas repetidas
            history.replaceState(null, null, 'JUSTIFICANTES.php'); 
        }

        // 🚨 3. VALIDACIÓN JS EN TIEMPO REAL
        const inputsToValidate = [
            // CAMPO UNIFICADO
            { id: 'studentName', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/, msg: 'Solo letras y espacios (Nombre Completo).', type: 'alpha_full' }, 
            
            // Validaciones Estrictas 
            { id: 'grade', pattern: /^[1-6]$/, msg: 'Solo un número (1 a 6).', type: 'grade' }, 
            { id: 'group', pattern: /^[A-Z]$/i, msg: 'Solo una letra (A-Z).', type: 'group' }, 
            // 💥 CORRECCIÓN CRÍTICA: PATRÓN DE 14 DÍGITOS 💥
            { id: 'idNumber', pattern: /^\d{14}$/, msg: 'Debe contener exactamente 14 números.', type: 'numeric14' }, 
            { id: 'specialty', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, msg: 'Solo letras (Especialidad).', type: 'alpha' }
        ];

        inputsToValidate.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                const validate = () => {
                    const value = input.value.trim();
                    if (value === '') { clearError(input); return; }
                    
                    let isValid = field.pattern.test(value);
                    let errorMsg = field.msg;

                    if (field.type === 'numeric14') { // Corregido a numeric14
                        if (/[a-zA-Z]/.test(value)) {
                            isValid = false; errorMsg = 'Se detectaron letras. Solo se permiten números.';
                        } else if (value.length !== 14) { // Corregido a 14
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

        // 🚨 4. PREVENCIÓN DE ENVÍO Y MANEJO DE FECHAS
        document.getElementById('justificanteForm').addEventListener('submit', function(event) {
            let hasVisualError = false;

            // Re-validación final de todos los campos
            inputsToValidate.forEach(field => {
                const input = document.getElementById(field.id);
                if (input) {
                    // Si el campo tiene la clase 'error' del JS, detenemos el envío.
                    if (input.closest('.form-group').classList.contains('error')) {
                         hasVisualError = true;
                    }
                    // Forzar revalidación si está vacío
                    if (input.required && input.value.trim() === '') {
                        hasVisualError = true; 
                    }
                }
            });
            
            // Validar Fechas (Start <= End)
            const dateStart = document.getElementById('absenceDateStart').value;
            const dateEnd = document.getElementById('absenceDateEnd').value;
            const absenceDatesHidden = document.getElementById('absenceDates');
            
            if (dateStart && dateEnd && dateStart > dateEnd) {
                alert('⚠️ Error: La Fecha de Inicio de Ausencia NO puede ser posterior a la Fecha de Fin de Ausencia.');
                event.preventDefault();
                hasVisualError = true;
            } else if (dateStart && dateEnd) {
                // UNIFICAR LAS FECHAS EN EL CAMPO OCULTO para que el backend lo pueda leer
                absenceDatesHidden.value = `Desde: ${dateStart} hasta: ${dateEnd}`;
            } else {
                 // Si faltan fechas, el campo oculto estará vacío, lo cual lo marca como error en el backend
                 absenceDatesHidden.value = '';
            }


            if (hasVisualError) { 
                event.preventDefault(); 
                alert('⚠️ Por favor, corrige los errores del formulario antes de continuar.');
            }
        });
    });

    // Helper functions (errores)
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

    // ------------------------------------------------------------------
    // FUNCIÓN DE GENERACIÓN DE PDF (Mantenida sin cambios)
    // ------------------------------------------------------------------
    function generatePDF(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); 
        
        // Datos del Justificante para el PDF
        const studentName = data.nombre_completo; 
        const idNumber = data.num_control;
        const grado = data.grado;
        const grupo = data.grupo;
        const especialidad = data.especialidad;
        const shiftText = data.turno; 
        
        const absenceDates = data.fechas_ausencia; 
        const reasonText = data.motivo; 
        const notes = data.notas_adicionales || "No hay notas adicionales.";
        const submissionDate = new Date().toLocaleDateString('es-MX');

        let y = 20; 
        const marginX = 15;
        const lineHeight = 8;
        const tableRowHeight = 7;
        const tableNumRows = 8;
        const pageCenter = 148.5;

        // 1. Título
        doc.setFontSize(26);
        doc.text("JUSTIFICANTE ESCOLAR", pageCenter, y, null, null, "center"); 
        y += 15;
        
        // 2. Datos del Alumno
        doc.setFontSize(12);
        doc.text(`Nombre completo: ${studentName}`, marginX, y); 
        doc.text(`Matrícula (Control): ${idNumber}`, 180, y); y += lineHeight;

        doc.text(`Grado: ${grado}`, marginX, y); 
        doc.text(`Grupo: ${grupo}`, 70, y);
        doc.text(`Especialidad: ${especialidad}`, 140, y);
        doc.text(`Turno: ${shiftText}`, 230, y); 
        y += lineHeight;

        doc.line(marginX, y, 280, y); 
        y += 5;
        
        // 3. Detalles de Justificación
        doc.setFontSize(14);
        doc.text("DETALLES DE LA INASISTENCIA", marginX, y); y += lineHeight;
        doc.setFontSize(12);
        doc.text(`Fechas a justificar: ${absenceDates}`, marginX, y); y += lineHeight;
        doc.text(`Motivo: ${reasonText}`, marginX, y); y += lineHeight;
        
        doc.text("Notas Adicionales:", marginX, y);
        const notesLines = doc.splitTextToSize(notes, 260); 
        doc.text(notesLines, marginX + 30, y); 
        y += (notesLines.length * 6) + 10; 
        
        // 4. Tabla de Firmas de Maestros
        doc.setFontSize(14);
        doc.text("Firmas de Maestros", marginX, y); y += 5;
        doc.setFontSize(10);

        let tableStartX = marginX;
        let tableStartY = y;
        
        // Encabezados
        doc.rect(tableStartX, y, 130, tableRowHeight); 
        doc.text("Materia/Maestro(a)", tableStartX + 2, y + 5);
        doc.rect(tableStartX + 130, y, 50, tableRowHeight);
        doc.text("Firma", tableStartX + 130 + 2, y + 5);
        y += tableRowHeight;

        // Filas para firmas
        for (let i = 1; i <= tableNumRows; i++) {
            doc.rect(tableStartX, y, 130, tableRowHeight);
            doc.text(`${i}.`, tableStartX + 2, y + 5);
            doc.rect(tableStartX + 130, y, 50, tableRowHeight);
            y += tableRowHeight;
        }
        
        // 5. Secciones de Firma y Entrega
        y = tableStartY + (tableNumRows * tableRowHeight) + 15;
        
        doc.line(tableStartX + 190, y, tableStartX + 270, y); y += 5;
        doc.setFontSize(12);
        doc.text("Firma del padre, madre o tutor", tableStartX + 200, y); y += lineHeight;
        
        doc.text(`Fecha de entrega del justificante: ${submissionDate}`, tableStartX + 190, y); 
        
        // Generar el PDF y forzar la descarga
        doc.save(`Justificante_${idNumber}.pdf`);
        alert("✅ ¡Éxito! Justificante guardado en la BD y descargado. Por favor, imprime el archivo para su llenado.");
    }
    
    </script>
</body>
</html>