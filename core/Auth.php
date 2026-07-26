<?php
/**
 * Maneja el estado de sesión del usuario logueado y el control de acceso por rol.
 * Esta es la pieza clave para que ningún usuario entre a una página que no le
 * corresponde, ni siquiera pegando la URL a mano.
 */

class Auth
{
    public static function login(array $user): void
    {
        // Regenerar el id de sesión al loguear evita session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'     => $user['id'],
            'nombre' => $user['nombre'],
            'email'  => $user['email'],
            'role'   => $user['role_nombre'], // 'administrador' | 'agente' | 'cliente'
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return self::check() && in_array(self::role(), $roles, true);
    }

    /** Exige estar logueado. Si no, redirige al login. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            set_flash('error', 'Necesitás iniciar sesión para acceder a esa página.');
            redirect('/login');
        }
    }

    /**
     * Exige estar logueado Y tener uno de los roles permitidos.
     * Si está logueado pero con otro rol, nunca lo dejamos pasar
     * (ni siquiera reescribiendo la URL a mano).
     */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            require VIEWS_DIR . '/errors/403.php';
            exit;
        }
    }
}
