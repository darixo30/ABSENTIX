<?php
// Archivo: REPORTE.php (VERSIÓN SENIOR con Theming y AJAX)
session_start(); 

// 🚨 1. COMPROBACIÓN DE SESIÓN Y THEME
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: LOGIN.php");
    exit; 
}
// Obtener el turno de la sesión para el theming
$user_turno = $_SESSION['turno'] ?? 'Matutino'; 
$is_matutino = ($user_turno === 'Matutino');

// 🚨 2. MANEJO DE ERRORES/STATUS (Si se usaran redirecciones)
// Nota: Para AJAX, los errores se manejan en JS. Esto es por si se usara el status_message en el futuro.
$status_message = '';
if (isset($_SESSION['report_status'])) {
    if ($_SESSION['report_status']['status'] === 'error') {
        $status_message = '<div style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px; margin-bottom: 20px;">' . htmlspecialchars($_SESSION['report_status']['msg']) . '</div>';
    }
    unset($_SESSION['report_status']);
}

// Nota: No se requiere require_once 'conexion.php' aquí, solo en el archivo que interactúa con la DB.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX PRO - Reporte de Conducta</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
/* -------------------------------------------------------------------------- */
/* ESTILOS Y THEMES (Ajustados para PRO)                                      */
/* -------------------------------------------------------------------------- */
:root {
    --color-text-light: #fff;
    --color-text-dark: #333;
    --color-error: #ff5757;
}

/* THEME: MATUTINO (VERDE/AZUL - Profesional) */
.theme-matutino {
    --color-primary: #1e8449; /* Verde fuerte */
    --color-secondary: #2e86c1; /* Azul corporativo */
    background: linear-gradient(to top right, #d0dbd4, #a4c4b5);
    color: var(--color-text-dark);
}
.theme-matutino input, .theme-matutino select, .theme-matutino textarea {
    color: var(--color-text-dark);
    border: 1px solid rgba(0, 0, 0, 0.2);
    background: rgba(255, 255, 255, 0.5);
}
.theme-matutino select option {
    background: #d0dbd4; 
    color: var(--color-text-dark);
}
.theme-matutino .submit-button {
    color: var(--color-text-light) !important;
}
.theme-matutino label, .theme-matutino .back-link {
    color: var(--color-text-dark);
}
.theme-matutino input[type="date"]::-webkit-calendar-picker-indicator {
    filter: none; 
}


/* THEME: VESPERTINO (MORADO/GRIS - Elegante) */
.theme-vespertino {
    --color-primary: #5d54a4; /* Morado Oscuro */
    --color-secondary: #a4545d; /* Rojo Ladrillo */
    background: linear-gradient(to top right, #1c1e26, #4b3e6d);
    color: var(--color-text-light);
}
.theme-vespertino input, .theme-vespertino select, .theme-vespertino textarea {
    color: var(--color-text-light);
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1); 
}
.theme-vespertino input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1); 
}


/* ESTILOS COMUNES */
body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center; 
    padding: 20px;
    transition: background 0.5s;
    background-attachment: fixed; 
}

.logo-text i { color: var(--color-primary) !important; }
.back-link:hover { color: var(--color-primary); }
input:focus, select:focus, textarea:focus { 
    border-color: var(--color-primary) !important; 
    background: rgba(255, 255, 255, 0.2);
}
.submit-button {
    background: var(--color-primary) !important; 
    font-weight: 700;
}
.submit-button:hover { background: var(--color-secondary) !important; }

.page-container {
    /* ... (Estilos de fade-in/fade-out) */
    opacity: 0; 
    transition: opacity 0.5s ease-in-out; 
}
.page-container.fade-in { opacity: 1; }


.form-section {
    backdrop-filter: blur(15px); 
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 700px; 
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); 
    border: 1px solid rgba(255, 255, 255, 0.18); 
    /* Fondo específico para el turno */
    background: rgba(255, 255, 255, 0.05);
}

.form-row { display: flex; gap: 20px; }
.form-row .form-group { flex: 1; }

/* ERROR FEEDBACK (ROJO) */
.form-group.error input, .form-group.error select, .form-group.error textarea { 
    border: 2px solid var(--color-error) !important; 
}
.error-message { 
    color: var(--color-error); 
    margin-top: 5px; 
    font-weight: bold; 
    font-size: 12px;
}

/* El resto de tus estilos CSS (header, label, input, etc.) deben ir aquí, adaptando colores */

