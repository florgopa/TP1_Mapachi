<section class="container section narrow">
    <a href="<?= BASE_URL ?>/propiedades" class="btn-link">← Volver al listado</a>

    <div class="property-detail">
        <div class="property-img-large">
            <?php if ($property['imagen']): ?>
                <img src="<?= UPLOADS_URL . '/' . rawurlencode($property['imagen']) ?>" alt="<?= htmlspecialchars($property['titulo'], ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div class="no-image">Sin imagen</div>
            <?php endif; ?>
        </div>

        <span class="badge badge-<?= $property['tipo'] ?>"><?= ucfirst($property['tipo']) ?></span>
        <span class="badge badge-estado-<?= $property['estado'] ?>"><?= ucfirst($property['estado']) ?></span>

        <h1><?= htmlspecialchars($property['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="property-price">$<?= number_format((float) $property['precio'], 0, ',', '.') ?></p>
        <p><?= icon('pin') ?> <?= htmlspecialchars($property['zona'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($property['direccion'], ENT_QUOTES, 'UTF-8') ?></p>
        <p>🛏 <?= (int) $property['dormitorios'] ?> dormitorios · 🛁 <?= (int) $property['banos'] ?> baños</p>

        <h3>Descripción</h3>
        <p><?= nl2br(htmlspecialchars($property['descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>

        <?php if (Auth::hasRole('administrador') || (Auth::hasRole('agente') && (int) $property['agente_id'] === Auth::id())): ?>
            <h3>Datos de gestión</h3>
            <p>Agente responsable: <?= htmlspecialchars($property['agente_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
            <p>Propietario/Inquilino asociado: <?= htmlspecialchars($property['propietario_nombre'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8') ?></p>
        <?php else: ?>
            <h3>Contacto</h3>
            <p>¿Te interesa esta propiedad? <a href="<?= BASE_URL ?>/contacto">Escribinos</a> y te contactamos.</p>
        <?php endif; ?>
    </div>
</section>
