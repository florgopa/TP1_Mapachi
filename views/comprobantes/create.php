<section class="container section narrow">
    <h1>Subir comprobante de pago</h1>

    <form action="<?= BASE_URL ?>/comprobantes" method="POST" enctype="multipart/form-data" class="form-card">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <label for="property_id">Propiedad</label>
        <select id="property_id" name="property_id" required>
            <option value="">Seleccionar propiedad...</option>
            <?php foreach ($propiedades as $p): ?>
                <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="monto">Monto pagado (opcional)</label>
        <input type="number" id="monto" name="monto" min="0" step="0.01" placeholder="Ej: 450">

        <label for="archivo">Comprobante (PDF, JPG o PNG, máx. 5MB)</label>
        <input type="file" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>

        <button type="submit" class="btn btn-full">Subir comprobante</button>
    </form>
</section>
