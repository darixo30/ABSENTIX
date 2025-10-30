<?php
// Archivo: REGISTROS.php (Versión con conteo de Justificantes y Reportes)

session_start();

// 🚨 COMPROBACIÓN DE SESIÓN
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: LOGIN.php"); 
    exit;
}

// 🚨 INCLUYE LA CONEXIÓN (Debe tener la función conectarBD() )
require_once 'conexion.php'; 
$conexion = conectarBD(); 

// =================================================================
// 1. INICIALIZACIÓN DE VARIABLES
// =================================================================

$alumnos = []; 
$turno_sesion = $_SESSION['turno'] ?? 'Matutino'; 
$message = "Ingresa criterios de búsqueda para filtrar la lista."; 

// Variables de los filtros (Recuperadas del formulario GET, inicializadas a cadena vacía)
$nombre = trim($_GET['nombre'] ?? '');
$apellido = trim($_GET['apellido'] ?? ''); 
$grado = trim($_GET['grado'] ?? '');
$grupo = trim($_GET['grupo'] ?? '');
$especialidad = trim($_GET['especialidad'] ?? '');


// =================================================================
// 2. Lógica de Búsqueda Flexible
// =================================================================

$sql = "SELECT * FROM alumnos WHERE 1=1 AND turno = ?";
$params = [$turno_sesion];
$types = "s"; 

$has_filters = !empty($nombre) || !empty($apellido) || !empty($grado) || !empty($grupo) || !empty($especialidad);


// A. Filtro por Nombre o Apellido
if (!empty($nombre) && !empty($apellido)) {
    $sql .= " AND nombre_completo LIKE ? AND nombre_completo LIKE ?"; 
    $params[] = "%" . $nombre . "%";
    $params[] = "%" . $apellido . "%";
    $types .= "ss";

} elseif (!empty($nombre) || !empty($apellido)) {
    $search_term = !empty($nombre) ? $nombre : $apellido;
    $sql .= " AND nombre_completo LIKE ?"; 
    $params[] = "%" . $search_term . "%";
    $types .= "s";
}

// B. Filtros Adicionales
if (!empty($grado)) {
    $sql .= " AND grado = ?"; 
    $params[] = $grado;
    $types .= "s";
}

if (!empty($grupo)) {
    $sql .= " AND grupo LIKE ?"; 
    $params[] = "%" . $grupo . "%";
    $types .= "s";
}

if (!empty($especialidad)) {
    $sql .= " AND especialidad LIKE ?"; 
    $params[] = "%" . $especialidad . "%";
    $types .= "s";
}


// 3. Ejecución de la consulta de BÚSQUEDA
// -------------------------------------------------------------------------

// Función auxiliar necesaria para el bind dinámico de parámetros
function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) {
        $refs = array();
        foreach($arr as $key => $value) $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

if ($has_filters || count($params) > 1) { 
    if ($stmt = $conexion->prepare($sql)) {
        
        $bind_params = array_merge([$types], $params);
        call_user_func_array([$stmt, 'bind_param'], refValues($bind_params));

        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            
            // 🚨 LOOP PRINCIPAL: FETCH DE ALUMNOS Y CONTEO DE DOCUMENTOS
            while($row = $result->fetch_assoc()) {
                $num_control_alumno = $row['num_control'];

                // 4. CONTEO DE JUSTIFICANTES
                $sql_just = "SELECT COUNT(*) AS total_justificantes FROM justificantes WHERE num_control = ?";
                $stmt_just = $conexion->prepare($sql_just);
                $stmt_just->bind_param("s", $num_control_alumno);
                $stmt_just->execute();
                $count_just = $stmt_just->get_result()->fetch_assoc()['total_justificantes'];
                $stmt_just->close();
                
                // 5. CONTEO DE REPORTES DE CONDUCTA
                $sql_rep = "SELECT COUNT(*) AS total_reportes FROM reportes_conducta WHERE num_control = ?";
                $stmt_rep = $conexion->prepare($sql_rep);
                $stmt_rep->bind_param("s", $num_control_alumno);
                $stmt_rep->execute();
                $count_rep = $stmt_rep->get_result()->fetch_assoc()['total_reportes'];
                $stmt_rep->close();

                // Añadir los conteos al array del alumno
                $row['total_justificantes'] = $count_just;
                $row['total_reportes'] = $count_rep;
                
                $alumnos[] = $row;
            }
            // FIN DEL LOOP PRINCIPAL
            
            $message = "Se encontraron **" . $result->num_rows . "** alumnos que coinciden con la búsqueda.";
        } else {
            $message = "No se encontraron alumnos con esos criterios en el turno **" . $turno_sesion . "**.";
        }

        $stmt->close();
    } else {
         $message = "Ocurrió un error en la preparación de la consulta SQL: " . $conexion->error;
    }
}

