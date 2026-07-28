    <div class="section-header">
        <h1>Comprobantes de pago</h1>
        <?php if (Auth::hasRole('cliente')): ?>
            <a href="<?= BASE_URL ?>/comprobantes/nuevo" class="btn">+ Subir comprobante</a>
        <?php endif; ?>
    </div>

    <?php if (empty($comprobantes)): ?>
        <p>No hay comprobantes para mostrar.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <?php if (!Auth::hasRole('cliente')): ?><th>Cliente</th><?php endif; ?>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comprobantes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php if (!Auth::hasRole('cliente')): ?><td><?= htmlspecialchars($c['cliente_nombre'], ENT_QUOTES, 'UTF-8') ?></td><?php endif; ?>
                        <td><?= $c['monto'] ? '$' . number_format((float) $c['monto'], 0, ',', '.') : '—' ?></td>
                        <td><span class="badge badge-comp-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                        <td><?= htmlspecialchars($c['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a href="<?= BASE_URL ?>/comprobantes/<?= (int) $c['id'] ?>" class="btn-link">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php require VIEWS_DIR . '/partials/pagination.php'; ?>
    <?php endif; ?>
