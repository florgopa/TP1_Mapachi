<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../images/favicon-32x32.png">   

    <title>Mapachi Inmobiliaria</title>

    <link rel="stylesheet" href="Styles.css">

    <link rel="stylesheet" href="{{ asset('css/Styles.css') }}">
    <script src="{{ asset('js/ui.js') }}"></script>
    <!-- <img src="{{ asset('images/favicon-32x32.png') }}" alt="Logo"> se pone un cuadrado en blanco ni idea--> 
    
</head>
<body>

<!-- Header -->
<header id="Header">
    <div class="contenido-header">
        <div class="barra">
            <a href="/TP_Mapachi/Index.blade.php">
                <img src="./images/Sin título-1.png" alt="Logo de la empresa" class="logo" id="logo-large">
                <img src="./images/Naranja.png" alt="Logo pequeño" class="logo" id="logo-small">
            </a>
            <nav class="navbar">
                <a href="{{ url('/index') }}">Inicio</a>
                <a href="">Nosotros</a>
                <a href="#contacto">Contacto</a>
                <a href="{{ url('/login') }}">Mi cuenta</a>
            </nav>
            <!-- Menú hamburguesa -->
            <div class="menu-hamburguesa" id="hamburger-icon">
                <img src="images/menu.png" class="hamburger-icon" alt="Menu">
            </div>
        </div>
    </div>
    <h1 class="hh1">Tu próxima casa a un clic de distancia</h1>

    <!-- Overlay para el menú hamburguesa -->
    <div class="menu-overlay" id="menu-overlay">
        <nav class="overlay-navbar">
            <a href="{{ url('/index') }}">Inicio</a>
            <a href="">Nosotros</a>
            <a href="#contacto">Contacto</a>
            <a href="{{ url('/login') }}">Mi cuenta</a>
        </nav>
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
    
    <!--Barra de búsqueda-->
    <section class="search">
        <div class="search-container">
            <h2>Encontrá tu hogar</h2>
            <form class="search-form">
                <div class="search-inputs">
                    <div class="input-group">
                        <label for="property-type">Tipo de propiedad</label>
                        <select id="property-type" name="property-type">
                            <option value="casa">Casa</option>
                            <option value="departamento">Departamento</option>
                            <option value="local">Local comercial</option>
                        </select>
                    </div>
    
                    <div class="input-group">
                        <label for="city">Ciudad o Zona</label>
                        <select id="city" name="city">
                            <option value="palermo">Palermo</option>
                            <option value="belgrano">Belgrano</option>
                            <option value="caballito">Caballito</option>
                            <option value="flores">Flores</option>
                            <option value="la_matanza">La Matanza</option>
                            <option value="nunez">Núñez</option>
                            <option value="villa_del_parque">Villa del Parque</option>
                            <option value="liniers">Liniers</option>
                        </select>
                    </div>
    
                    <div class="input-group">
                        <label for="price-range">Rango de precio</label>
                        <input type="text" id="price-range" name="price-range" placeholder="Ejemplo: 100000 - 500000" />
                    </div>
    
                    <!-- Botón de búsqueda -->
                    <button type="submit" class="search-btn">Buscar propiedades</button>
                </div>
            </form>
        </div>
    </section>    

    <!-- Servicios-->
    <section class="services">
        <h2>Nosotros</h2>
        <p>
            Nuestra misión es ayudarte a encontrar el lugar perfecto, ya sea que estés buscando tu primer hogar, una propiedad para inversión o un lugar ideal para alquilar. Con años de experiencia en el sector inmobiliario, ofrecemos un servicio personalizado y profesional, adaptado a tus necesidades.
        </p>
        <p>
            <strong>¿Por qué elegirnos?</strong>
        </p>
        <ul>
            <li>Amplia gama de propiedades: Desde departamentos modernos hasta casas en venta o alquiler, tenemos opciones para todos los gustos y presupuestos.</li>
            <li>Proceso transparente y sin complicaciones: Nos encargamos de todos los detalles, asegurándonos de que tu experiencia sea fluida y sin sorpresas. Ya sea para alquilar o comprar una propiedad, estarás acompañado en cada paso.</li>
            <li>Compromiso con el cliente: Tu satisfacción es nuestra prioridad. Nos esforzamos por ofrecerte un servicio de calidad, con atención al detalle y un enfoque único para cada cliente.</li>
            <br>
        
        </ul>
        <p>
            <strong>Nuestros servicios:</strong>
        </p>
        <ul>
            <li>Venta de propiedades</li>
            <li>Alquileres residenciales y comerciales</li>
            <li>Compra de propiedades en pozo</li>
        </ul>
        <p>
            <br>
            ¡Contáctanos hoy mismo! Estamos aquí para ayudarte en todo lo que necesites. Si tienes preguntas, dudas o quieres agendar una cita, no dudes en ponerte en contacto con nosotros a través de nuestro formulario de contacto o por teléfono. En Mapachi Inmobiliaria, trabajamos para hacer realidad tus proyectos.<br>
        
        </p>
        </div>
    </section>

    <h2 class="hh2"><br>Propiedades destacadas</h1>
    
    <!-- Carrousel -->
    <section class="properties">
        <div class="property-card">
            <img src="../images/casa1.png" alt="Casa en venta">
            <h3>Casa en venta - $120,000</h3>
            <p>Hermosa casa con 3 habitaciones, 2 baños y jardín privado.</p>
        </div>
        <div class="property-card">
            <img src="../images/Depto2.png" alt="Departamento en venta">
            <h3>Departamento en venta - $85,000</h3>
            <p>Moderno departamento de 2 habitaciones con vista al mar.</p>
        </div>
        <div class="property-card">
            <img src="../images/Local3.png" alt="Local comercial">
            <h3>Local comercial en alquiler - $1,500/mes</h3>
            <p>Local amplio en una excelente ubicación céntrica.</p>
        </div>
    </section>

    <!--Contacto-->
    <section id="contacto">
        <h2>Contáctanos</h2>
        <form action="#" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
    
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
    
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej. +54 9 123 456 7890" required>
    
            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" required></textarea>
    
            <button type="submit">Enviar</button>
        </form>
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
                <img src="../images/Beige.png" alt="Mapachi Inmobiliaria Logo" class="logo">
            </div>
            
            <div class="footer-right">
                <img src="../images/002-instagram.png" alt="Ir a Instagram" class="social-icon">
                <img src="../images/003-whatsapp.png" alt="Ir a Whatsapp" class="social-icon">
                <img src="../images/001-tik-tok.png" alt="Ir a TikTok" class="social-icon">
            </div>
        </div>
        <p>&copy; 2024 Mapachi Inmobiliaria - Todos los derechos reservados.</p>
    </footer>    
    
    <script src="ui.js"></script>
</body>
</html>