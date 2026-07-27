<section class="container section narrow">
    <h1>Mi perfil</h1>
    <p style="color: var(--color-muted); margin-top: -10px;">
        Rol: <strong><?= htmlspecialchars(ucfirst(Auth::role()), ENT_QUOTES, 'UTF-8') ?></strong>
        (si necesitás cambiar tu rol, pedíselo a un administrador)
    </p>

    <form action="<?= BASE_URL ?>/perfil" method="POST" class="form-card">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?>" required minlength="3" maxlength="100">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" required maxlength="150">

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="30">

        <hr style="margin: 16px 0; border: none; border-top: 1px solid var(--color-border);">

        <label for="password">Nueva contraseña (dejar vacío para no cambiarla)</label>
        <input type="password" id="password" name="password" minlength="6">

        <label for="password_confirm">Repetir nueva contraseña</label>
        <input type="password" id="password_confirm" name="password_confirm" minlength="6">

        <button type="submit" class="btn btn-full">Guardar cambios</button>
    </form>
</section>
