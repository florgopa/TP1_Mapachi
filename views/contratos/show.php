<?php
$esInquilino = Auth::hasRole('cliente') && (int) $contrato['inquilino_id'] === Auth::id();
$esPropietarioViendo = Auth::hasRole('cliente') && (int) $contrato['propietario_id'] === Auth::id();
$puedeGestionar = Auth::hasRole('administrador', 'agente');
?>
    <a href="<?= BASE_URL ?>/contratos" class="btn-link">← Volver a contratos</a>

    <h1><?= htmlspecialchars($contrato['property_titulo'], ENT_QUOTES, 'UTF-8') ?></h1>

    <div class="info-bar">
        <p><strong>Inquilino:</strong> <?= htmlspecialchars($contrato['inquilino_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Propietario:</strong> <?= htmlspecialchars($contrato['propietario_nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Inicio:</strong> <?= htmlspecialchars($contrato['fecha_inicio'], ENT_QUOTES, 'UTF-8') ?> — <strong>Duración:</strong> <?= (int) $contrato['duracion_meses'] ?> meses</p>
        <p><strong>Monto inicial:</strong> $<?= number_format((float) $contrato['monto_inicial'], 0, ',', '.') ?> — <strong>Ajuste cada:</strong> <?= (int) $contrato['frecuencia_ajuste_meses'] ?> meses (ICL - BCRA)</p>
    </div>

    <?php if ($esPropietarioViendo): ?>
        <p style="color: var(--color-muted); font-style: italic;">📖 Estás viendo esto como propietario, en modo solo lectura.</p>
    <?php endif; ?>

    <?php if ($puedeGestionar): ?>
        <form action="<?= BASE_URL ?>/contratos/<?= (int) $contrato['id'] ?>/actualizar-icl" method="POST" style="margin: 16px 0;">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <button type="submit" class="btn btn-secondary">🔄 Actualizar cuotas "a convenir" con el ICL del BCRA</button>
        </form>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Comprobante</th>
                <?php if ($puedeGestionar): ?><th>Gestión</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cuotas as $cuota): ?>
                <tr>
                    <td><?= (new DateTime($cuota['mes_vencimiento']))->format('m/Y') ?></td>
                    <td>
                        <?= $cuota['monto'] !== null ? '$' . number_format((float) $cuota['monto'], 0, ',', '.') : '<span style="color: var(--color-muted);">A convenir</span>' ?>
                    </td>
                    <td>
                        <span class="badge badge-cuota-<?= $cuota['estado'] ?>">
                            <?= ['a_convenir' => 'A convenir', 'pendiente' => 'Sin pagar', 'pagado' => 'Pagado', 'vencido' => 'Vencido'][$cuota['estado']] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($cuota['archivo']): ?>
                            <a href="<?= UPLOADS_URL . '/' . rawurlencode($cuota['archivo']) ?>" target="_blank" class="btn-link"><?= icon('paperclip') ?> Descargar</a>
                        <?php elseif ($esInquilino && $cuota['estado'] === 'pendiente'): ?>
                            <form action="<?= BASE_URL ?>/cuotas/<?= (int) $cuota['id'] ?>/comprobante" method="POST" enctype="multipart/form-data" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required style="max-width: 160px; display: inline-block;">
                                <button type="submit" class="btn-link">Subir</button>
                            </form>
                        <?php else: ?>
                            <span style="color: var(--color-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($puedeGestionar): ?>
                        <td>
                            <?php if ($cuota['estado'] === 'a_convenir'): ?>
                                <form action="<?= BASE_URL ?>/cuotas/<?= (int) $cuota['id'] ?>/monto" method="POST" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="number" name="monto" placeholder="Monto" min="1" step="0.01" style="width: 90px; display: inline-block;" required>
                                    <button type="submit" class="btn-link">Cargar</button>
                                </form>
                            <?php else: ?>
                                <form action="<?= BASE_URL ?>/cuotas/<?= (int) $cuota['id'] ?>/estado" method="POST" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <select name="estado" onchange="this.form.submit()" style="font-size: .8rem;">
                                        <option value="pendiente" <?= $cuota['estado'] === 'pendiente' ? 'selected' : '' ?>>Sin pagar</option>
                                        <option value="pagado" <?= $cuota['estado'] === 'pagado' ? 'selected' : '' ?>>Pagado</option>
                                        <option value="vencido" <?= $cuota['estado'] === 'vencido' ? 'selected' : '' ?>>Vencido</option>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
