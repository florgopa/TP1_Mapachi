<?php
/**
 * Variables esperadas: $property (array|null), $agentes, $clientes, $csrf, $action
 */
$p = $property ?? [];
$val = fn(string $key, $default = '') => htmlspecialchars((string) ($p[$key] ?? $default), ENT_QUOTES, 'UTF-8');
?>
<form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <label for="titulo">Título</label>
    <input type="text" id="titulo" name="titulo" value="<?= $val('titulo') ?>" required minlength="5" maxlength="150">

    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" required minlength="10" rows="4"><?= $val('descripcion') ?></textarea>

    <div class="form-row">
        <div>
            <label for="tipo">Tipo</label>
            <select id="tipo" name="tipo" required>
                <?php foreach (['venta' => 'Venta', 'alquiler' => 'Alquiler', 'pozo' => 'Pozo'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($p['tipo'] ?? '') === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
                <?php foreach (['disponible' => 'Disponible', 'reservada' => 'Reservada', 'alquilada' => 'Alquilada', 'vendida' => 'Vendida'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($p['estado'] ?? 'disponible') === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div>
            <label for="precio">Precio (USD)</label>
            <input type="number" id="precio" name="precio" value="<?= $val('precio') ?>" required min="1" step="0.01">
        </div>
        <div>
            <label for="zona">Zona / Barrio</label>
            <input type="text" id="zona" name="zona" value="<?= $val('zona') ?>" required maxlength="100">
        </div>
    </div>

    <label for="direccion">Dirección</label>
    <input type="text" id="direccion" name="direccion" value="<?= $val('direccion') ?>" required maxlength="200">

    <div class="form-row">
        <div>
            <label for="dormitorios">Dormitorios</label>
            <input type="number" id="dormitorios" name="dormitorios" value="<?= $val('dormitorios', 0) ?>" min="0" max="20">
        </div>
        <div>
            <label for="banos">Baños</label>
            <input type="number" id="banos" name="banos" value="<?= $val('banos', 0) ?>" min="0" max="10">
        </div>
    </div>

    <?php if (Auth::hasRole('administrador')): ?>
        <label for="agente_id">Agente responsable</label>
        <select id="agente_id" name="agente_id" required>
            <option value="">Seleccionar agente...</option>
            <?php foreach ($agentes as $a): ?>
                <option value="<?= (int) $a['id'] ?>" <?= (int) ($p['agente_id'] ?? 0) === (int) $a['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <label for="propietario_id">Propietario / Inquilino asociado (opcional)</label>
    <select id="propietario_id" name="propietario_id">
        <option value="">Sin asignar</option>
        <?php foreach ($clientes as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= (int) ($p['propietario_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="imagen">Foto de la propiedad <?= $property ? '(dejar vacío para no cambiarla)' : '(opcional)' ?></label>
    <input type="file" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp">

    <button type="submit" class="btn btn-full"><?= $property ? 'Guardar cambios' : 'Crear propiedad' ?></button>
</form>
