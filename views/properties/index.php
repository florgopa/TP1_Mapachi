<section class="container section">
    <div class="section-header">
        <h1>Propiedades</h1>
        <?php if (Auth::hasRole('administrador', 'agente')): ?>
            <a href="<?= BASE_URL ?>/propiedades/nueva" class="btn">+ Nueva propiedad</a>
        <?php endif; ?>
    </div>

    <form method="GET" class="filters">
        <select name="tipo">
            <option value="">Todos los tipos</option>
            <option value="venta" <?= ($filters['tipo'] ?? '') === 'venta' ? 'selected' : '' ?>>Venta</option>
            <option value="alquiler" <?= ($filters['tipo'] ?? '') === 'alquiler' ? 'selected' : '' ?>>Alquiler</option>
            <option value="pozo" <?= ($filters['tipo'] ?? '') === 'pozo' ? 'selected' : '' ?>>Pozo</option>
        </select>
        <input type="text" name="zona" placeholder="Zona (ej: Belgrano)" value="<?= htmlspecialchars($filters['zona'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <input type="number" name="precio_min" placeholder="Precio mín." min="0" value="<?= htmlspecialchars((string) ($filters['precio_min'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="number" name="precio_max" placeholder="Precio máx." min="0" value="<?= htmlspecialchars((string) ($filters['precio_max'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <select name="dormitorios">
            <option value="">Dormitorios (mín.)</option>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>" <?= (int) ($filters['dormitorios'] ?? 0) === $i ? 'selected' : '' ?>><?= $i ?>+</option>
            <?php endfor; ?>
        </select>
        <select name="orden">
            <option value="recientes" <?= ($filters['orden'] ?? '') === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
            <option value="precio_asc" <?= ($filters['orden'] ?? '') === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
            <option value="precio_desc" <?= ($filters['orden'] ?? '') === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
    </form>

    <div class="property-grid">
        <?php foreach ($properties as $prop): ?>
            <div class="property-card">
                <div class="property-img">
                    <?php if ($prop['imagen']): ?>
                        <img src="<?= UPLOADS_URL . '/' . rawurlencode($prop['imagen']) ?>" alt="<?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php else: ?>
                        <div class="no-image">Sin imagen</div>
                    <?php endif; ?>
                </div>
                <div class="property-body">
                    <span class="badge badge-<?= $prop['tipo'] ?>"><?= ucfirst($prop['tipo']) ?></span>
                    <span class="badge badge-estado-<?= $prop['estado'] ?>"><?= ucfirst($prop['estado']) ?></span>
                    <h3><?= htmlspecialchars($prop['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="property-price">$<?= number_format((float) $prop['precio'], 0, ',', '.') ?></p>
                    <p class="property-zona">📍 <?= htmlspecialchars($prop['zona'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="property-agente">Agente: <?= htmlspecialchars($prop['agente_nombre'], ENT_QUOTES, 'UTF-8') ?></p>

                    <div class="card-actions">
                        <a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>" class="btn-link">Ver detalle</a>

                        <?php $puedeGestionar = Auth::hasRole('administrador') || (Auth::hasRole('agente') && (int) $prop['agente_id'] === Auth::id()); ?>
                        <?php if ($puedeGestionar): ?>
                            <a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>/editar" class="btn-link">Editar</a>
                            <form action="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>/eliminar" method="POST"
                                  onsubmit="return confirm('¿Seguro que querés eliminar esta propiedad?');" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="btn-link btn-danger">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($properties)): ?>
            <p>No hay propiedades para mostrar todavía.</p>
        <?php endif; ?>
    </div>

    <?php require VIEWS_DIR . '/partials/pagination.php'; ?>
</section>
