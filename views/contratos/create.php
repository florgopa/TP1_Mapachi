    <h1>Nuevo contrato de alquiler</h1>

    <form action="<?= BASE_URL ?>/contratos" method="POST" class="form-card">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <label for="property_id">Propiedad (solo alquileres)</label>
        <select id="property_id" name="property_id" required>
            <option value="">Seleccionar propiedad...</option>
            <?php foreach ($propiedades as $p): ?>
                <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['titulo'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="inquilino_id">Inquilino</label>
        <select id="inquilino_id" name="inquilino_id" required>
            <option value="">Seleccionar cliente...</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>

        <label for="propietario_id">Propietario (dueño)</label>
        <select id="propietario_id" name="propietario_id" required>
            <option value="">Seleccionar cliente...</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <small style="color: var(--color-muted);">Si todavía no cargaste un cliente distinto para el dueño, podés repetir el mismo por ahora.</small>

        <div class="form-row">
            <div>
                <label for="monto_inicial">Monto inicial mensual</label>
                <input type="number" id="monto_inicial" name="monto_inicial" min="1" step="0.01" required>
            </div>
            <div>
                <label for="fecha_inicio">Fecha de inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label for="duracion_meses">Duración (meses)</label>
                <input type="number" id="duracion_meses" name="duracion_meses" min="1" max="60" value="24" required>
            </div>
            <div>
                <label for="frecuencia_ajuste_meses">Ajuste cada (meses)</label>
                <select id="frecuencia_ajuste_meses" name="frecuencia_ajuste_meses" required>
                    <option value="3" selected>3 (trimestral)</option>
                    <option value="4">4 (cuatrimestral)</option>
                    <option value="6">6 (semestral)</option>
                    <option value="12">12 (anual)</option>
                    <option value="1">1 (mensual)</option>
                </select>
            </div>
        </div>

        <p style="font-size: .85rem; color: var(--color-muted);">
            Se van a generar automáticamente todas las cuotas del contrato. Las del primer
            período quedan con el monto inicial; las siguientes quedan "a convenir" hasta
            que se calculen por ICL (BCRA) o se carguen a mano.
        </p>

        <button type="submit" class="btn btn-full">Crear contrato y generar cuotas</button>
    </form>