cerrarConexion($conexion); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Alumnos - ABSENTIX</title>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+US+Modern:wght@100..400&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos generales y tema oscuro */
        :root {
            --color-primary-red: #9d5353;
            --font-logo: 'Playwrite US Modern', cursive;
            --font-main: 'Roboto', sans-serif;
            --bg-gradient: linear-gradient(to top, #1c1e26, #263248);
            --bg-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --glass-border: 1px solid rgba(255, 255, 255, 0.18);
            --input-bg: rgba(255, 255, 255, 0.1);
            --input-border: 1px solid rgba(255, 255, 255, 0.3);
            --color-green: #4CAF50;
            --color-red: #F44336;
        }
        body { margin: 0; font-family: var(--font-main); min-height: 100vh; background: var(--bg-gradient); background-image: var(--bg-image); background-size: cover; background-position: center; background-attachment: fixed; color: #fff; }
        .page-container { padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        header { width: 100%; max-width: 900px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .logo-text { font-family: var(--font-logo); font-size: 36px; color: #fff; }
        .back-link { color: #ccc; text-decoration: none; transition: 0.3s; }
        .back-link:hover { color: #fff; }

        .search-container { 
            background: var(--glass-bg); 
            box-shadow: var(--glass-shadow); 
            border: var(--glass-border); 
            backdrop-filter: blur(15px);
            padding: 30px; 
            border-radius: 20px; 
            width: 100%; 
            max-width: 900px; 
        }
        .section-title { font-family: var(--font-logo); font-size: 28px; margin-bottom: 20px; border-bottom: 2px solid var(--color-primary-red); padding-bottom: 10px; }
        .form-row { display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; }
        .form-row .form-group { flex: 1; min-width: 150px; }
        label { margin-bottom: 5px; font-weight: 600; font-size: 14px; display: block; }
        input, select { 
            padding: 12px 15px; 
            border-radius: 8px; 
            font-size: 16px; 
            transition: border-color 0.3s, background 0.3s; 
            outline: none; 
            box-sizing: border-box; 
            width: 100%; 
            background: var(--input-bg); 
            color: #fff; 
            border: var(--input-border);
        }
        input:focus, select:focus { border-color: var(--color-primary-red); background: rgba(255, 255, 255, 0.15); }
        .button-group { display: flex; gap: 20px; margin-top: 25px; }
        .search-button, .clear-button { 
            flex: 1; 
            padding: 15px; 
            border: none; 
            border-radius: 8px; 
            color: white; 
            font-size: 18px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: background 0.3s; 
        }
        .search-button { background: var(--color-primary-red); }
        .search-button:hover { background: #7a4242; }
        .clear-button { background: #555; }
        .clear-button:hover { background: #333; }

        /* Estilos de Resultados */
        .results-section { margin-top: 40px; width: 100%; max-width: 900px; }
        .results-title { font-size: 22px; margin-bottom: 15px; }
        .results-message { color: #81c784; font-weight: bold; margin-bottom: 20px; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; background: var(--glass-bg); border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-size: 14px; }
        th { background: rgba(255, 255, 255, 0.1); font-weight: bold; text-transform: uppercase; }
        
        /* Estilos para los contadores */
        .count-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
            margin-right: 5px;
            display: inline-block;
        }
        .justificante-count { background-color: var(--color-green); }
        .reporte-count { background-color: var(--color-red); }

        @media (max-width: 768px) {
            .form-row { flex-direction: column; }
            .button-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header>
            <h1 class="logo-text">ABSENTIX - CBtis 258</h1>
            <a href="MENU.php" class="back-link">← Volver al Inicio</a>
        </header>

        <div class="search-container">
            <h2 class="section-title">🔎 Buscador de Alumnos (Turno <?php echo $turno_sesion; ?>)</h2>

            <form action="REGISTROS.php" method="GET">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Ej: Sofía">
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" placeholder="Ej: Orozco">
                    </div>
                    <div class="form-group">
                        <label for="grado">Grado</label>
                        <input type="text" id="grado" name="grado" value="<?php echo htmlspecialchars($grado); ?>" placeholder="Ej: 1 ó 5">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="grupo">Grupo</label>
                        <input type="text" id="grupo" name="grupo" value="<?php echo htmlspecialchars($grupo); ?>" placeholder="Ej: A o C">
                    </div>
                    <div class="form-group">
                        <label for="especialidad">Especialidad</label>
                        <input type="text" id="especialidad" name="especialidad" value="<?php echo htmlspecialchars($especialidad); ?>" placeholder="Ej: Programación">
                    </div>
                    <div class="form-group">
                        <label for="turno_fijo">Turno Fijo (Por Sesión)</label>
                        <input type="text" id="turno_fijo" value="<?php echo $turno_sesion; ?>" readonly> 
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="search-button">🔍 Buscar Alumnos</button>
                    <a href="REGISTROS.php" class="clear-button">🧹 Limpiar Filtros</a> 
                </div>
            </form>
        </div>

        <section class="results-section">
            <h3 class="results-title">Resultados de la Búsqueda (<?php echo count($alumnos); ?> Alumnos)</h3>
            <p class="results-message"><?php echo $message; ?></p> 
            
            <?php if (count($alumnos) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Num. Control</th>
                            <th>Nombre Completo</th>
                            <th>Grado</th>
                            <th>Grupo</th>
                            <th>Especialidad</th>
                            <th>Docs. Registrados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alumno['num_control']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['grado']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['grupo']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['especialidad']); ?></td>
                            <td>
                                <span class="count-badge justificante-count">J: <?php echo $alumno['total_justificantes']; ?></span>
                                <span class="count-badge reporte-count">R: <?php echo $alumno['total_reportes']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </section>

    </div>
</body>
</html>