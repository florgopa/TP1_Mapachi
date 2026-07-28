<?php
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if (BASE_PATH !== '' && str_starts_with($currentPath, BASE_PATH)) {
    $currentPath = substr($currentPath, strlen(BASE_PATH));
}
$isActive = fn(string $path) => $currentPath === $path || str_starts_with($currentPath, $path . '/') ? 'active' : '';

$user = Auth::user();
$inicial = mb_strtoupper(mb_substr($user['nombre'], 0, 1));
?>
<aside class="dashboard-sidebar">
    <a href="<?= BASE_URL ?>/" class="dashboard-logo">
        <img src="<?= BASE_URL ?>/public/images/logolargomapachi.svg" alt="Mapachi Inmobiliaria">
    </a>

    <div class="dashboard-user">
        <div class="dashboard-avatar"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></div>
        <p class="dashboard-username"><?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="dashboard-userrole">(<?= htmlspecialchars(ucfirst(Auth::role()), ENT_QUOTES, 'UTF-8') ?>)</p>
    </div>

    <nav class="dashboard-nav">
        <a href="<?= BASE_URL ?>/dashboard" class="<?= $isActive('/dashboard') ?>">📊 Panel</a>

        <?php if (Auth::hasRole('administrador', 'agente')): ?>
            <a href="<?= BASE_URL ?>/propiedades" class="<?= $isActive('/propiedades') ?>">🏠 Propiedades</a>
        <?php endif; ?>

        <?php if (Auth::hasRole('administrador')): ?>
            <a href="<?= BASE_URL ?>/usuarios" class="<?= $isActive('/usuarios') ?>">👥 Usuarios</a>
            <a href="<?= BASE_URL ?>/mensajes" class="<?= $isActive('/mensajes') ?>">✉️ Mensajes</a>
        <?php endif; ?>

        <?php if (Auth::hasRole('cliente')): ?>
            <a href="<?= BASE_URL ?>/mis-propiedades" class="<?= $isActive('/mis-propiedades') ?>">🏠 Mis propiedades</a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/reclamos" class="<?= $isActive('/reclamos') ?>">📋 Reclamos</a>
        <a href="<?= BASE_URL ?>/contratos" class="<?= $isActive('/contratos') ?>">📅 <?= Auth::hasRole('cliente') ? 'Estado de alquiler' : 'Contratos' ?></a>
        <a href="<?= BASE_URL ?>/comprobantes" class="<?= $isActive('/comprobantes') ?>">🧾 Comprobantes</a>
        <a href="<?= BASE_URL ?>/perfil" class="<?= $isActive('/perfil') ?>">👤 Mi perfil</a>
    </nav>

    <a href="<?= BASE_URL ?>/logout" class="dashboard-logout">⏻ Cerrar sesión</a>
</aside>

<main class="dashboard-main">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" style="margin-bottom: 16px; border-radius: var(--radius);">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <div class="dashboard-card">