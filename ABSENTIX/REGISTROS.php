<?php
// ===============================================
// 1. SIMULACIÓN DE CONEXIÓN A BASE DE DATOS Y FUNCIONES
// ===============================================

/**
 * Simula la obtención de todos los alumnos de la base de datos.
 * @return array
 */
function obtenerTodosLosAlumnosSimulacion() {
    // Datos simulados (los mismos de antes)
    return [
        ['id' => 1, 'nombre' => 'Ana', 'apellido' => 'García', 'grado' => '1', 'grupo' => 'A', 'especialidad' => 'Informática', 'turno' => 'Matutino', 'reportes' => 2, 'justificantes' => 5],
        ['id' => 2, 'nombre' => 'Luis', 'apellido' => 'Martínez', 'grado' => '3', 'grupo' => 'C', 'especialidad' => 'Contabilidad', 'turno' => 'Vespertino', 'reportes' => 4, 'justificantes' => 1],
        ['id' => 3, 'nombre' => 'Sofía', 'apellido' => 'Rodríguez', 'grado' => '1', 'grupo' => 'A', 'especialidad' => 'Informática', 'turno' => 'Matutino', 'reportes' => 0, 'justificantes' => 2],
        ['id' => 4, 'nombre' => 'Carlos', 'apellido' => 'Pérez', 'grado' => '2', 'grupo' => 'B', 'especialidad' => 'Electrónica', 'turno' => 'Matutino', 'reportes' => 1, 'justificantes' => 0],
        ['id' => 5, 'nombre' => 'María', 'apellido' => 'López', 'grado' => '3', 'grupo' => 'C', 'especialidad' => 'Contabilidad', 'turno' => 'Vespertino', 'reportes' => 5, 'justificantes' => 3],
    ];
}

/**
 * Simula la actualización de reportes en la base de datos (se mantiene por si acaso, pero no se usa en la interfaz).
 */
function actualizarReporteSimulacion($alumno_id, $nuevo_reporte_count) {
    // En la simulación, simplemente confirmamos el éxito.
    return true; 
}


// ===============================================
// 2. LÓGICA DE FILTRADO (BÚSQUEDA)
// ===============================================

// Obtener todos los alumnos para poder aplicar el filtro
$alumnos_base = obtenerTodosLosAlumnosSimulacion(); 
$resultados = $alumnos_base;

// Capturar los valores de búsqueda enviados por el formulario (GET)
$filtro_nombre = $_GET['nombre'] ?? '';
$filtro_apellido = $_GET['apellido'] ?? '';
$filtro_grado = $_GET['grado'] ?? '';
$filtro_grupo = $_GET['grupo'] ?? '';
$filtro_especialidad = $_GET['especialidad'] ?? '';
$filtro_turno = $_GET['turno'] ?? '';

// Bandera para saber si se ha intentado hacer una búsqueda con criterios
$busqueda_activa = !empty($filtro_nombre) || !empty($filtro_apellido) || !empty($filtro_grado) || !empty($filtro_grupo) || !empty($filtro_especialidad) || !empty($filtro_turno);

// Aplicar filtros a los datos SÓLO si hay búsqueda activa
if ($busqueda_activa) {
    $resultados = array_filter($alumnos_base, function($alumno) use ($filtro_nombre, $filtro_apellido, $filtro_grado, $filtro_grupo, $filtro_especialidad, $filtro_turno) {
        // La búsqueda se basa en la lógica original
        $nombre_match = empty($filtro_nombre) || stripos($alumno['nombre'], $filtro_nombre) !== false;
        $apellido_match = empty($filtro_apellido) || stripos($alumno['apellido'], $filtro_apellido) !== false;
        $grado_match = empty($filtro_grado) || $alumno['grado'] == $filtro_grado;
        $grupo_match = empty($filtro_grupo) || stripos($alumno['grupo'], $filtro_grupo) !== false;
        $especialidad_match = empty($filtro_especialidad) || stripos($alumno['especialidad'], $filtro_especialidad) !== false;
        $turno_match = empty($filtro_turno) || $alumno['turno'] == $filtro_turno;

        return $nombre_match && $apellido_match && $grado_match && $grupo_match && $especialidad_match && $turno_match;
    });
}
// Si no hay búsqueda activa, inicializamos resultados a un array vacío para ocultar la tabla.
if (!$busqueda_activa) {
    $resultados = []; 
}

