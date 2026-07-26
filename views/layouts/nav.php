<header class="site-header">
    <div class="container header-inner">
        <a href="<?= BASE_URL ?>/" class="logo">
            <img src="<?= BASE_URL ?>/public/images/logolargomapachi.svg" alt="Mapachi Inmobiliaria" class="logo-img-long">
        </a>
        <nav class="navbar">
            <a href="<?= BASE_URL ?>/">Inicio</a>
            <a href="<?= BASE_URL ?>/#nosotros">Nosotros</a>
            <a href="<?= BASE_URL ?>/propiedades">Propiedades</a>
            <a href="<?= BASE_URL ?>/#contacto">Contacto</a>

            <?php if (Auth::check()): ?>
                <?php if (Auth::hasRole('administrador')): ?>
                    <a href="<?= BASE_URL ?>/usuarios">Usuarios</a>
                    <a href="<?= BASE_URL ?>/mensajes">Mensajes</a>
                <?php endif; ?>

                <span class="nav-user">👤 <?= htmlspecialchars(Auth::user()['nombre'], ENT_QUOTES, 'UTF-8') ?>
                    <small>(<?= htmlspecialchars(Auth::role(), ENT_QUOTES, 'UTF-8') ?>)</small>
                </span>
                <a href="<?= BASE_URL ?>/logout" class="btn-link">Cerrar sesión</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="btn-link">Mi cuenta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
