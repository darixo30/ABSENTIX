<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Generar Justificante</title>
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
    align-items: center; /* Centra el contenido horizontalmente */
    padding: 20px;
    background: rgba(0, 0, 0, 0.4); /* Capa oscura para mejorar legibilidad */
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
    gap: 20px; /
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
    background: rgba(255, 255, 255, 0.1); /* Fondo transparente para el glassmorphism */
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
<body>
    <div class="page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-file-medical"></i> Generar Justificante</h1>
            <a href="MENU.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Menú</a>
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
