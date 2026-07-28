    <a href="<?= BASE_URL ?>/comprobantes" class="btn-link">← Volver a comprobantes</a>

    <h1>Comprobante de pago</h1>
    <span class="badge badge-comp-<?= $comprobante['estado'] ?>"><?= ucfirst($comprobante['estado']) ?></span>

    <div class="info-bar" style="margin-top: 16px;">
        <p><strong>Propiedad:</strong> <?= htmlspecialchars($comprobante['property_titulo'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!Auth::hasRole('cliente')): ?>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($comprobante['cliente_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <p><strong>Monto:</strong> <?= $comprobante['monto'] ? '$' . number_format((float) $comprobante['monto'], 0, ',', '.') : 'No especificado' ?></p>
        <p><strong>Fecha:</strong> <?= htmlspecialchars($comprobante['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if (!empty($comprobante['archivo'])): ?>
        <p>
            <a href="<?= UPLOADS_URL . '/' . rawurlencode($comprobante['archivo']) ?>" target="_blank" class="btn-link">
                <?= icon('paperclip') ?> Ver archivo adjunto
            </a>
        </p>
    <?php endif; ?>

    <?php if (!empty($comprobante['nota_admin'])): ?>
        <h3>Nota</h3>
        <div class="info-bar"><p><?= nl2br(htmlspecialchars($comprobante['nota_admin'], ENT_QUOTES, 'UTF-8')) ?></p></div>
    <?php endif; ?>

    <?php if (Auth::hasRole('administrador', 'agente')): ?>
        <h3 style="margin-top: 24px;">Revisar comprobante</h3>
        <form action="<?= BASE_URL ?>/comprobantes/<?= (int) $comprobante['id'] ?>/estado" method="POST" class="form-card">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <option value="pendiente" <?= $comprobante['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="aprobado" <?= $comprobante['estado'] === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                <option value="rechazado" <?= $comprobante['estado'] === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
            </select>

            <label for="nota_admin">Nota (opcional)</label>
            <textarea id="nota_admin" name="nota_admin" rows="3"><?= htmlspecialchars($comprobante['nota_admin'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

            <button type="submit" class="btn btn-full">Guardar</button>
        </form>
    <?php endif; ?>
