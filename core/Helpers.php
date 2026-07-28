<?php
/**
 * Funciones auxiliares de uso general: sanitización, redirecciones,
 * flash messages, paginación y protección CSRF.
 */

/**
 * Devuelve el SVG de un ícono de línea (estilo consistente, sin depender de
 * ninguna librería externa ni CDN). Usa currentColor, así hereda el color
 * del texto/link que lo contiene.
 */
function icon(string $name, string $class = 'icon'): string
{
    $icons = [
        'home'      => '<path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V9.5Z"/>',
        'key'       => '<circle cx="7" cy="15" r="4"/><path d="m10.5 11.5 8-8M16 6l2 2M19 3l2 2"/>',
        'crane'     => '<path d="M4 21V9l9-6 9 6M4 21h18M9 21V11M9 11h9M18 11V6"/>',
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'users'     => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14a5 5 0 0 1 5.5 5"/>',
        'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'clipboard' => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1M9 11h6M9 15h6"/>',
        'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18M8 15h.01M12 15h.01M16 15h.01"/>',
        'receipt'   => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6"/>',
        'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>',
        'logout'    => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/>',
        'pin'       => '<path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/>',
        'star'      => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21.1 7 14.2l-5-4.9 6.9-1L12 2Z"/>',
        'paperclip' => '<path d="M21 12.5 12.5 21a5 5 0 0 1-7-7L14 5.5a3.5 3.5 0 0 1 5 5L10.5 19a2 2 0 0 1-3-3L15 8.5"/>',
        'edit'      => '<path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'trash'     => '<path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0-1 14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 6h14ZM10 11v6M14 11v6"/>',
        'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'shield'    => '<path d="M12 3 5 6v6c0 5 3.4 7.5 7 9 3.6-1.5 7-4 7-9V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
        'heart'     => '<path d="M12 21s-7-4.5-9.5-9C1 8.5 2 5 5.5 5c2 0 3.3 1.2 4 2.3.7-1.1 2-2.3 4-2.3 3.5 0 4.5 3.5 3 7-2.5 4.5-9.5 9-9.5 9Z"/>',
        'arrow-up' => '<path d="M12 19V5M5 12l7-7 7 7"/>',
    ];
    $paths = $icons[$name] ?? '';
    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}

/** Limpia texto libre (nombres, descripciones, mensajes) */
function clean_string($value): string
{
    $value = trim($value ?? '');
    $value = strip_tags($value);
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/** Valida y castea un entero positivo (ideal para ids que vienen por URL/GET) */
function clean_int($value, int $default = 0): int
{
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    return $filtered !== false && $filtered > 0 ? $filtered : $default;
}

/** Valida un float (precios) */
function clean_float($value, float $default = 0.0): float
{
    $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
    return $filtered !== false ? $filtered : $default;
}

/** Valida un email */
function clean_email($value): string|false
{
    $value = trim($value ?? '');
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

/** Redirige a una ruta interna y corta la ejecución */
function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/** Guarda un mensaje flash para mostrar en la próxima request */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Devuelve (y borra) el mensaje flash actual, si existe */
function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Genera (o reutiliza) el token CSRF de la sesión actual */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Valida el token CSRF recibido por POST */
function csrf_verify(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Calcula datos de paginación a partir del total de registros.
 * Devuelve ['page' => int, 'offset' => int, 'totalPages' => int]
 */
function paginate(int $totalItems, int $perPage = ITEMS_PER_PAGE): array
{
    $page = clean_int($_GET['page'] ?? 1, 1);
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    return ['page' => $page, 'offset' => $offset, 'perPage' => $perPage, 'totalPages' => $totalPages];
}
