<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Generar Justificante</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.styles.css">
</head>
<body>
    <div class="page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-file-medical"></i> Generar Justificante</h1>
            <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Menú</a>
        </header>
        
        <main class="form-section">
            <h2 class="section-title">Justificante</h2>
            <form action="#" method="POST" class="justificante-form">
                
                <div class="form-group">
                    <label for="studentName">Nombre Completo del Alumno</label>
                    <input type="text" id="studentName" placeholder="Ej: Juan Pérez García" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grade">Grado y Grupo</label>
                        <input type="text" id="grade" placeholder="Ej: 3° C" required>
                    </div>
                    <div class="form-group">
                        <label for="idNumber">Número de control</label>
                        <input type="text" id="idNumber" placeholder="Ej: 12345678" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="absenceDates">Fechas de Inasistencia (Rango o Múltiples Días)</label>
                    <input type="text" id="absenceDates" placeholder="Ej: 15/Oct/2025 o 15-17/Oct/2025" required>
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
                    <i class="fas fa-paper-plane"></i> Generar y Enviar Solicitud
                </button>
            </form>
        </main>
    </div>
</body>
</html>