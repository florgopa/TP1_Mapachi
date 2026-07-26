<section class="container section narrow">
    <h1>Contactanos</h1>
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
