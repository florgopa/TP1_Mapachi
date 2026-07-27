<section class="container section">
    <div class="section-header">
        <h1>Reclamos</h1>
        <?php if (Auth::hasRole('cliente')): ?>
            <a href="<?= BASE_URL ?>/reclamos/nuevo" class="btn">+ Nuevo reclamo</a>
        <?php endif; ?>
    </div>

    <?php if (empty($reclamos)): ?>
        <p>No hay reclamos para mostrar.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <?php if (!Auth::hasRole('cliente')): ?><th>Cliente</th><?php endif; ?>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reclamos as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <?php if (!Auth::hasRole('cliente')): ?><td><?= htmlspecialchars($r['cliente_nombre'], ENT_QUOTES, 'UTF-8') ?></td><?php endif; ?>
                        <td><?= htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge badge-reclamo-<?= $r['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $r['estado'])) ?></span></td>
                        <td><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a href="<?= BASE_URL ?>/reclamos/<?= (int) $r['id'] ?>" class="btn-link">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php require VIEWS_DIR . '/partials/pagination.php'; ?>
    <?php endif; ?>
</section>
