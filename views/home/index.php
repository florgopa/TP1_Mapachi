<section class="hero">
    <div class="container">
        <h1>Tu próxima propiedad a un clic de distancia</h1>
        <p>Venta, alquiler y propiedades en pozo. Te acompañamos en cada paso.</p>
    </div>
</section>

<section class="container section" id="propiedades-destacadas">
    <h2>Propiedades destacadas</h2>
    <div class="property-grid">
        <?php foreach ($destacadas as $prop): ?>
            <div class="property-card">
                <div class="property-img">
                    <?php if ($prop['imagen']): ?>
                        <img src="<?= UPLOADS_URL . '/' . rawurlencode($prop['imagen']) ?>" alt="<?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php else: ?>
                        <div class="no-image">Sin imagen</div>
                    <?php endif; ?>
                </div>
                <div class="property-body">
                    <span class="badge badge-<?= $prop['tipo'] ?>"><?= ucfirst($prop['tipo']) ?></span>
                    <h3><?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="property-price">$<?= number_format((float) $prop['precio'], 0, ',', '.') ?></p>
                    <p class="property-zona"><?= htmlspecialchars($prop['zona'], ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>" class="btn-link">Ver detalle</a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($destacadas)): ?>
            <p>Todavía no hay propiedades cargadas.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="<?= BASE_URL ?>/propiedades" class="btn">Ver catálogo completo</a>
        <?php if (!Auth::check()): ?>
            <a href="<?= BASE_URL ?>/login" class="btn-link" style="margin-left: 12px;">¿Sos agente o cliente? Ingresá acá</a>
        <?php endif; ?>
    </div>
</section>

<section class="container section" id="nosotros">
    <h2>Sobre Mapachi Inmobiliaria</h2>
    <p>
        Somos una inmobiliaria dedicada a ayudarte a encontrar el lugar perfecto, ya sea que estés
        buscando tu primer hogar, una propiedad para inversión o un lugar ideal para alquilar.
        Con años de experiencia en el sector, ofrecemos un servicio personalizado y profesional,
        adaptado a tus necesidades.
    </p>

    <h3>¿Por qué elegirnos?</h3>
    <ul>
        <li>Amplia gama de propiedades: desde departamentos modernos hasta casas en venta o alquiler.</li>
        <li>Proceso transparente y sin complicaciones, acompañándote en cada paso.</li>
        <li>Compromiso con el cliente: tu satisfacción es nuestra prioridad.</li>
    </ul>

    <h3>Nuestros servicios</h3>
    <ul>
        <li>Venta de propiedades</li>
        <li>Alquileres residenciales y comerciales</li>
        <li>Compra de propiedades en pozo (sobre planos)</li>
    </ul>

    <h3>Cómo funciona el sistema</h3>
    <p>
        La creación de cuentas de usuario es un proceso exclusivo de nuestro equipo administrativo,
        que habilita el acceso una vez firmado el contrato correspondiente. Cada usuario (agente,
        propietario/inquilino) cuenta con un panel personalizado según su rol dentro de la plataforma.
    </p>
</section>

<section class="container section narrow" id="contacto">
    <h2>Contactanos</h2>
    <p>Dejanos tu consulta y te respondemos a la brevedad.</p>

    <div class="info-bar">
        <p><strong>Dirección:</strong> Av. Cabildo 2447, CABA, Buenos Aires</p>
        <p><strong>Teléfono:</strong> +54 9 11 1234-5678</p>
        <p><strong>Horarios:</strong> Lunes a Viernes - 9:00 a 16:00</p>
    </div>

    <form action="<?= BASE_URL ?>/contacto" method="POST" class="form-card">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required maxlength="100">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required maxlength="150">

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Ej. +54 9 123 456 7890" maxlength="30">

        <label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje" required maxlength="1000" rows="5"></textarea>

        <button type="submit" class="btn">Enviar mensaje</button>
    </form>
</section>
