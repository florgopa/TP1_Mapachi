<h1>Reclamos</h1>

<?php if (Auth::hasRole('cliente')): ?>
    <?php if (empty($propiedades)): ?>
        <p style="color: var(--color-muted);">Todavía no tenés propiedades asociadas a tu cuenta, así que no podés levantar reclamos.</p>
    <?php else: ?>
        <h2 style="margin-top: 0;">Iniciar reclamo</h2>
        <form action="<?= BASE_URL ?>/reclamos" method="POST" class="form-card" style="margin-bottom: 30px;">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <label for="property_id">Propiedad</label>
            <select id="property_id" name="property_id" required>
                <option value="">Seleccionar propiedad...</option>
                <?php foreach ($propiedades as $p): ?>
                    <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>

            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required minlength="5" maxlength="150" placeholder="Ej: Pérdida en el techo">

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" required minlength="10" rows="4" placeholder="Contanos con el mayor detalle posible qué está pasando..."></textarea>

            <button type="submit" class="btn btn-full">Enviar reclamo</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

<h2 style="margin-top: 0;">Historial de reclamos</h2>

<?php if (empty($reclamos)): ?>
    <p style="color: var(--color-muted);">No hay reclamos para mostrar.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Propiedad</th>
                <?php if (!Auth::hasRole('cliente')): ?><th>Cliente</th><?php endif; ?>
                <th>Título</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reclamos as $r): ?>
                <tr class="expand-row" onclick="toggleReclamoRow(<?= (int) $r['id'] ?>)">
                    <td><span class="expand-icon" id="icon-r<?= (int) $r['id'] ?>">▶</span> <?= (int) $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <?php if (!Auth::hasRole('cliente')): ?><td><?= htmlspecialchars($r['cliente_nombre'], ENT_QUOTES, 'UTF-8') ?></td><?php endif; ?>
                    <td><?= htmlspecialchars($r['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge badge-reclamo-<?= $r['estado'] ?>"><?= ucfirst(str_replace('_', ' ', $r['estado'])) ?></span></td>
                    <td><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr class="expand-detail" id="detail-r<?= (int) $r['id'] ?>">
                    <td colspan="<?= Auth::hasRole('cliente') ? 5 : 6 ?>">
                        <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($r['descripcion'], ENT_QUOTES, 'UTF-8')) ?></p>

                        <?php if (!empty($r['respuesta'])): ?>
                            <p><strong>Respuesta:</strong><br><?= nl2br(htmlspecialchars($r['respuesta'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php else: ?>
                            <p style="color: var(--color-muted);">Todavía sin respuesta.</p>
                        <?php endif; ?>

                        <?php if (Auth::hasRole('administrador', 'agente')): ?>
                            <form action="<?= BASE_URL ?>/reclamos/<?= (int) $r['id'] ?>/estado" method="POST" class="form-card" onclick="event.stopPropagation()">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <label for="estado-<?= (int) $r['id'] ?>">Estado</label>
                                <select id="estado-<?= (int) $r['id'] ?>" name="estado" required>
                                    <option value="abierto" <?= $r['estado'] === 'abierto' ? 'selected' : '' ?>>Abierto</option>
                                    <option value="en_proceso" <?= $r['estado'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                                    <option value="resuelto" <?= $r['estado'] === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                                </select>
                                <label for="respuesta-<?= (int) $r['id'] ?>">Respuesta para el cliente</label>
                                <textarea id="respuesta-<?= (int) $r['id'] ?>" name="respuesta" rows="3"><?= htmlspecialchars($r['respuesta'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                <button type="submit" class="btn">Guardar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
function toggleReclamoRow(id) {
    document.getElementById('detail-r' + id).classList.toggle('open');
    const icon = document.getElementById('icon-r' + id);
    icon.closest('tr').classList.toggle('open');
}
</script>