// Lógica para calcular totales (para los botones globales)
$total_reportes_busqueda = 0;
$total_justificantes_busqueda = 0;
foreach ($resultados as $alumno) {
    $total_reportes_busqueda += $alumno['reportes'];
    $total_justificantes_busqueda += $alumno['justificantes'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Buscador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* FUENTES EXTERNAS */
@import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');

/* ESTILOS BASE */
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
    opacity: 1; 
    transition: opacity 0.5s ease-in-out; 
}

/* Transición para salir (IMPORTANTE para que funcione el JS) */
.page-container.fade-out {
    opacity: 0;
    pointer-events: none; 
}

/* ENCABEZADO */
header {
    width: 100%;
    max-width: 1200px; /* Ancho para esta página que tiene más contenido */
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

/* CONTENEDOR PRINCIPAL (similar a form-section) */
.form-section {
    background: rgba(255, 255, 255, 0.05); 
    backdrop-filter: blur(15px); 
    border-radius: 20px;
    padding: 40px;
    width: 100%;
    max-width: 1200px; /* Ancho para la tabla */
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 
                 0 0 30px rgba(255, 255, 255, 0.3); 
    border: 1px solid rgba(255, 255, 255, 0.18); 
    margin-bottom: 20px;
}

h2 {
    color: #ffffffff; 
    font-family: "Alan Sans", sans-serif;
    font-size: 24px;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 10px;
}

label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #ccc;
    font-size: 14px;
    display: block; /* Asegura que la etiqueta use todo el espacio */
}

/* INPUTS, SELECTS */
input[type="text"], input[type="date"], select, textarea { 
    padding: 10px 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1); 
    color: #fff;
    font-size: 16px;
    transition: border-color 0.3s, background 0.3s;
    outline: none;
    width: 100%; /* Ocupa el 100% de su contenedor */
    box-sizing: border-box;
}

input[type="text"]:focus, select:focus {
    border-color: #9d5353; 
    background: rgba(255, 255, 255, 0.15);
}

/* ESTILO ESPECÍFICO DE SELECT */
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


/* Estilos específicos para el buscador */
.form-busqueda {
    display: flex;
    flex-wrap: wrap; 
    gap: 20px;
    margin-bottom: 20px;
}

.form-busqueda > div {
    flex: 1 1 calc(33.33% - 15px); /* 3 columnas en desktop */
    min-width: 150px;
    display: flex;
    flex-direction: column;
}

.botones-busqueda {
    flex: 1 1 100%; 
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.botones-busqueda button {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 16px;
}

.btn-buscar {
    background: #9d5353; 
    color: white;
}

.btn-buscar:hover {
    background: #7a4242;
}

.btn-limpiar {
    background: #555;
    color: white;
}

.btn-limpiar:hover {
    background: #333;
}

/* ================== ESTILOS DE RESULTADOS ================== */

.search-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.search-controls h2 {
    border: none;
    margin: 0;
    padding: 0;
}

.botones-globales button {
    margin-left: 10px;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    color: white;
}

.btn-global-justificante {
    background-color: #3f92bb; 
}
.btn-global-justificante:hover {
    background-color: #347fa6;
}

.btn-global-reporte {
    background-color: #9d5353; 
}
.btn-global-reporte:hover {
    background-color: #7a4242;
}


/* Estilos de la Tabla */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    overflow: hidden; 
}

