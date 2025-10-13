<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = ""; // en XAMPP normalmente está vacío
$bd = "justificanteor";

$conn = mysqli_connect($servidor, $usuario, $contrasena, $bd);

if (!$conn) {
    die("❌ Error al conectar con la base de datos: " . mysqli_connect_error());
}
?>
