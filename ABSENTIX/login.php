<?php 
// Incluir el archivo de conexión para iniciar sesión y acceder a la conexión si fuera necesario
include_once 'conexion.php'; 

$error_message = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'pass') {
        $error_message = "Contraseña incorrecta. Intenta de nuevo.";
    } elseif ($_GET['error'] == 'user') {
        $error_message = "Usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ABSENTIX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* (Mismos estilos CSS que proporcionaste anteriormente) */
    @import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');

body {
    margin: 0;
    font-family: "Playwrite US Modern", cursive; 
    background: linear-gradient(to top, #1c1e26, #263248); 
    background-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
    background-size: cover;
    background-position: center;
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.navbar { 
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
    color: white;
    backdrop-filter: blur(10px); /* Efecto blur */
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 25px;
}

.navbar ul li a {
    text-decoration: none;
    color: white;
    font-size: 18px;
}

.search input {
    padding: 5px 10px;
    border-radius: 15px;
    border: none;
}

/* --- Contenedor Principal (Aplica tanto a Login como a Registro) --- */
.login-container, .container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}


.login-box, .signup-box { 
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px); /* Efecto Glassmorphism */
    border-radius: 40px;
    padding: 40px;
    text-align: center;
    width: 320px;
    color: white;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
}

.login-box h2 {
    color: #ffffffff; 
    margin-bottom: 15px; 
    font-size: 28px;
}

.logo-text {
    color: #ffffffff; 
    font-size: 40px;
    margin-bottom: 5px;
}

.login-box input, .signup-box input {
    width: calc(100% - 20px); 
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 8px;
    outline: none;
}

.input-group { 
    display: flex;
    align-items: center;
    background: rgba(240, 225, 225, 0.66); 
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 0 10px;
}

.input-group i {
    color: #9d5353; 
    margin-right: 10px;
}

.input-group input {
    flex-grow: 1;
    border: none;
    background: none; 
    padding: 10px 0;
    margin: 0;
    /* CORRECCIÓN 1: Color de texto oscuro para que se vea */
    color: #1c1e26; 
}

/* CORRECCIÓN 1 ADICIONAL: Color del placeholder */
.input-group input::placeholder {
    color: rgba(28, 30, 38, 0.7); 
}


/* --- Botones --- */
.login-button, .signup-box button {
    width: 100%;
    padding: 10px;
    border: none;
    background: #9d5353; 
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none; 
    display: inline-block; 
}

.login-button:hover, .signup-box button:hover {
    background: #9d5353;
}

/* Mensaje de Error */
.error-message {
    color: #ff6b6b; /* Un rojo suave para el error */
    margin-bottom: 15px;
    font-weight: bold;
}
.login-box, .signup-box {

    box-shadow: 
      0 0 30px rgba(255, 255, 255, 1);

}
.login-box a {
    display: block; 
    text-decoration: none;
    margin-bottom: 15px; 
}

.login-button {
    width: 100%;
    padding: 12px 0; 
    border: none;
    background: #9d5353; 
    color: white;
    font-weight: bold;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s; 
    text-transform: uppercase;
}


.login-button:hover {
    background: #7a4242; 
    transform: scale(1.05); 
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo-text">ABSENTIX</h1>
            <h2>Iniciar Sesión</h2>
            
            <form action="validar_login.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Nombre de usuario" required> 
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Contraseña" required> 
                </div>
                
                <p id="errorMessage" class="error-message"><?php echo $error_message; ?></p> 
                
                <button type="submit" class="login-button">Entrar</button>
                
            </form>
            
            <div class="separator"></div>
        </div>
    </div>
    </body>
</html>