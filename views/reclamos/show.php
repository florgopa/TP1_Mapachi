<section class="container section narrow">
    <a href="<?= BASE_URL ?>/reclamos" class="btn-link">← Volver a reclamos</a>

    <h1><?= htmlspecialchars($reclamo['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
    <span class="badge badge-reclamo-<?= $reclamo['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $reclamo['estado'])) ?></span>

    <div class="info-bar" style="margin-top: 16px;">
        <p><strong>Propiedad:</strong> <?= htmlspecialchars($reclamo['property_titulo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!Auth::hasRole('cliente')): ?>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($reclamo['cliente_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($reclamo['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <h3>Descripción</h3>
    <p><?= nl2br(htmlspecialchars($reclamo['descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>

    <?php if (!empty($reclamo['respuesta'])): ?>
        <h3>Respuesta</h3>
        <div class="info-bar"><p><?= nl2br(htmlspecialchars($reclamo['respuesta'], ENT_QUOTES, 'UTF-8')) ?></p></div>
    <?php endif; ?>

    <?php if (Auth::hasRole('administrador', 'agente')): ?>
        <h3 style="margin-top: 24px;">Actualizar reclamo</h3>
        <form action="<?= BASE_URL ?>/reclamos/<?= (int) $reclamo['id'] ?>/estado" method="POST" class="form-card">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <option value="abierto" <?= $reclamo['estado'] === 'abierto' ? 'selected' : '' ?>>Abierto</option>
                <option value="en_proceso" <?= $reclamo['estado'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                <option value="resuelto" <?= $reclamo['estado'] === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
            </select>

            <label for="respuesta">Respuesta para el cliente</label>
            <textarea id="respuesta" name="respuesta" rows="4"><?= htmlspecialchars($reclamo['respuesta'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

            <button type="submit" class="btn btn-full">Guardar</button>
        </form>
    <?php endif; ?>
</section>
