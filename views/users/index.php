<div class="section-header">
    <h1>Usuarios</h1>
    <a href="<?= BASE_URL ?>/usuarios/nuevo" class="btn">+ Nuevo usuario</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <?php $tieneDetalle = $u['role_nombre'] === 'cliente'; ?>
            <tr class="<?= $tieneDetalle ? 'expand-row' : '' ?>" <?= $tieneDetalle ? 'onclick="toggleUserRow(' . (int) $u['id'] . ')"' : '' ?>>
                <td>
                    <?php if ($tieneDetalle): ?><span class="expand-icon" id="icon-<?= (int) $u['id'] ?>">▶</span><?php endif; ?>
                    <div>
                        <strong><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                        <small style="color: var(--color-muted);"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></small>
                    </div>
                </td>
                <td><?= htmlspecialchars(ucfirst($u['role_nombre']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $u['activo'] ? '✅ Activo' : '⛔ Inactivo' ?></td>
                <td class="card-actions" onclick="event.stopPropagation()">
                    <a href="<?= BASE_URL ?>/usuarios/<?= (int) $u['id'] ?>/editar" class="btn-link">Editar</a>
                    <?php if ((int) $u['id'] !== Auth::id()): ?>
                        <form action="<?= BASE_URL ?>/usuarios/<?= (int) $u['id'] ?>/eliminar" method="POST"
                              onsubmit="return confirm('¿Eliminar este usuario?');" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn-link btn-danger">Eliminar</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($tieneDetalle): ?>
                <tr class="expand-detail" id="detail-<?= (int) $u['id'] ?>">
                    <td colspan="4">
                        <?php if (empty($u['propiedades'])): ?>
                            <p style="margin: 0; color: var(--color-muted);">Sin propiedades asociadas.</p>
                        <?php else: ?>
                            <table class="expand-subtable">
                                <thead>
                                    <tr><th>Propiedad</th><th>Dirección</th><th>Tipo</th><th>Rol</th><th>Estado</th><th>Reclamos</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($u['propiedades'] as $i => $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($p['direccion'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= ucfirst($p['tipo']) ?></td>
                                            <td>
                                                <?= (int) $p['propietario_id'] === (int) $u['id'] ? 'Dueño' : '' ?>
                                                <?= (int) $p['inquilino_id'] === (int) $u['id'] ? 'Inquilino' : '' ?>
                                            </td>
                                            <td><?= ucfirst($p['estado']) ?></td>
                                            <td><?= $i === 0 ? (int) $u['reclamos_count'] . ' en total' : '' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function toggleUserRow(id) {
    const detail = document.getElementById('detail-' + id);
    const icon = document.getElementById('icon-' + id);
    detail.classList.toggle('open');
    icon.parentElement.parentElement.classList.toggle('open');
}
</script>
