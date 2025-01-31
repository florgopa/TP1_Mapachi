<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inmobiliaria</title>
    <link rel="stylesheet" href="Styles.css">
</head>
<body>
    <!-- Header -->
    <header id="Header">
        <div class="contenido-header">
            <div class="barra">
                <a href="/TP_Mapachi/Index.html">
                    <img src="./images/Sin título-1.png" alt="Logo de la empresa" class="logo">
                </a>
                <nav class="navbar">
                    <a href="{{ url('/') }}">Inicio</a>
                    <a href="Nosotros.html">Nosotros</a>
                    <a href="Contacto.html">Contacto</a>
                    <a href="{{ url('/login') }}">Mi cuenta</a>
                </nav>
            </div>
        </div>
    </header>

        <!--Barra Contacto-->
        <section class="info-bar">
            <div class="container">
                <p class="info-item"><strong>Dirección:</strong> Av. Cabildo 2447, CABA, Buenos Aires</p>
                <p class="info-item"><strong>Teléfono:</strong> +54 9 11 1234-5678</p>
                <p class="info-item"><strong>Horarios:</strong> Lunes a Viernes - 9:00 AM a 4:00 PM</p>
            </div>
        </section>

    <!-- Login -->
    <section class="login">
        <h2>Acceso al sistema</h2>
        <p>Ingresa tu usuario designado y contraseña para acceder a tu cuenta.</p>
        <form action="/login" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Usuario designado" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn">Iniciar sesión</button>
        </form>
        <div class="login-options">
            <p><a href="#">¿Olvidaste tu contraseña?</a></p>
            <p>¿Tienes problemas para ingresar? <a href="#">Comunícate con nosotros</a></p>
        </div>
    </section>    

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Servicios</a></li>
                    <li><a href="#">Contacto</a></li>
                    <li><a href="#">Sobre Nosotros</a></li>
                </ul>
            </div>
            
            <div class="footer-center">
                <img src="../TP_Mapachi/images/Beige.png" alt="Mapachi Inmobiliaria Logo" class="logo">
            </div>
            
            <div class="footer-right">
                <img src="../TP_Mapachi/images/002-instagram.png" alt="Ir a Instagram" class="social-icon">
                <img src="../TP_Mapachi/images/003-whatsapp.png" alt="Ir a Whatsapp" class="social-icon">
                <img src="../TP_Mapachi/images/001-tik-tok.png" alt="Ir a TikTok" class="social-icon">
            </div>
        </div>
        <p>&copy; 2024 Mapachi Inmobiliaria - Todos los derechos reservados.</p>
    </footer>   
    
</body>
</html>