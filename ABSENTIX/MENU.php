<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSENTIX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* Importación de fuentes */
@import url('https://fonts.googleapis.com/css2?family=Alan+Sans:wght@300..900&family=Playwrite+US+Modern:wght@100..400&display=swap');

/* --- Estilos Globales y Fondo --- */
body {
    margin: 0;
    font-family: "Alan Sans", sans-serif; 
    background: linear-gradient(to top, #1c1e26, #263248); 
    background-image: url('https://i.pinimg.com/1200x/04/55/40/0455409798297344219f2332ece43b8d.jpg');
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* --- CONTENEDOR DE TRANSICIÓN Y PÁGINA --- */
.page-container {
    flex: 1; 
    display: flex;
    flex-direction: column;
    align-items: center; 
    padding: 20px; /* Espacio alrededor del contenido */
    background: rgba(0, 0, 0, 0.4); 
    
    /* MODIFICACIÓN CLAVE PARA EL FADE-IN */
    opacity: 0; /* Inicia invisible */
    transition: opacity 0.5s ease-in-out; 
}

/* Estado de transición de salida (fade-out) */
.page-container.fade-out {
    opacity: 0;
    pointer-events: none; 
}

/* CLASE CLAVE: Estado de transición de entrada (fade-in) */
.page-container.fade-in {
    opacity: 1; /* Transiciona a visible al cargar */
}

/* --- ENCABEZADO SUPERIOR --- */
header {
    width: 100%;
    max-width: 450px; 
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    margin-bottom: 20px;
    color: #fff;
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

.logo-text {
    font-family: "Playwrite US Modern", cursive; 
    color: #ffffff; 
    font-size: 30px;
    margin: 0;
}
.logo-text i {
    margin-right: 10px;
    color: #9d5353; 
}


/* --- Contenedor y Caja Principal --- */
.login-container {
    flex: 1; 
    display: flex; 
    justify-content: center;
    align-items: center;
}

.login-box, .signup-box { 
    /* Efecto Glassmorphism */
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px); 
    border-radius: 40px;
    padding: 40px;
    text-align: center;
    width: 320px;
    color: white;
    
    /* Resplandor blanco exterior */
    box-shadow: 0 0 30px rgba(255, 255, 255, 1);
}


.login-box h2 {
    font-family: "Alan Sans", sans-serif; 
    color: #ffffff; 
    margin-bottom: 5px; 
    font-size: 30px; 
}

.login-box h3 {
    font-family: "Playwrite US Modern", cursive; 
    color: #ffffff; 
    font-size: 24px;
    margin-top: 10px;
    margin-bottom: 30px; 
}

/* --- Botones de Navegación Centrados --- */
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

/* ANIMACIÓN: Botón se agranda y tiene un sutil resplandor */
.login-button:hover {
    background: #7a4242; 
    transform: scale(1.05); 
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
}
</style>
</head>
<body>
    
    <div class="page-container" id="menu-page-container"> 
        
        <header>
            <h1 class="logo-text"><i class="fas fa-exclamation-triangle"></i> ABSENTIX</h1>
            <a href="LOGIN.php" class="back-link" onclick="handlePageTransition(event, this.href)">
                <i class="fas fa-arrow-left"></i> Cerrar Sesión
            </a>
        </header>

        <div class="login-container">
            <div class="login-box">
                <h2>CBTis258</h2>
                <h3>¿Qué deseas realizar hoy?</h3>
                
                <a href="JUSTIFICANTES.php" onclick="handlePageTransition(event, this.href)">
                    <button type="button" class="login-button">Justificantes</button>
                </a>
                
                <a href="REPORTES.php" onclick="handlePageTransition(event, this.href)">
                    <button type="button" class="login-button">Reportes</button>
                </a>
                
                <a href="REGISTROS.php" onclick="handlePageTransition(event, this.href)">
                    <button type="button" class="login-button">Registros</button>
                </a>
                
                <div class="separator"></div>
            </div>
        </div>
    </div>
    
    <script>
        /**
         * Función de transición de página (fade-out) - Usada para SALIR del menú
         */
        function handlePageTransition(event, url) {
            event.preventDefault(); 
            
            const pageContainer = document.querySelector('.page-container');
            
            if (pageContainer) {
                // Se asegura de que inicie la transición de salida
                pageContainer.classList.add('fade-out');
                
                // Navega a la nueva página después de que la transición haya terminado
                setTimeout(() => {
                    window.location.href = url;
                }, 500); 
            } else {
                window.location.href = url;
            }
        }

        // NUEVA LÓGICA CLAVE: FADE-IN al cargar la página del menú
        document.addEventListener('DOMContentLoaded', () => {
            const menuContainer = document.getElementById('menu-page-container');
            if (menuContainer) {
                // Elimina la clase fade-out si estaba presente y aplica el fade-in
                menuContainer.classList.remove('fade-out');
                menuContainer.classList.add('fade-in'); 
            }
        });
    </script>
</body>
</html>
