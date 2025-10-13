<?php
include("conexion.php");

// 1️⃣ Recibir datos del formulario
$Matricula = $_POST['matricula'];
$Nombre = $_POST['nombre'];
$A_paterno = $_POST['apellido_paterno'];
$A_materno = $_POST['apellido_materno'];
$Turno = $_POST['turno'];
$Especialidad = $_POST['especialidad'];
$Grupo = $_POST['grupo'];
$Motivo = $_POST['motivo'];
$Fechajustificar = $_POST['fechajustificar'];
$Sistema = $_POST['sistema'];

// Fecha actual (día en que se genera el justificante)
$Fechadedia = date('Y-m-d');

// 2️⃣ Validar Turno y Sistema
if($Turno != "Matutino" && $Turno != "Vespertino"){
    die("❌ Turno inválido.");
}

if($Sistema != "Dual" && $Sistema != "Presencial"){
    die("❌ Sistema inválido.");
}

// 3️⃣ Guardar o actualizar los datos del alumno
$checkAlumno = "SELECT * FROM alumno WHERE Matricula = '$Matricula'";
$resultado = mysqli_query($conn, $checkAlumno);

if (mysqli_num_rows($resultado) > 0) {
    // Si ya existe, actualizamos su información
    $sqlUpdate = "UPDATE alumno SET 
        Nombre='$Nombre',
        A_paterno='$A_paterno',
        A_materno='$A_materno',
        Turno='$Turno',
        Especialidad='$Especialidad',
        Grupo='$Grupo'
        WHERE Matricula='$Matricula'";
    mysqli_query($conn, $sqlUpdate);
} else {
    // Si no existe, lo insertamos
    $sqlInsert = "INSERT INTO alumno (Matricula, Nombre, A_paterno, A_materno, Turno, Especialidad, Grupo)
                  VALUES ('$Matricula', '$Nombre', '$A_paterno', '$A_materno', '$Turno', '$Especialidad', '$Grupo')";
    mysqli_query($conn, $sqlInsert);
}

$sqlJustificante = "INSERT INTO justificante (matricula, fechadedia, motivo, fechajustificar, sistema)
                    VALUES ('$Matricula', '$Fechadedia', '$Motivo', '$Fechajustificar', '$Sistema')";


if (mysqli_query($conn, $sqlJustificante)) {
    echo "<h2 style='color:green; text-align:center;'>✅ Justificante guardado correctamente.</h2>";
    echo "<p style='text-align:center;'><a href='JUSTIFICANTES.php'>Volver al formulario</a></p>";
} else {
    echo "<h2 style='color:red; text-align:center;'>❌ Error al guardar el justificante:</h2> " . mysqli_error($conn);
}

mysqli_close($conn);
?>

