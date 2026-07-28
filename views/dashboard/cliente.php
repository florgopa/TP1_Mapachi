<h1>Mi panel</h1>
<p class="dashboard-greeting">Hola, <?= htmlspecialchars(Auth::user()['nombre'], ENT_QUOTES, 'UTF-8') ?> 👋</p>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= count($propiedades) ?></div>
        <div class="stat-label">Propiedades asociadas</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= count($contratos) ?></div>
        <div class="stat-label">Contratos activos</div>
    </div>
    <div class="stat-card <?= count($reclamos) > 0 ? 'stat-card--accent' : '' ?>">
        <div class="stat-number"><?= count($reclamos) ?></div>
        <div class="stat-label">Reclamos recientes</div>
        <a href="<?= BASE_URL ?>/reclamos" class="btn-link stat-link">Ver reclamos →</a>
    </div>
</div>

<h2>Mis propiedades</h2>
<?php if (empty($propiedades)): ?>
    <p>Todavía no tenés propiedades asociadas a tu cuenta.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>Propiedad</th><th>Tipo</th><th>Rol</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($propiedades as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-<?= $p['tipo'] ?>"><?= ucfirst($p['tipo']) ?></span></td>
                    <td>
                        <?= (int) $p['propietario_id'] === Auth::id() ? 'Dueño' : '' ?>
                        <?= (int) $p['inquilino_id'] === Auth::id() ? 'Inquilino' : '' ?>
                    </td>
                    <td><a href="<?= BASE_URL ?>/propiedades/<?= (int) $p['id'] ?>" class="btn-link">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="dashboard-actions">
    <a href="<?= BASE_URL ?>/reclamos" class="btn">Reclamos</a>
    <a href="<?= BASE_URL ?>/contratos" class="btn btn-secondary">Estado de alquiler</a>
    <a href="<?= BASE_URL ?>/comprobantes" class="btn btn-secondary">Comprobantes</a>
</div>