</style>
</head>
<body class="theme-<?php echo strtolower($user_turno); ?>">
    <div class="page-container" id="reporte-page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-exclamation-triangle"></i> ABSENTIX PRO</h1>
            <a href="MENU.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al Inicio (Turno: **<?php echo $user_turno; ?>**)
            </a>
        </header>
        
        <main class="form-section">
            <h2 class="section-title">Reporte de Conducta</h2>
            <?php echo $status_message; // Mensaje de error/éxito (si se usara sin AJAX) ?>
            
            <form id="reporteForm" class="reporte-form"> 
                
                <div class="form-group">
                    <label for="studentName">Nombre Completo del Alumno **(Solo Letras)**</label>
                    <input type="text" id="studentName" name="studentName" placeholder="Ej: Juan Pérez García" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grade">Grado **(Ej: 3°)**</label>
                        <input type="text" id="grade" name="grade" placeholder="Ej: 3°" required>
                    </div>
                    <div class="form-group">
                        <label for="group">Grupo **(Ej: C)**</label>
                        <input type="text" id="group" name="group" placeholder="Ej: C" required>
                    </div>
                    <div class="form-group">
                        <label for="shift">Turno **(Fijo por Sesión)**</label>
                        <input type="text" id="shift" name="shift" value="<?php echo $user_turno; ?>" readonly style="cursor: not-allowed;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="specialty">Especialidad **(Solo Letras)**</label>
                        <input type="text" id="specialty" name="specialty" placeholder="Ej: Programación" required>
                    </div>
                    <div class="form-group">
                        <label for="idNumber">Número de Control **(Solo Números, 8 o 14 dígitos)**</label>
                        <input type="text" id="idNumber" name="idNumber" placeholder="Ej: 12345678" required>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">

                <div class="form-row">
                    <div class="form-group">
                        <label for="reportDate">Fecha del Incidente</label>
                        <input type="date" id="reportDate" name="reportDate" required>
                    </div>
                    <div class="form-group">
                        <label for="reporterName">Nombre Completo del Profesor/Reportante **(Solo Letras)**</label>
                        <input type="text" id="reporterName" name="reporterName" placeholder="Ej: Profr. Ana López" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="misconductType">Tipo de Falta</label>
                    <select id="misconductType" name="misconductType" required>
                        <option value="">Selecciona el tipo de falta...</option>
                        <option value="leve">Falta Leve (Ej: Retardo constante, uso de celular)</option>
                        <option value="moderada">Falta Moderada (Ej: Desacato, lenguaje inapropiado)</option>
                        <option value="grave">Falta Grave (Ej: Pelea, daño a instalaciones, acoso)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="misconductDetails">Descripción Detallada del Incidente **(Mínimo 20 Caracteres)**</label>
                    <textarea id="misconductDetails" name="misconductDetails" rows="4" placeholder="Describe claramente lo sucedido, el lugar y los testigos (si los hay)." required></textarea>
                </div>

                <button type="submit" class="submit-button" id="submitFormButton">
                    <i class="fas fa-file-pdf"></i> Generar y Guardar Reporte
                </button>
            </form>
        </main>
    </div>
    
    
    <script>
    
    // ------------------------------------------------------------------
    // LÓGICA JAVASCRIPT: VALIDACIÓN, AJAX Y PDF
    // ------------------------------------------------------------------
    
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar Fade-In
        const container = document.getElementById('reporte-page-container');
        if (container) {
            container.classList.add('fade-in'); 
        }

        // Aplicar validación en tiempo real a los campos sensibles
        setupRealTimeValidation();
        
        // Listener principal para el envío del formulario
        document.getElementById('reporteForm').addEventListener('submit', handleFormSubmission);
    });

    /**
     * Configura la validación en tiempo real para campos específicos.
     */
    function setupRealTimeValidation() {
        const inputsToValidate = [
            { id: 'studentName', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/, msg: 'Solo letras y espacios (Nombre).', minLength: 5 },
            { id: 'reporterName', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/, msg: 'Solo letras y espacios (Reportante).', minLength: 5 },
            { id: 'specialty', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, msg: 'Solo letras y espacios (Especialidad).', minLength: 2 },
            // Permite 8 o 14 dígitos. Si detecta una letra, marca error.
            { id: 'idNumber', pattern: /^(\d{8}|\d{14})$/, msg: 'Debe contener 8 o 14 números exactos.', type: 'numeric' },
            { id: 'misconductDetails', minLength: 20, msg: 'La descripción debe tener al menos 20 caracteres.', type: 'text' }
        ];

        inputsToValidate.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                const validate = () => {
                    const value = input.value.trim();
                    let errorMessage = null;

                    if (value === '') {
                        clearError(input);
                        return;
                    }

                    // 1. Validar Patrón (para texto y numérico)
                    if (field.pattern && !field.pattern.test(value)) {
                        errorMessage = field.msg;
                    }
                    
                    // 2. Validar Longitud Mínima
                    if (field.minLength && value.length < field.minLength && !errorMessage) {
                         errorMessage = `Mínimo ${field.minLength} caracteres.`;
                    }
                    
                    if (errorMessage) {
                        displayError(input, errorMessage);
                    } else {
                        clearError(input);
                    }
                };

                input.addEventListener('input', validate);
                input.addEventListener('blur', validate);
            }
        });
    }

    /**
     * Maneja el envío del formulario: valida, hace AJAX y genera PDF.
     */
    async function handleFormSubmission(event) {
        event.preventDefault(); 
        
        if (!validateForm(true)) { // Ejecutar validación final
            alert('¡ALTO! Corrige los campos marcados en rojo antes de enviar. La información es obligatoria.');
            return;
        }

        const form = event.target;
        const formData = new FormData(form);
        const submitButton = document.getElementById('submitFormButton');
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando y Generando...';
        
        try {
            // 1. Envío de datos por AJAX al backend (GUARDAR_REPORTE_AJAX.PHP)
            const response = await fetch('GUARDAR_REPORTE_AJAX.PHP', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert('¡Éxito! El reporte se guardó correctamente en la Base de Datos. Generando PDF...');
                
                // 2. Generar y descargar el PDF con los datos del formulario (ya validados)
                const dataForPdf = Object.fromEntries(formData.entries());
                generatePDF(dataForPdf);

            } else {
                // Error del servidor (ej: fallo de DB)
                alert('❌ ERROR AL GUARDAR EN EL SERVIDOR: ' + (result.msg || 'Error desconocido.'));
            }

        } catch (error) {
            console.error('Error de red o procesamiento:', error);
            alert('⚠️ Error de conexión o procesamiento. Revisa la consola para más detalles.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-file-pdf"></i> Generar y Guardar Reporte';
        }
    }

    /**
     * Realiza la validación final del formulario.
     */
    function validateForm(isSubmit = false) {
        let isValid = true;
        
        if (isSubmit) {
             // Limpiar todos los errores visuales antes de revalidar
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.form-group.error').forEach(el => el.classList.remove('error'));
        }

        const fields = [
            { id: 'studentName', minLength: 5, msg: 'Nombre completo (mínimo 5 letras).', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/ },
            { id: 'grade', minLength: 1, msg: 'Grado obligatorio.', pattern: /^.+$/ },
            { id: 'group', minLength: 1, msg: 'Grupo obligatorio.', pattern: /^.+$/ },
            { id: 'specialty', minLength: 2, msg: 'Especialidad obligatoria (mínimo 2 letras).', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/ },
            { id: 'idNumber', msg: 'Número de Control (8 o 14 números).', pattern: /^(\d{8}|\d{14})$/ },
            { id: 'reportDate', msg: 'Fecha del incidente obligatoria.', pattern: /^.+$/ },
            { id: 'reporterName', minLength: 5, msg: 'Nombre del reportante (mínimo 5 letras).', pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\.]+$/ },
            { id: 'misconductType', msg: 'Tipo de falta obligatoria.', pattern: /^.+$/ },
            { id: 'misconductDetails', minLength: 20, msg: 'Descripción detallada (mínimo 20 caracteres).', pattern: /^.+$/ },
        ];

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                const value = input.value.trim();
                let error = null;

                if (!field.pattern.test(value)) {
                    error = field.msg;
                }
                
                if (field.minLength && value.length < field.minLength && !error) {
                    error = `Mínimo ${field.minLength} caracteres.`;
                }

                if (error) {
                    displayError(input, error);
                    isValid = false;
                } else if (isSubmit) {
                    clearError(input);
                }
            }
        });

        return isValid;
    }

    // Helper functions (mantener para feedback visual)
    function displayError(inputElement, message) {
        const formGroup = inputElement.closest('.form-group');
        formGroup.classList.add('error');
        // Quitar mensajes de error previos
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
    // FUNCIÓN DE GENERACIÓN DE PDF
    // ------------------------------------------------------------------
    function generatePDF(data) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); 
        
        // Recolección de datos
        const studentName = data.studentName;
        const grade = data.grade;
        const group = data.group;
        const shift = data.shift; 
        const specialty = data.specialty;
        const idNumber = data.idNumber;
        const reportDate = data.reportDate;
        const reporterName = data.reporterName;
        
        const misconductTypeSelect = document.getElementById('misconductType');
        const misconductTypeText = misconductTypeSelect.options[misconductTypeSelect.selectedIndex].text;
        
        const misconductDetails = data.misconductDetails;
        
        const formattedDate = reportDate.split('-').reverse().join('/');

        let y = 20;
        const lineHeight = 8;
        const margin = 15;
        const maxWidth = 260; 
        const pageCenter = 148.5; 
        
        // Fuente y Estilo (Simulación de "Times-Bold" o similar para títulos)
        doc.setFont('Helvetica', 'bold');

        // Título
        doc.setFontSize(26);
        doc.text("REPORTE DE CONDUCTA ESCOLAR", pageCenter, y, null, null, "center");
        doc.line(margin, y + 5, 282, y + 5);
        y += 15;
        
        
        // Sección: DATOS DEL ALUMNO
        doc.setFontSize(16);
        doc.text("DATOS DEL ALUMNO REPORTADO", margin, y); y += lineHeight + 2;
        doc.line(margin, y, 282, y); y += 5;
        doc.setFontSize(12);
        doc.setFont('Helvetica', 'normal');
        
        // Fila 1
        doc.text(`ALUMNO(A): ${studentName}`, margin, y);
        doc.text(`NÚMERO DE CONTROL: ${idNumber}`, 160, y); y += lineHeight;
        
        // Fila 2
        doc.text(`GRADO Y GRUPO: ${grade}° ${group}`, margin, y);
        doc.text(`TURNO: ${shift}`, 160, y); 
        y += lineHeight;

        // Fila 3
        doc.text(`ESPECIALIDAD: ${specialty}`, margin, y);
        y += lineHeight * 2;
        
        // Sección: DETALLES DEL INCIDENTE
        doc.setFontSize(16);
        doc.setFont('Helvetica', 'bold');
        doc.text("DETALLES DEL INCIDENTE", margin, y); y += lineHeight + 2;
        doc.line(margin, y, 282, y); y += 5;
        doc.setFontSize(12);
        doc.setFont('Helvetica', 'normal');
        
        // Fila 1
        doc.text(`FECHA DEL INCIDENTE: ${formattedDate}`, margin, y);
        doc.text(`TIPO DE FALTA: ${misconductTypeText.toUpperCase()}`, 160, y); y += lineHeight;
        
        // Fila 2
        doc.text(`PROFESOR/REPORTANTE: ${reporterName}`, margin, y); y += lineHeight * 2;

        // Sección: DESCRIPCIÓN
        doc.setFontSize(16);
        doc.setFont('Helvetica', 'bold');
        doc.text("DESCRIPCIÓN DETALLADA:", margin, y); y += lineHeight;
        doc.setFontSize(12);
        doc.setFont('Helvetica', 'normal');
        
        // Auto-wrap del texto
        const splitDetails = doc.splitTextToSize(misconductDetails, maxWidth); 
        doc.text(splitDetails, margin, y); 
        y += (splitDetails.length * 6) + lineHeight * 3; 

    
        // Sección: FIRMAS
        const signatureY = y;
        const signatureX1 = margin + 10;
        const signatureX2 = signatureX1 + 100;
        const signatureX3 = signatureX2 + 100;

        doc.line(signatureX1 - 5, signatureY, signatureX1 + 75, signatureY); 
        doc.line(signatureX2 - 5, signatureY, signatureX2 + 75, signatureY); 
        doc.line(signatureX3 - 5, signatureY, signatureX3 + 75, signatureY); 
        y += 5;
        doc.setFontSize(10);
        doc.text("Firma del Profesor/Reportante", signatureX1, y); 
        doc.text("Firma del Alumno", signatureX2 + 10, y); 
        doc.text("Firma de Dirección/Control Escolar", signatureX3 - 10, y); 

        // Guardar el PDF
        doc.save(`Reporte_Conducta_${idNumber}_${formattedDate.replace(/\//g, '-')}.pdf`);
    }
    </script>
</body>
</html>