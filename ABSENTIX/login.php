<?php
// ===============================================
// 1. LÓGICA DE AUTENTICACIÓN REAL (INTEGRADA)
// ===============================================
session_start();

// Inicializar variables y credenciales
$mensaje_error = '';
$login_exitoso = false; 

// Base de datos de usuarios (hardcodeados)
$users = [
    'orientacionmat258' => ['password' => 'orienmat', 'turno' => 'Matutino'], 
    'orientacionves258' => ['password' => 'orienves', 'turno' => 'Vespertino'], 
];

// Inicializar las variables del formulario (para evitar warnings al inicio)
$usuario = ''; 

// Verificar si se ha enviado el formulario de login (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar y limpiar (sanitizar) los valores del formulario
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    // 1. Verificar si el usuario existe
    if (isset($users[$usuario])) {
        $user_data = $users[$usuario];
        
        // 2. Verificar la contraseña
        if ($contrasena === $user_data['password']) {
            
            // 3. Inicio de sesión exitoso
            // GUARDAR VARIABLES CRÍTICAS EN SESIÓN
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $usuario;
            $_SESSION['turno'] = $user_data['turno'];
            
            // Marcar la bandera para que el JS haga la transición suave al cargar el HTML
            $login_exitoso = true; 
            
        } else {
            // Contraseña incorrecta
            $mensaje_error = "Contraseña incorrecta para el usuario: $usuario.";
        }
    } else {
        // Usuario no encontrado
        $mensaje_error = "Usuario '$usuario' no registrado en el sistema.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX - Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* ... (El CSS se mantiene igual para coherencia de estilo) ... */
@import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');
body {
    margin: 0; font-family: "Alan Sans", sans-serif; color: #fff;
    background: linear-gradient(to top, #1c1e26, #263248);
    background-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
    background-size: cover; background-position: center; background-attachment: fixed; 
}
.page-container {
    min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; 
    padding: 20px; background: rgba(0, 0, 0, 0.4); opacity: 1; transition: opacity 0.5s ease-in-out; 
}
.page-container.fade-out { opacity: 0; pointer-events: none; }
.login-section {
    background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border-radius: 20px;
    padding: 40px; width: 100%; max-width: 450px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 
    0 0 30px rgba(255, 255, 255, 0.3); border: 1px solid rgba(255, 255, 255, 0.18); text-align: center;
}
.welcome-message { color: #ccc; font-size: 18px; font-weight: 300; margin-bottom: 5px; }
h1 {
    font-size: 32px; color: #fff; margin-top: 0; margin-bottom: 30px; border-bottom: 2px solid #9d5353;
    padding-bottom: 10px; display: inline-block;
}
h1 i { margin-right: 10px; color: #9d5353; }
.form-group { margin-bottom: 20px; text-align: left; }
.password-container { position: relative; }
.toggle-password {
    position: absolute; top: 50%; right: 15px; transform: translateY(-50%); color: #ccc;
    cursor: pointer; transition: color 0.3s; font-size: 18px; padding: 5px; 
}
.toggle-password:hover { color: #9d5353; }
label { margin-bottom: 8px; font-weight: 600; color: #ccc; font-size: 14px; display: block; }
input[type="text"], input[type="password"] { 
    padding: 12px 15px; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px;
    background: rgba(255, 255, 255, 0.1); color: #fff; font-size: 16px; transition: border-color 0.3s, background 0.3s;
    outline: none; width: 100%; box-sizing: border-box;
}
input[type="text"]:focus, input[type="password"]:focus { border-color: #9d5353; background: rgba(255, 255, 255, 0.15); }
.btn-login {
    width: 100%; padding: 12px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;
    transition: background 0.3s; font-size: 18px; background: #3f92bb; color: white; margin-top: 15px;
}
.btn-login:hover { background: #347fa6; }
.mensaje-error {
    margin-top: 20px; padding: 10px; background-color: rgba(157, 83, 83, 0.8); border-radius: 8px;
    color: #fff; font-weight: bold; font-size: 14px;
}
@media (max-width: 600px) {
    .login-section { padding: 30px; margin: 20px; }
    h1 { font-size: 28px; }
}
</style>
</head>
<body>

    <div class="page-container">
        
        <main class="login-section">
            
            <p class="welcome-message">Bienvenido a ABSENTIX</p>

            <h1><i class="fas fa-user-shield"></i> Iniciar Sesión</h1>

            <?php if (!empty($mensaje_error)): ?>
                <div class="mensaje-error">
                    <?php echo htmlspecialchars($mensaje_error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="loginForm">
                
                <div class="form-group">
                    <label for="usuario"><i class="fas fa-user"></i> Usuario</label>
                    <input type="text" name="usuario" id="usuario" required value="<?php echo htmlspecialchars($usuario); ?>">
                </div>
                
                <div class="form-group">
                    <label for="contrasena"><i class="fas fa-lock"></i> Contraseña</label>
                    <div class="password-container">
                        <input type="password" name="contrasena" id="contrasena" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Entrar al Sistema</button>
            </form>

        </main>
    </div>

<script>
    /**
     * Función que alterna entre mostrar y ocultar la contraseña.
     */
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('contrasena');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
    
    /**
     * Función de transición de página (fade-out) para la navegación.
     */
    function handlePageTransition(url) {
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

    // LÓGICA DE REDIRECCIÓN CON TRANSICIÓN SUAVE SI EL LOGIN FUE EXITOSO
    <?php if ($login_exitoso): ?>
        // Si PHP marcó el login como exitoso, iniciamos la transición al cargar la página.
        window.onload = function() {
             handlePageTransition('MENU.php');
        };
    <?php endif; ?>

</script>

</body>
</html>