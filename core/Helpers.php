<?php
/**
 * Funciones auxiliares de uso general: sanitización, redirecciones,
 * flash messages, paginación y protección CSRF.
 */

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
