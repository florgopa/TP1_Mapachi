<h1>Editar usuario</h1>
<form action="<?= BASE_URL ?>/usuarios/<?= (int) $user['id'] ?>" method="POST" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <label for="nombre">Nombre completo</label>
    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?>" required minlength="3" maxlength="100">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="150">

    <label for="telefono">Teléfono</label>
    <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="30">

    <label for="password">Nueva contraseña (dejar vacío para no cambiarla)</label>
    <input type="password" id="password" name="password" minlength="6">

    <label for="role_id">Rol</label>
    <select id="role_id" name="role_id" required <?= (int) $user['id'] === Auth::id() ? 'disabled' : '' ?>>
        <option value="1" <?= (int) $user['role_id'] === 1 ? 'selected' : '' ?>>Administrador</option>
        <option value="2" <?= (int) $user['role_id'] === 2 ? 'selected' : '' ?>>Agente Inmobiliario</option>
        <option value="3" <?= (int) $user['role_id'] === 3 ? 'selected' : '' ?>>Cliente (Propietario/Inquilino)</option>
    </select>
    <?php if ((int) $user['id'] === Auth::id()): ?>
        <input type="hidden" name="role_id" value="<?= (int) $user['role_id'] ?>">
        <small>No podés cambiar tu propio rol.</small>
    <?php endif; ?>

    <label class="checkbox-label">
        <input type="checkbox" name="activo" <?= $user['activo'] ? 'checked' : '' ?> <?= (int) $user['id'] === Auth::id() ? 'disabled' : '' ?>>
        Usuario activo
    </label>
    <?php if ((int) $user['id'] === Auth::id()): ?>
        <input type="hidden" name="activo" value="1">
    <?php endif; ?>

    <button type="submit" class="btn btn-full">Guardar cambios</button>
</form>
