<?php
/**
 * Controlador base: todos los controladores heredan de acá.
 * Centraliza el renderizado de vistas dentro del layout general.
 */

abstract class Controller
{
    /**
     * Renderiza una vista dentro del layout (header/nav/footer).
     * $view es la ruta relativa dentro de /views, sin extensión. Ej: 'properties/index'
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $flash = get_flash();

        require VIEWS_DIR . '/layouts/header.php';
        require VIEWS_DIR . '/layouts/nav.php';
        require VIEWS_DIR . '/' . $view . '.php';
        require VIEWS_DIR . '/layouts/footer.php';
    }

    /** Renderiza una vista dentro del layout de panel (sidebar + card), para páginas de gestión */
    protected function renderDashboard(string $view, array $data = []): void
    {
        extract($data);
        $flash = get_flash();

        require VIEWS_DIR . '/layouts/dashboard_header.php';
        require VIEWS_DIR . '/layouts/sidebar.php';
        require VIEWS_DIR . '/' . $view . '.php';
        require VIEWS_DIR . '/layouts/dashboard_footer.php';
    }

    /** Renderiza una vista "standalone", sin el layout (ej: login) */
    protected function renderPlain(string $view, array $data = []): void
    {
        extract($data);
        require VIEWS_DIR . '/' . $view . '.php';
    }
}
