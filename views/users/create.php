<h1>Nuevo usuario</h1>
<form action="<?= BASE_URL ?>/usuarios" method="POST" class="form-card">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <label for="nombre">Nombre completo</label>
    <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="100">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required maxlength="150">

    <label for="telefono">Teléfono</label>
    <input type="tel" id="telefono" name="telefono" maxlength="30">

    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" required minlength="6">

    <label for="role_id">Rol</label>
    <select id="role_id" name="role_id" required>
        <option value="1">Administrador</option>
        <option value="2">Agente Inmobiliario</option>
        <option value="3" selected>Cliente (Propietario/Inquilino)</option>
    </select>

    <label class="checkbox-label">
        <input type="checkbox" name="activo" checked> Usuario activo
    </label>

    <button type="submit" class="btn btn-full">Crear usuario</button>
</form>
