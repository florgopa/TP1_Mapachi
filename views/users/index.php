<section class="container section">
    <div class="section-header">
        <h1>Usuarios</h1>
        <a href="<?= BASE_URL ?>/usuarios/nuevo" class="btn">+ Nuevo usuario</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(ucfirst($u['role_nombre']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $u['activo'] ? '✅ Activo' : '⛔ Inactivo' ?></td>
                    <td class="card-actions">
                        <a href="<?= BASE_URL ?>/usuarios/<?= (int) $u['id'] ?>/editar" class="btn-link">Editar</a>
                        <?php if ((int) $u['id'] !== Auth::id()): ?>
                            <form action="<?= BASE_URL ?>/usuarios/<?= (int) $u['id'] ?>/eliminar" method="POST"
                                  onsubmit="return confirm('¿Eliminar este usuario?');" class="inline-form">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="btn-link btn-danger">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
