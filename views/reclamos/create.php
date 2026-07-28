<h1>Nuevo reclamo</h1>

<form action="<?= BASE_URL ?>/reclamos" method="POST" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <label for="property_id">Propiedad</label>
    <select id="property_id" name="property_id" required>
        <option value="">Seleccionar propiedad...</option>
        <?php foreach ($propiedades as $p): ?>
            <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label for="titulo">Título del reclamo</label>
    <input type="text" id="titulo" name="titulo" required minlength="5" maxlength="150" placeholder="Ej: Pérdida en el techo">

    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" required minlength="10" rows="5" placeholder="Contanos con el mayor detalle posible qué está pasando..."></textarea>

    <button type="submit" class="btn btn-full">Enviar reclamo</button>
</form>
