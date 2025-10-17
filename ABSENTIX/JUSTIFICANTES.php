<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Generar Justificante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');
body {
    margin: 0;
    font-family: "Alan Sans", sans-serif;
    color: #fff; /* Texto principal blanco */
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
    opacity: 0; /* Inicia invisible */
    transition: opacity 0.5s ease-in-out; 
}

/* Clase para el efecto de desvanecimiento al salir */
.page-container.fade-out {
    opacity: 0;
    pointer-events: none; 
}

/* CLASE CLAVE: Estado de transición de entrada (fade-in) */
.page-container.fade-in {
    opacity: 1; /* Transiciona a visible al cargar */
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


.justificante-form {
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


input[type="text"], input[type="file"], select, textarea {
    padding: 12px 15px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1); 
    color: #fff;
    font-size: 16px;
    transition: border-color 0.3s, background 0.3s;
    outline: none;
}

input[type="text"]:focus, select:focus, textarea:focus {
    border-color: #9d5353; 
    background: rgba(255, 255, 255, 0.15);
}


::placeholder {
    color: rgba(255, 255, 255, 0.5);
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

input[type="file"] {
    cursor: pointer;
    background: rgba(255, 255, 255, 0.05);
    border: 1px dashed rgba(255, 255, 255, 0.4);
    
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
    <div class="page-container" id="justificante-page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-exclamation-triangle"></i> ABSENTIX</h1>
            <a href="MENU.php" class="back-link" onclick="handlePageTransition(event, this.href)">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </header>

        <main class="form-section">
            <h2 class="section-title">Generar Justificante</h2>
            <form id="justificanteForm" action="#" method="POST" class="justificante-form"> 
                
                <div class="form-group">
                    <label for="studentName">Nombre Completo del Alumno</label>
                    <input type="text" id="studentName" placeholder="Ej: Juan Pérez García" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grade">Grado</label>
                        <input type="text" id="grade" placeholder="Ej: 3°" required>
                    </div>
                    <div class="form-group">
                        <label for="group">Grupo</label>
                        <input type="text" id="group" placeholder="Ej: C" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty">Especialidad</label>
                        <input type="text" id="specialty" placeholder="Ej: Programación" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="idNumber">Número de control</label>
                        <input type="text" id="idNumber" placeholder="Ej: 12345678" required>
                    </div>
                    <div class="form-group">
                        <label for="shift">Turno</label>
                        <select id="shift" required>
                            <option value="">Selecciona el turno...</option>
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="absenceDates">Fechas de Inasistencia (Rango o Múltiples Días)</label>
                    <input type="text" id="absenceDates" placeholder="Ej: 30/Sep/2025 o 15-17/Oct/2025" required>
                </div>
                
                <div class="form-group">
                    <label for="reason">Motivo de la Ausencia</label>
                    <select id="reason" required>
                        <option value="">Selecciona un motivo...</option>
                        <option value="medico">Cita/Enfermedad Médica</option>
                        <option value="familiar">Motivo Familiar (Defunción/Evento)</option>
                        <option value="oficial">Trámite Oficial/Legal</option>
                        <option value="otro">Otro (Especificar en notas)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supportDoc">Documento de Soporte (Ej: Receta, Acta, Cita)</label>
                    <input type="file" id="supportDoc" accept=".pdf, .jpg, .png">
                </div>
                
                <div class="form-group">
                    <label for="notes">Notas Adicionales (Opcional)</label>
                    <textarea id="notes" rows="3" placeholder="Detalles extra sobre el justificante..."></textarea>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-file-pdf"></i> Generar y Descargar Justificante
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
            const container = document.getElementById('justificante-page-container');
            if (container) {
                container.classList.remove('fade-out'); 
                container.classList.add('fade-in');     
            }
        });

        // Listener del formulario
        document.getElementById('justificanteForm').addEventListener('submit', function(event) {
            
            event.preventDefault(); 
            
            if (validateForm()) {
                generatePDFAndSave(); 
            } else {
                alert('Por favor, corrige los campos marcados en rojo.');
            }
        });

        function validateForm() {
            let isValid = true;
        
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            document.querySelectorAll('.form-group.error').forEach(el => el.classList.remove('error'));

            const studentNameInput = document.getElementById('studentName');
            const idNumberInput = document.getElementById('idNumber');
            const datesInput = document.getElementById('absenceDates');
            const gradeInput = document.getElementById('grade');      
            const groupInput = document.getElementById('group');      
            const specialtyInput = document.getElementById('specialty');
            const reasonSelect = document.getElementById('reason');
            const shiftSelect = document.getElementById('shift'); 
            
            // 1. Nombre del Alumno
            const namePattern = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (!namePattern.test(studentNameInput.value.trim()) || studentNameInput.value.trim().length < 5) {
                displayError(studentNameInput, 'Solo se permiten letras y espacios, y debe ser un nombre completo.');
                isValid = false;
            }

            // 2. Número de Control
            const idPattern = /^\d{8}$/; 
            if (!idPattern.test(idNumberInput.value.trim())) {
                displayError(idNumberInput, 'Debe ser un número de control de 8 dígitos (solo números).');
                isValid = false;
            }
            
            // 3. Grado, Grupo y Especialidad
            if (gradeInput.value.trim() === '') {
                displayError(gradeInput, 'El grado es obligatorio.');
                isValid = false;
            }
            if (groupInput.value.trim() === '') {
                displayError(groupInput, 'El grupo es obligatorio.');
                isValid = false;
            }
            if (specialtyInput.value.trim() === '') {
                displayError(specialtyInput, 'La especialidad es obligatoria.');
                isValid = false;
            }
        
            // 4. Turno 
            if (shiftSelect.value === '') {
                displayError(shiftSelect, 'Debe seleccionar un turno.');
                isValid = false;
            }

            // 5. Fechas de Inasistencia
            if (datesInput.value.trim() === '') {
                displayError(datesInput, 'Este campo es obligatorio.');
                isValid = false;
            }
            
            // 6. Motivo de la Ausencia
            if (reasonSelect.value === '') {
                displayError(reasonSelect, 'Debe seleccionar un motivo de la lista.');
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

        function generatePDFAndSave() {
            const { jsPDF } = window.jspdf;
            
            const doc = new jsPDF('l', 'mm', 'a4'); 
            
            const studentName = document.getElementById('studentName').value.trim();
            const idNumber = document.getElementById('idNumber').value.trim();
            const absenceDates = document.getElementById('absenceDates').value.trim();
            const reasonText = document.getElementById('reason').options[document.getElementById('reason').selectedIndex].text;
            const submissionDate = new Date().toLocaleDateString('es-MX');

            const grado = document.getElementById('grade').value.trim();
            const grupo = document.getElementById('group').value.trim();
            const especialidad = document.getElementById('specialty').value.trim();
            const shiftText = document.getElementById('shift').options[document.getElementById('shift').selectedIndex].text; 
            
            // Variables de Posición
            let y = 20; 
            const marginX = 15;
            const lineHeight = 8;
            const tableStartY = 85;
            const tableRowHeight = 7;
            const tableNumRows = 8;
            const pageCenter = 148.5;

            // 1. Título
            doc.setFontSize(26);
            doc.text("JUSTIFICANTE ESCOLAR", pageCenter, y, null, null, "center"); // Centrado en A4-L
            y += 15;
            
            // 2. Datos del Alumno
            doc.setFontSize(12);
            doc.text(`Nombre completo: ${studentName}`, marginX, y); 
            doc.text(`Matrícula: ${idNumber}`, 180, y); y += lineHeight;

            // Uso de variables separadas
            doc.text(`Grado: ${grado}`, marginX, y); 
            doc.text(`Grupo: ${grupo}`, 70, y);
            doc.text(`Especialidad: ${especialidad}`, 140, y);
            doc.text(`Turno: ${shiftText}`, 230, y); 
            y += lineHeight;

            doc.line(marginX, y, 280, y); // Separador
            y += 5;
            
            // 3. Detalles de Justificación
            doc.setFontSize(14);
            doc.text("DETALLES DE LA INASISTENCIA", marginX, y); y += lineHeight;
            doc.setFontSize(12);
            doc.text(`Fechas a justificar: ${absenceDates}`, marginX, y); y += lineHeight;
            doc.text(`Motivo: ${reasonText}`, marginX, y); y += lineHeight * 2;
            
            // 4. Tabla de Firmas de Maestros
            doc.setFontSize(14);
            doc.text("Firmas de Maestros", marginX, y); y += 5;
            doc.setFontSize(10);

            // Encabezados de la Tabla
            doc.rect(marginX, y, 130, tableRowHeight); // Borde
            doc.text("Materia/Maestro(a)", marginX + 2, y + 5);
            doc.rect(marginX + 130, y, 50, tableRowHeight);
            doc.text("Firma", marginX + 130 + 2, y + 5);
            y += tableRowHeight;

            // Filas de la Tabla
            for (let i = 1; i <= tableNumRows; i++) {
                doc.rect(marginX, y, 130, tableRowHeight);
                doc.text(`${i}.`, marginX + 2, y + 5);
                doc.rect(marginX + 130, y, 50, tableRowHeight);
                y += tableRowHeight;
            }
            
            // 5. Secciones de Firma y Entrega (Columna Inferior Derecha)
            
            y = tableStartY + (tableNumRows * tableRowHeight) + 15;
            
            // Firma del Padre/Tutor
            doc.line(marginX + 190, y, marginX + 270, y); y += 5;
            doc.setFontSize(12);
            doc.text("Firma del padre, madre o tutor", marginX + 200, y); y += lineHeight;
            
            // Fecha de Entrega
            doc.text(`Fecha de entrega del justificante: ${submissionDate}`, marginX + 190, y); y += lineHeight;


            // Generar el PDF y forzar la descarga
            doc.save(`Justificante_${idNumber}.pdf`);
            alert("Justificante generado y descargado con éxito. Por favor, imprime el archivo para su llenado.");
        }
    </script>
</body>
</html>
