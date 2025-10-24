<?php
// REPORTE.php - Verificación de Sesión

// 1. Inicia la sesión de PHP
session_start();

// 2. Verifica si el usuario NO está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    
    // Si no está logueado, redirige al login
    header("location: LOGIN.php");
    exit; // Detiene la ejecución del script para evitar que se cargue la página
}

// El resto de tu código PHP y HTML de la página de reportes va aquí...
// ...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Generar Reporte de Conducta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* (El CSS es el mismo que proporcionaste. Se omite aquí por brevedad) */
@import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');

body {
    margin: 0;
    font-family: "Alan Sans", sans-serif;
    color: #fff;
    background: linear-gradient(to top, #1c1e26, #263248);
    background-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed; 
}


.page-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center; 
    padding: 20px;
    background: rgba(0, 0, 0, 0.4);
    /* MODIFICACIÓN CLAVE PARA EL FADE-IN */
    opacity: 0; 
    transition: opacity 0.5s ease-in-out; 
}

.page-container.fade-out {
    opacity: 0;
    pointer-events: none; 
}

.page-container.fade-in {
    opacity: 1;
}

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
    font-size: 28px;
    color: #fff;
    margin: 0;
}

.logo-text i {
    margin-right: 10px;
    color: #9d5353; 
}

.back-link {
    text-decoration: none;
    color: #ccc;
    font-size: 16px;
    transition: 0.3s;
}

.back-link:hover {
    color: #fff;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.back-link i {
    margin-right: 5px;
}

.form-section {
    background: rgba(255, 255, 255, 0.05); 
    backdrop-filter: blur(15px); 
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 700px; 
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 
                 0 0 30px rgba(255, 255, 255, 0.3); 
    border: 1px solid rgba(255, 255, 255, 0.18); 
}

.section-title {
    color: #ffffffff; 
    font-family: "Playwrite US Modern", cursive;
    font-size: 32px;
    margin-bottom: 30px;
    text-align: center;
}

.reporte-form {
    display: flex;
    flex-direction: column;
    gap: 20px; 
}

.form-group {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #ccc;
    font-size: 14px;
}


.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1; 
}


input[type="text"], input[type="date"], select, textarea { 
    padding: 12px 15px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1); 
    color: #fff;
    font-size: 16px;
    transition: border-color 0.3s, background 0.3s;
    outline: none;
}

input[type="text"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
    border-color: #9d5353; 
    background: rgba(255, 255, 255, 0.15);
}


::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

input[type="date"] {
    color: #fff;
}
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1); 
    cursor: pointer;
}

