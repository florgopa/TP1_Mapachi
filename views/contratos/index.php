    <div class="section-header">
        <h1>Contratos de alquiler</h1>
        <?php if (Auth::hasRole('administrador', 'agente')): ?>
            <a href="<?= BASE_URL ?>/contratos/nuevo" class="btn">+ Nuevo contrato</a>
        <?php endif; ?>
    </div>

    <?php if (Auth::hasRole('cliente')): ?>
        <?php if (!empty($comoInquilino)): ?>
            <h2>Como inquilino</h2>
            <table class="table">
                <thead><tr><th>Propiedad</th><th>Monto inicial</th><th>Inicio</th><th>Duración</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($comoInquilino as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= number_format((float) $c['monto_inicial'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($c['fecha_inicio'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $c['duracion_meses'] ?> meses</td>
                            <td><a href="<?= BASE_URL ?>/contratos/<?= (int) $c['id'] ?>" class="btn-link">Ver cuotas</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($comoPropietario)): ?>
            <h2 style="margin-top: 24px;">Como propietario</h2>
            <table class="table">
                <thead><tr><th>Propiedad</th><th>Inquilino</th><th>Monto inicial</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($comoPropietario as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['inquilino_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= number_format((float) $c['monto_inicial'], 0, ',', '.') ?></td>
                            <td><a href="<?= BASE_URL ?>/contratos/<?= (int) $c['id'] ?>" class="btn-link">Ver estado de pagos</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (empty($comoInquilino) && empty($comoPropietario)): ?>
            <p>No tenés contratos de alquiler asociados todavía.</p>
        <?php endif; ?>
    <?php else: ?>
        <?php if (empty($contratos)): ?>
            <p>No hay contratos cargados todavía.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>Propiedad</th><th>Inquilino</th><th>Propietario</th><th>Inicio</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($contratos as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['property_titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['inquilino_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['propietario_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['fecha_inicio'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= ucfirst($c['estado']) ?></td>
                            <td><a href="<?= BASE_URL ?>/contratos/<?= (int) $c['id'] ?>" class="btn-link">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
