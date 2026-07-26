<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Mapachi Inmobiliaria</title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/public/images/logomapachi.svg">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="login-body">
    <?php $flash = get_flash(); ?>
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="login-box">
        <img src="<?= BASE_URL ?>/public/images/logolargomapachi.svg" alt="Mapachi Inmobiliaria" class="login-logo">
        <h2>Acceso al sistema</h2>
        <form action="<?= BASE_URL ?>/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-full">Iniciar sesión</button>
        </form>

        <div class="login-hint">
            <p><strong>Usuarios de prueba</strong> (contraseña: <code>123456</code>)</p>
            <ul>
                <li>admin@mapachi.com — Administrador</li>
                <li>agente@mapachi.com — Agente</li>
                <li>cliente@mapachi.com — Cliente</li>
            </ul>
        </div>
        <a href="<?= BASE_URL ?>/" class="btn-link">← Volver al inicio</a>
    </div>
</body>
</html>