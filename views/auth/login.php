<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Mapachi Inmobiliaria</title>
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/public/images/logomapachi.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="login-body">
    <?php $flash = get_flash(); ?>
    <main class="login-main">
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" style="position: absolute; top: 0; left: 0; right: 0;">
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

            <a href="<?= BASE_URL ?>/" class="btn-link">← Volver al inicio</a>
        </div>
    </main>

    <?php require VIEWS_DIR . '/layouts/footer.php'; ?>
