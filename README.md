<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ABSENTIX</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="logo-text">ABSENTIX</h1>
            <h2>Iniciar Sesión</h2>
            
            <form id="loginForm">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" placeholder="Nombre de usuario" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" placeholder="Contraseña" required>
                </div>
                
                <p id="errorMessage" class="error-message"></p>
                
                <button type="submit" class="login-button" a href="MENU.php">Entrar</button>
            </form>
            
            <div class="separator"></div>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
  
    event.preventDefault(); 
    

    const usernameInput = document.getElementById('username').value;
    const passwordInput = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');

    // 2. Definir las credenciales solicitadas
    const correctUsername = "orientacionedu258";
    const correctPassword = "orien258";

    // 3. Limpiar mensajes de error previos
    errorMessage.textContent = "";

    // 4. Realizar la verificación
    if (usernameInput === correctUsername && passwordInput === correctPassword) {
        // Credenciales correctas
        // Redirige al menú principal (simulación)
        window.location.href = "menu.php"; 
    } else {
        // Credenciales incorrectas
        errorMessage.textContent = "Usuario o contraseña incorrectos. Intenta de nuevo.";
    }
});
    </script>
</body>
</html>
