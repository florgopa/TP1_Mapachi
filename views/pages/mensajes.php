    <h1>Mensajes de contacto</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Mensaje</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mensajes as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['telefono'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= nl2br(htmlspecialchars($m['mensaje'], ENT_QUOTES, 'UTF-8')) ?></td>
                    <td><?= htmlspecialchars($m['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($mensajes)): ?>
                <tr><td colspan="5">No hay mensajes todavía.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php require VIEWS_DIR . '/partials/pagination.php'; ?>