thead {
    background: rgba(0, 0, 0, 0.4);
    color: #fff;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

tbody tr:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Alerta de Reportes */
.alerta-reporte {
    font-weight: bold;
    color: #ffcccc; 
    background-color: rgba(157, 83, 83, 0.5); 
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-block;
    font-size: 12px;
}

/* Botón de acción individual */
.btn-accion {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
    color: white;
    background-color: #3f92bb; 
}

.btn-accion:hover {
    background-color: #347fa6;
}

/* Mensaje de no búsqueda */
.no-busqueda-msg {
    margin-top: 20px; 
    padding: 15px; 
    border: 1px dashed #3f92bb; 
    background-color: rgba(63, 146, 187, 0.1); 
    text-align: center;
    border-radius: 8px;
    color: #ccc;
}
.no-busqueda-msg strong {
    color: #fff;
}

/* ================== MEDIA QUERY (Móvil) ================== */
@media (max-width: 850px) {
    header {
        max-width: 100%;
        padding: 10px;
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    .form-section {
        padding: 25px;
    }
    
    .form-busqueda > div {
        flex: 1 1 100%; /* 1 columna en móvil */
    }
    
    .search-controls {
        flex-direction: column;
        align-items: flex-start;
    }
    .botones-globales {
        width: 100%;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
    }
    .botones-globales button {
        margin-left: 0;
        flex: 1;
    }
    
    /* Tabla Responsiva */
    table, thead, tbody, th, td, tr { 
        display: block; 
    }
    
    thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    
    td { 
        border: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        position: relative;
        padding-left: 50%; 
        text-align: right;
    }
    
    td:last-child {
        border-bottom: 3px solid rgba(157, 83, 83, 0.5); /* Separador visual entre alumnos */
        text-align: center;
    }
    
    td:before { 
        position: absolute;
        top: 12px;
        left: 15px;
        width: 45%; 
        white-space: nowrap;
        text-align: left;
        font-weight: bold;
        color: #ccc;
        font-size: 14px;
    }

    td:nth-of-type(1):before { content: "Nombre"; }
    td:nth-of-type(2):before { content: "Grado, Grupo y Especialidad"; }
    td:nth-of-type(3):before { content: "Turno"; }
    td:nth-of-type(4):before { content: "Reportes"; }
    td:nth-of-type(5):before { content: "Justificantes"; }
    td:nth-of-type(6):before { content: "Acciones"; }
}
</style>
</head>
<body>

    <div class="page-container">
        
        <header>
            <h1 class="logo-text"><i class="fas fa-exclamation-triangle"></i> ABSENTIX - CBtis 258</h1>
            <a href="MENU.php" class="back-link" onclick="handlePageTransition(event, this.href)">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </header>

        <main class="form-section">
            <h2>🔎 Buscador de Alumnos</h2>

            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form-busqueda">
                <div>
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                </div>
                <div>
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($filtro_apellido); ?>">
                </div>
                
                <div>
                    <label for="grado">Grado</label>
                    <input type="text" name="grado" id="grado" value="<?php echo htmlspecialchars($filtro_grado); ?>" placeholder="Ej: 1 o 3">
                </div>
                <div>
                    <label for="grupo">Grupo</label>
                    <input type="text" name="grupo" id="grupo" value="<?php echo htmlspecialchars($filtro_grupo); ?>" placeholder="Ej: A o C">
                </div>
                
                <div>
                    <label for="especialidad">Especialidad</label>
                    <input type="text" name="especialidad" id="especialidad" value="<?php echo htmlspecialchars($filtro_especialidad); ?>" placeholder="Ej: Informática">
                </div>
                <div>
                    <label for="turno">Turno</label>
                    <select name="turno" id="turno">
                        <option value="">Todos</option>
                        <option value="Matutino" <?php echo $filtro_turno == 'Matutino' ? 'selected' : ''; ?>>Matutino</option>
                        <option value="Vespertino" <?php echo $filtro_turno == 'Vespertino' ? 'selected' : ''; ?>>Vespertino</option>
                    </select>
                </div>

                <div class="botones-busqueda">
                    <button type="submit" class="btn-buscar"><i class="fas fa-search"></i> Buscar Alumnos</button>
                    <button type="button" class="btn-limpiar" onclick="handlePageTransition(event, '<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>')"><i class="fas fa-eraser"></i> Limpiar Filtros</button>
                </div>
            </form>
            
            <?php if ($busqueda_activa): ?>
            
                <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 20px 0;">

                <div class="search-controls">
                    <h2>Resultados de la Búsqueda (<?php echo count($resultados); ?> Alumnos)</h2>
                    
                    <?php if (!empty($resultados)): ?>
                    <div class="botones-globales">
                        <button 
                            class="btn-global-justificante" 
                            onclick="mostrarDetalleGlobal('Justificantes', '<?php echo htmlspecialchars(json_encode($resultados)); ?>', '<?php echo $total_justificantes_busqueda; ?>')">
                            📑 Justificantes Grupo (<?php echo $total_justificantes_busqueda; ?>)
                        </button>
                        <button 
                            class="btn-global-reporte" 
                            onclick="mostrarDetalleGlobal('Reportes', '<?php echo htmlspecialchars(json_encode($resultados)); ?>', '<?php echo $total_reportes_busqueda; ?>')">
                            🚨 Reportes Grupo (<?php echo $total_reportes_busqueda; ?>)
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (empty($resultados)): ?>
                    <p style="color: #ffcccc; font-weight: bold;"> No se encontraron alumnos con esos criterios.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre y Apellido</th>
                            <th>Grado, Grupo y Especialidad</th>
                            <th>Turno</th>
                            <th>Reportes</th>
                            <th>Justificantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $alumno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['grado'] . 'º ' . $alumno['grupo'] . ' (' . $alumno['especialidad'] . ')'); ?></td>
                            <td><?php echo htmlspecialchars($alumno['turno']); ?></td>
                            <td>
                                <?php
                                // Lógica para la advertencia de reportes
                                if ($alumno['reportes'] > 3) {
                                    echo '<div class="alerta-reporte"> <i class="fas fa-exclamation-circle"></i> ADVERTENCIA: ' . $alumno['reportes'] . ' Reportes</div>';
                                } else {
                                    echo $alumno['reportes'] . ' Reportes';
                                }
                                ?>
                            </td>
                            <td><?php echo $alumno['justificantes']; ?> Justificantes</td>
                            <td>
                                <button 
                                    class="btn-accion" 
                                    onclick="mostrarDetalle('Justificantes', '<?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']); ?>', '<?php echo $alumno['justificantes']; ?>')">
                                    Ver Detalle
                                </button>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-busqueda-msg">
                    <i class="fas fa-info-circle"></i> Ingrese los datos de búsqueda en el formulario superior y presione <strong>"Buscar Alumnos"</strong> para ver los resultados.
                </p>
            <?php endif; ?>

        </main>
    </div>

<script>
    /**
     * Función de transición de página (fade-out)
     */
    function handlePageTransition(event, url) {
        event.preventDefault(); 
        
        const pageContainer = document.querySelector('.page-container');
        
        if (pageContainer) {
            pageContainer.classList.add('fade-out');
            
            // Espera a que la transición termine (0.5s) antes de navegar
            setTimeout(() => {
                window.location.href = url;
            }, 500); 
        } else {
            // Navega inmediatamente si no encuentra el contenedor (fallback)
            window.location.href = url;
        }
    }

    /**
     * Muestra el detalle de Justificantes de un solo alumno (función de PRUEBA).
     */
    function mostrarDetalle(tipo, nombreAlumno, cantidad) {
        alert("--- Detalle de " + tipo + " ---\n" +
              "Alumno: " + nombreAlumno + "\n" +
              "Cantidad: " + cantidad + "\n\n" +
              "** Función de PRUEBA para un alumno. Aquí se cargarían los detalles específicos. **");
    }
    
    /**
     * Muestra el detalle total (Reportes/Justificantes) del grupo de alumnos.
     */
    function mostrarDetalleGlobal(tipo, jsonAlumnos, total) {
        // Deserializar el JSON (que fue codificado en PHP)
        const alumnos = JSON.parse(jsonAlumnos);
        let detalle = `--- ${tipo} del Grupo/Búsqueda ---\nTotal: ${total} ${tipo}\n\n`;
        
        // Construir el detalle alumno por alumno
        alumnos.forEach(alumno => {
            const dato = tipo === 'Reportes' ? alumno.reportes : alumno.justificantes;
            detalle += `• ${alumno.nombre} ${alumno.apellido}: ${dato} ${tipo}\n`;
        });
        
        detalle += "\n** Función de PRUEBA para el grupo. En un sistema real, aquí se mostraría una tabla detallada con filtros aplicados. **";
        
        alert(detalle);
    }
</script>

</body>
</html>
