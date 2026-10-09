<?php
/**
 * Configuración general de la aplicación.
 * Se incluye en todos los puntos de entrada.
 */

// Mostrar errores en desarrollo (poner en 0 antes de exponer en producción)
error_reporting(E_ALL);

$isProduction = getenv('APP_ENV') === 'production';

ini_set('display_errors', $isProduction ? '0' : '1');
ini_set('log_errors', '1');

// Sesión: la iniciamos una sola vez acá
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta base del proyecto dentro del dominio (NO incluye protocolo ni host,
// así funciona sin importar si entrás por localhost, 127.0.0.1, una IP de red, etc.)
// Ejemplo: si accedés como http://localhost/mapachi/ dejar '/mapachi'
define('BASE_PATH', '');
define('BASE_URL', BASE_PATH);
// Rutas físicas
define('ROOT_DIR', dirname(__DIR__));
define('VIEWS_DIR', ROOT_DIR . '/views');
define('UPLOADS_DIR', ROOT_DIR . '/public/uploads');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

// Paginación
define('ITEMS_PER_PAGE', 9);

require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/core/Helpers.php';
require_once ROOT_DIR . '/core/Auth.php';
require_once ROOT_DIR . '/core/Controller.php';
