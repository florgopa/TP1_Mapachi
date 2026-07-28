<section class="hero">
    <div class="container">
        <h1>Tu próxima propiedad a un clic de distancia</h1>
        <p>Venta, alquiler y propiedades en pozo. Te acompañamos en cada paso.</p>
    </div>
</section>

<section class="stats-bar">
    <div class="container stats-bar-inner">
        <div class="stats-bar-item">
            <span class="stats-bar-number"><?= $stats['totalPropiedades'] ?>+</span>
            <span class="stats-bar-label">Propiedades publicadas</span>
        </div>
        <div class="stats-bar-item">
            <span class="stats-bar-number"><?= $stats['zonas'] ?></span>
            <span class="stats-bar-label">Zonas cubiertas</span>
        </div>
        <div class="stats-bar-item">
            <span class="stats-bar-number"><?= $stats['porTipo']['venta'] ?></span>
            <span class="stats-bar-label">En venta</span>
        </div>
        <div class="stats-bar-item">
            <span class="stats-bar-number"><?= $stats['porTipo']['alquiler'] ?></span>
            <span class="stats-bar-label">En alquiler</span>
        </div>
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

<section class="services-section">
    <div class="container">
        <h2 style="text-align: center;">Lo que hacemos por vos</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><?= icon('home') ?></div>
                <h3>Venta</h3>
                <p>Te ayudamos a encontrar (o vender) la propiedad ideal, con acompañamiento en cada paso de la operación.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><?= icon('key') ?></div>
                <h3>Alquiler</h3>
                <p>Gestión completa de alquileres residenciales y comerciales, con contratos claros y seguimiento de pagos.</p>
            </div>
            <div class="service-card">
                <div class="service-icon"><?= icon('crane') ?></div>
                <h3>Pozo</h3>
                <p>Invertí en propiedades en construcción con planes de pago flexibles y seguimiento del proyecto.</p>
            </div>
        </div>
    </div>
</section>

<section class="nosotros-section" id="nosotros">
<div class="container section">
    <h2>Sobre Mapachi Inmobiliaria</h2>
    <p class="section-lead">
        Somos una inmobiliaria dedicada a ayudarte a encontrar el lugar perfecto, ya sea que estés
        buscando tu primer hogar, una propiedad para inversión o un lugar ideal para alquilar.
        Con años de experiencia en el sector, ofrecemos un servicio personalizado y profesional,
        adaptado a tus necesidades.
    </p>

    <h3>¿Por qué elegirnos?</h3>
    <div class="why-grid">
        <div class="why-card">
            <div class="why-icon"><?= icon('grid') ?></div>
            <p>Amplia gama de propiedades: desde departamentos modernos hasta casas en venta o alquiler.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><?= icon('shield') ?></div>
            <p>Proceso transparente y sin complicaciones, acompañándote en cada paso.</p>
        </div>
        <div class="why-card">
            <div class="why-icon"><?= icon('heart') ?></div>
            <p>Compromiso con el cliente: tu satisfacción es nuestra prioridad.</p>
        </div>
    </div>

    <h3>Cómo funciona</h3>
    <div class="steps-grid">
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-icon"><?= icon('mail') ?></div>
            <h4>Nos contactás</h4>
            <p>Nos escribís por el formulario de contacto o elegís una propiedad del catálogo.</p>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-icon"><?= icon('edit') ?></div>
            <h4>Firmás el contrato</h4>
            <p>Nuestro equipo te acompaña en toda la gestión, sin sorpresas ni letra chica.</p>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-icon"><?= icon('dashboard') ?></div>
            <h4>Accedés a tu panel</h4>
            <p>Con tu cuenta habilitada, gestionás pagos, reclamos y todo lo demás desde un solo lugar.</p>
        </div>
    </div>
</section>

</div>
<section class="testimonials-section">
    <div class="container">
        <h2 style="text-align: center;">Lo que dicen nuestros clientes</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-avatar">M</div>
                <p class="testimonial-text">"Encontramos nuestro depto en menos de dos semanas. El agente nos acompañó en todo el proceso, muy recomendable."</p>
                <p class="testimonial-author">Marcos D. — Propietario</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-avatar">L</div>
                <p class="testimonial-text">"La gestión del alquiler es súper simple, subo el comprobante desde el celular y ya está. Cero vueltas."</p>
                <p class="testimonial-author">Lucía F. — Inquilina</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-avatar">T</div>
                <p class="testimonial-text">"Invertí en un proyecto en pozo y pude seguir cada etapa de la construcción sin llamar por teléfono ni una vez."</p>
                <p class="testimonial-author">Tomás I. — Inversor</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section" id="contacto">
    <div class="container">
        <h2>Contactanos</h2>
        <p class="contact-lead">Dejanos tu consulta y te respondemos a la brevedad.</p>

        <div class="contact-grid">
            <div class="contact-form-col">
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
            </div>

            <div class="contact-map-col">
                <iframe
                    src="https://www.google.com/maps?q=Av.+Cabildo+2447,+Buenos+Aires,+Argentina&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="Ubicación de Mapachi Inmobiliaria">
                </iframe>
            </div>
        </div>
    </div>
</section>
