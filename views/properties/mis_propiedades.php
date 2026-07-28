<section class="container section">
    <h1>Mis propiedades</h1>
    <p style="color: var(--color-muted); margin-top: -10px;">Propiedades asociadas a tu cuenta como propietario/inquilino.</p>

    <?php if (empty($propiedades)): ?>
        <p>Todavía no tenés propiedades asociadas a tu cuenta. Si creés que es un error, contactanos.</p>
    <?php else: ?>
        <div class="property-grid">
            <?php foreach ($propiedades as $prop): ?>
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
                        <span class="badge badge-estado-<?= $prop['estado'] ?>"><?= ucfirst($prop['estado']) ?></span>
                        <?php if ((int) $prop['propietario_id'] === Auth::id()): ?>
                            <span class="badge badge-cuota-pagado">Sos el dueño</span>
                        <?php endif; ?>
                        <?php if ((int) $prop['inquilino_id'] === Auth::id()): ?>
                            <span class="badge badge-cuota-pendiente">Sos el inquilino</span>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="property-zona">📍 <?= htmlspecialchars($prop['zona'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="property-agente">Agente: <?= htmlspecialchars($prop['agente_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
                        <a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>" class="btn-link">Ver detalle</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-actions">
        <a href="<?= BASE_URL ?>/contratos" class="btn">Ver mis contratos y cuotas</a>
        <a href="<?= BASE_URL ?>/reclamos/nuevo" class="btn btn-secondary">Levantar un reclamo</a>
        <a href="<?= BASE_URL ?>/comprobantes/nuevo" class="btn btn-secondary">Subir comprobante de pago</a>
    </div>
</section>
