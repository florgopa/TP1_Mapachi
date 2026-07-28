<h1>
    <?= Auth::hasRole('administrador') ? 'Panel general' : 'Mi panel' ?>
</h1>
<p class="dashboard-greeting">Hola, <?= htmlspecialchars(Auth::user()['nombre'], ENT_QUOTES, 'UTF-8') ?> 👋</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $totalPropiedades ?></div>
            <div class="stat-label"><?= Auth::hasRole('administrador') ? 'Propiedades totales' : 'Mis propiedades' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $porEstado['disponible'] ?></div>
            <div class="stat-label">Disponibles</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $porEstado['reservada'] ?></div>
            <div class="stat-label">Reservadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $porEstado['alquilada'] + $porEstado['vendida'] ?></div>
            <div class="stat-label">Alquiladas / Vendidas</div>
        </div>

        <div class="stat-card <?= $reclamosAbiertos > 0 ? 'stat-card--accent' : '' ?>">
            <div class="stat-number"><?= $reclamosAbiertos ?></div>
            <div class="stat-label">Reclamos abiertos</div>
            <?php if ($reclamosAbiertos > 0): ?>
                <a href="<?= BASE_URL ?>/reclamos" class="btn-link stat-link">Ver reclamos →</a>
            <?php endif; ?>
        </div>

        <?php if (Auth::hasRole('administrador')): ?>
            <div class="stat-card stat-card--accent">
                <div class="stat-number"><?= $totalUsuarios ?></div>
                <div class="stat-label">Usuarios registrados</div>
            </div>
            <div class="stat-card stat-card--accent">
                <div class="stat-number"><?= $mensajesSinLeer ?></div>
                <div class="stat-label">Mensajes sin leer</div>
                <?php if ($mensajesSinLeer > 0): ?>
                    <a href="<?= BASE_URL ?>/mensajes" class="btn-link stat-link">Ver mensajes →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-columns">
        <div>
            <h2>Propiedades por tipo</h2>
            <table class="table">
                <thead>
                    <tr><th>Tipo</th><th>Cantidad</th></tr>
                </thead>
                <tbody>
                    <tr><td><span class="badge badge-venta">Venta</span></td><td><?= $porTipo['venta'] ?></td></tr>
                    <tr><td><span class="badge badge-alquiler">Alquiler</span></td><td><?= $porTipo['alquiler'] ?></td></tr>
                    <tr><td><span class="badge badge-pozo">Pozo</span></td><td><?= $porTipo['pozo'] ?></td></tr>
                </tbody>
            </table>

            <?php if (Auth::hasRole('administrador')): ?>
                <h2 style="margin-top: 24px;">Usuarios por rol</h2>
                <table class="table">
                    <thead>
                        <tr><th>Rol</th><th>Cantidad</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Administrador</td><td><?= $porRol['administrador'] ?></td></tr>
                        <tr><td>Agente</td><td><?= $porRol['agente'] ?></td></tr>
                        <tr><td>Cliente</td><td><?= $porRol['cliente'] ?></td></tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div>
            <h2><?= Auth::hasRole('administrador') ? 'Últimas propiedades cargadas' : 'Mis últimas propiedades' ?></h2>
            <?php if (empty($recientes)): ?>
                <p>Todavía no hay propiedades cargadas.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr><th>Título</th><th>Tipo</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recientes as $prop): ?>
                            <tr>
                                <td><?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><span class="badge badge-<?= $prop['tipo'] ?>"><?= ucfirst($prop['tipo']) ?></span></td>
                                <td><span class="badge badge-estado-<?= $prop['estado'] ?>"><?= ucfirst($prop['estado']) ?></span></td>
                                <td><a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>" class="btn-link">Ver</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if (Auth::hasRole('administrador') && !empty($mensajesRecientes)): ?>
                <h2 style="margin-top: 24px;">Últimos mensajes de contacto</h2>
                <table class="table">
                    <thead>
                        <tr><th>Nombre</th><th>Mensaje</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajesRecientes as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars(mb_strimwidth($m['mensaje'], 0, 60, '…'), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="<?= BASE_URL ?>/propiedades/nueva" class="btn">+ Nueva propiedad</a>
        <a href="<?= BASE_URL ?>/propiedades" class="btn btn-secondary">Ver todas las propiedades</a>
        <?php if (Auth::hasRole('administrador')): ?>
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-secondary">Gestionar usuarios</a>
        <?php endif; ?>
    </div>