select {
    appearance: none; 
    background-image: url('data:image/svg+xml;charset=UTF8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white"><path d="M5.516 7.548l4.484 4.484 4.484-4.484z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 15px top 50%;
    background-size: 0.65em auto;
}

select option {
    background: #263248; 
    color: #fff;
}

.submit-button {
    padding: 15px;
    border: none;
    border-radius: 8px;
    background: #9d5353; 
    color: white;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s, transform 0.1s;
    margin-top: 10px;
}

.submit-button:hover {
    background: #7a4242; 
    transform: translateY(-2px);
}

.submit-button i {
    margin-right: 8px;
}

.error-message { 
    color: #ffcccc; 
    margin-top: 5px; 
    font-weight: bold; 
    font-size: 12px;
}

.form-group.error input, .form-group.error select, .form-group.error textarea { 
    border: 2px solid #9d5353 !important; 
}


@media (max-width: 600px) {
    header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .form-section {
        padding: 25px;
    }
    
    .form-row {
        flex-direction: column; 
        gap: 15px;
    }
    
    .logo-text {
        font-size: 24px;
    }
}
</style>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <div class="page-container" id="reporte-page-container"> 
        <header>
            <h1 class="logo-text"><i class="fas fa-exclamation-triangle"></i> ABSENTIX</h1>
            <a href="MENU.php" class="back-link" onclick="handlePageTransition(event, this.href)">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </header>
        
        <main class="form-section">
            <h2 class="section-title">Reporte de Conducta</h2>
            <?php echo $status_message; // Muestra mensajes de éxito o error ?>
            
            <form id="reporteForm" action="guardar_reporte.php" method="POST" class="reporte-form"> 
                
                <div class="form-group">
                    <label for="studentName">Nombre Completo del Alumno</label>
                    <input type="text" id="studentName" name="studentName" placeholder="Ej: Juan Pérez García" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grade">Grado</label>
                        <input type="text" id="grade" name="grade" placeholder="Ej: 3°" required>
                    </div>
                    <div class="form-group">
                        <label for="group">Grupo</label>
                        <input type="text" id="group" name="group" placeholder="Ej: C" required>
                    </div>
                    <div class="form-group">
                        <label for="shift">Turno</label>
                        <select id="shift" name="shift" required>
                            <option value="">Selecciona el Turno...</option>
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="specialty">Especialidad</label>
                        <input type="text" id="specialty" name="specialty" placeholder="Ej: Programación" required>
                    </div>
                    <div class="form-group">
                        <label for="idNumber">Número de control</label>
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
                        <label for="reporterName">Nombre Completo del Profesor/Reportante</label>
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
                    <label for="misconductDetails">Descripción Detallada del Incidente</label>
                    <textarea id="misconductDetails" name="misconductDetails" rows="4" placeholder="Describe claramente lo sucedido, el lugar y los testigos (si los hay)." required></textarea>
                </div>

                <button type="submit" class="submit-button" id="submitFormButton">
                    <i class="fas fa-file-pdf"></i> Generar y Guardar Reporte
                </button>
            </form>
        </main>
    </div>
    
    <script>
        // Función de transición de página (fade-out)
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
        
        // LÓGICA DE FADE-IN AL CARGAR LA PÁGINA
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('reporte-page-container');
            if (container) {
                container.classList.remove('fade-out'); 
                container.classList.add('fade-in');     
            }
        });


        document.getElementById('reporteForm').addEventListener('submit', function(event) {
            
            // 1. Ejecutar validación de campos JS
            if (validateForm()) {
                
                // 2. Prevenir el envío automático
                event.preventDefault(); 
                
                // 3. Generar y descargar el PDF
                generatePDFAndPrint();
                
                // 4. Esperar un momento (para que la descarga inicie) y luego forzar el envío
                setTimeout(() => {
                    // Agrega un campo oculto para indicar a PHP que se generó el PDF (opcional, pero útil)
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'pdf_generated';
                    hiddenInput.value = 'true';
                    this.appendChild(hiddenInput);

                    // Re-enviar el formulario a guardar_reporte.php
                    this.submit();
                }, 100); 
                
            } else {
                event.preventDefault(); // Evita el envío si la validación falla
                alert('Por favor, corrige los campos marcados en rojo.');
            }
        });

        function validateForm() {
            let isValid = true;
        
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.form-group.error').forEach(el => el.classList.remove('error'));

            // [MANTENEMOS LA LÓGICA DE VALIDACIÓN JS EXACTAMENTE IGUAL]
            const studentNameInput = document.getElementById('studentName');
            const idNumberInput = document.getElementById('idNumber');
            const gradeInput = document.getElementById('grade');
            const shiftSelect = document.getElementById('shift'); 
            const reportDateInput = document.getElementById('reportDate');
            const reporterNameInput = document.getElementById('reporterName');
            const misconductTypeSelect = document.getElementById('misconductType');
            const misconductDetailsTextarea = document.getElementById('misconductDetails');
            const groupInput = document.getElementById('group');
            const specialtyInput = document.getElementById('specialty');

        
            const namePattern = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (!namePattern.test(studentNameInput.value.trim()) || studentNameInput.value.trim().length < 5) {
                displayError(studentNameInput, 'Solo se permiten letras y espacios, y debe ser un nombre completo.');
                isValid = false;
            }

            
            const idPattern = /^\d{8}$/; 
            if (!idPattern.test(idNumberInput.value.trim())) {
                displayError(idNumberInput, 'Debe ser un número de control de 8 dígitos (solo números).');
                isValid = false;
            }
        
            if (gradeInput.value.trim() === '') {
                displayError(gradeInput, 'Este campo es obligatorio.');
                isValid = false;
            }
            if (groupInput.value.trim() === '') {
                displayError(groupInput, 'Este campo es obligatorio.');
                isValid = false;
            }
            if (specialtyInput.value.trim() === '') {
                displayError(specialtyInput, 'Este campo es obligatorio.');
                isValid = false;
            }
            
            if (shiftSelect.value === '') {
                displayError(shiftSelect, 'Debe seleccionar el turno.');
                isValid = false;
            }
            
            if (reportDateInput.value.trim() === '') {
                displayError(reportDateInput, 'Debe seleccionar la fecha del incidente.');
                isValid = false;
            }

            if (!namePattern.test(reporterNameInput.value.trim()) || reporterNameInput.value.trim().length < 5) {
                displayError(reporterNameInput, 'Nombre del reportante obligatorio (solo letras y espacios).');
                isValid = false;
            }

            
            if (misconductTypeSelect.value === '') {
                displayError(misconductTypeSelect, 'Debe seleccionar el tipo de falta.');
                isValid = false;
            }

        
            if (misconductDetailsTextarea.value.trim().length < 20) {
                displayError(misconductDetailsTextarea, 'La descripción es obligatoria y debe tener al menos 20 caracteres.');
                isValid = false;
            }

            return isValid;
        }

        function displayError(inputElement, message) {
            const formGroup = inputElement.closest('.form-group');
            formGroup.classList.add('error');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            formGroup.appendChild(errorDiv);
        }

        function generatePDFAndPrint() {
            const { jsPDF } = window.jspdf;
            
            const doc = new jsPDF('l', 'mm', 'a4'); 
            
            // Recolección de datos
            const studentName = document.getElementById('studentName').value.trim();
            const grade = document.getElementById('grade').value.trim();
            const group = document.getElementById('group').value.trim();
            const shift = document.getElementById('shift').value.trim(); 
            const specialty = document.getElementById('specialty').value.trim();
            const idNumber = document.getElementById('idNumber').value.trim();
            const reportDate = document.getElementById('reportDate').value.trim();
            const reporterName = document.getElementById('reporterName').value.trim();
            const misconductTypeText = document.getElementById('misconductType').options[document.getElementById('misconductType').selectedIndex].text;
            const misconductDetails = document.getElementById('misconductDetails').value.trim();
            
            const formattedDate = reportDate.split('-').reverse().join('/');

            let y = 20;
            const lineHeight = 8;
            const margin = 15;
            const maxWidth = 260; 
            const pageCenter = 148.5; 

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
            doc.text("DETALLES DEL INCIDENTE", margin, y); y += lineHeight + 2;
            doc.line(margin, y, 282, y); y += 5;
            doc.setFontSize(12);
            
            // Fila 1
            doc.text(`FECHA DEL INCIDENTE: ${formattedDate}`, margin, y);
            doc.text(`TIPO DE FALTA: ${misconductTypeText}`, 160, y); y += lineHeight;
            
            // Fila 2
            doc.text(`PROFESOR/REPORTANTE: ${reporterName}`, margin, y); y += lineHeight * 2;

            // Sección: DESCRIPCIÓN
            doc.setFontSize(16);
            doc.text("DESCRIPCIÓN DETALLADA:", margin, y); y += lineHeight;
            doc.setFontSize(12);
            
            
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
            alert("Reporte de Conducta generado y descargado con éxito. El registro se enviará a la base de datos.");
        }
    </script>
</body>
</html>