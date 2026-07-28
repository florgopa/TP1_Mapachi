<div class="section-header">
    <h1>Estado de propiedades</h1>
    <a href="<?= BASE_URL ?>/propiedades/nueva" class="btn">+ Nueva propiedad</a>
</div>

<form method="GET" class="filters" style="margin-bottom: 18px;">
    <select name="tipo">
        <option value="">Todos los tipos</option>
        <option value="venta" <?= ($filters['tipo'] ?? '') === 'venta' ? 'selected' : '' ?>>Venta</option>
        <option value="alquiler" <?= ($filters['tipo'] ?? '') === 'alquiler' ? 'selected' : '' ?>>Alquiler</option>
        <option value="pozo" <?= ($filters['tipo'] ?? '') === 'pozo' ? 'selected' : '' ?>>Pozo</option>
    </select>
    <input type="text" name="zona" placeholder="Zona" value="<?= htmlspecialchars($filters['zona'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="btn btn-secondary">Filtrar</button>
</form>

<table class="table">
    <thead>
        <tr>
            <th>Dirección</th>
            <th>Zona</th>
            <th>Tipo</th>
            <th>Ver publicación</th>
            <th>Estado</th>
            <th>Editar</th>
            <th>Eliminar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($properties as $prop): ?>
            <tr>
                <td><?= htmlspecialchars($prop['direccion'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($prop['zona'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge badge-<?= $prop['tipo'] ?>"><?= ucfirst($prop['tipo']) ?></span></td>
                <td><a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>" class="btn-link" target="_blank">Ver enlace</a></td>
                <td><span class="badge badge-estado-<?= $prop['estado'] ?>"><?= ucfirst($prop['estado']) ?></span></td>
                <td><a href="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>/editar" class="btn-link"><?= icon('edit') ?></a></td>
                <td>
                    <form action="<?= BASE_URL ?>/propiedades/<?= (int) $prop['id'] ?>/eliminar" method="POST"
                          onsubmit="return confirm('¿Eliminar esta propiedad?');" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-link btn-danger"><?= icon('trash') ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($properties)): ?>
            <tr><td colspan="7">No hay propiedades para mostrar.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require VIEWS_DIR . '/partials/pagination.php'; ?>
