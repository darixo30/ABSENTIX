<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Generar Justificante</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="page-container">
        <header>
            <h1 class="logo-text"><i class="fas fa-file-medical"></i> Generar Justificante</h1>
            <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al Menú</a>
        </header>
        
        <main class="form-section">
            <h2 class="section-title">Datos del Alumno</h2>

            <form action="guardar_justificante.php" method="POST" enctype="multipart/form-data" class="justificante-form">

                <!-- FECHA DEL JUSTIFICANTE -->
                <div class="form-group">
                    <label for="fechadedia">Fecha del Justificante</label>
                    <input type="date" id="fechadedia" name="fechadedia" required>
                </div>

                <!-- DATOS DEL ALUMNO -->
                <div class="form-group">
                    <label for="matricula">Matrícula</label>
                    <input type="text" id="matricula" name="matricula" placeholder="Ej: 12345678901234" required
                           pattern="\d{14}" title="Debe contener exactamente 14 números">
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan" required
                           pattern="[A-Za-z\s]+" title="Solo letras y espacios permitidos">
                </div>

                <div class="form-group">
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" id="apellido_paterno" name="apellido_paterno" placeholder="Ej: Pérez" required
                           pattern="[A-Za-z\s]+" title="Solo letras y espacios permitidos">
                </div>

                <div class="form-group">
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" id="apellido_materno" name="apellido_materno" placeholder="Ej: García" required
                           pattern="[A-Za-z\s]+" title="Solo letras y espacios permitidos">
                </div>

                <div class="form-group">
                    <label for="turno">Turno</label>
                    <select id="turno" name="turno" required>
                        <option value="">Selecciona un turno...</option>
                        <option value="Matutino">Matutino</option>
                        <option value="Vespertino">Vespertino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="especialidad">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad" placeholder="Ej: Programación, Contabilidad, etc." required>
                </div>

                <div class="form-group">
                    <label for="grupo">Grupo</label>
                    <input type="text" id="grupo" name="grupo" placeholder="Ej: 3C" required
                           pattern="\d[A-Za-z]" title="Debe ser un número seguido de una letra, ejemplo: 3C">
                </div>

                <hr>

                <!-- DATOS DEL JUSTIFICANTE -->
                <h2 class="section-title">Datos del Justificante</h2>

                <div class="form-group">
                    <label for="motivo">Motivo de la Inasistencia</label>
                    <select id="motivo" name="motivo" required>
                        <option value="">Selecciona un motivo...</option>
                        <option value="Medico">Cita o Enfermedad Médica</option>
                        <option value="Familiar">Motivo Familiar (Defunción o Evento)</option>
                        <option value="Oficial">Trámite Oficial/Legal</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fechajustificar">Fechas de Inasistencia</label>
                    <input type="text" id="fechajustificar" name="fechajustificar" 
                     placeholder="Ej: 15/Oct/2025 o 15-17/Oct/2025" required>
                </div>

                <div class="form-group">
                    <label for="sistema">Sistema de Estudio</label>
                    <select id="sistema" name="sistema" required>
                        <option value="">Selecciona un sistema...</option>
                        <option value="Dual">Dual</option>
                        <option value="Presencial">Presencial</option>
                    </select>
                </div>

                <button type="submit" class="submit-button">
                    <i class="fas fa-paper-plane"></i> Guardar Justificante
                </button>

            </form>
        </main>
    </div>
</body>
</html>
