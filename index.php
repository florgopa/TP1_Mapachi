<?php
/**
 * Front controller. Todas las requests pasan por acá (ver .htaccess).
 * Acá se define la tabla de rutas -> controlador@acción.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Router.php';

// Autoload simple de modelos (evita tener que requerir cada uno a mano)
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_DIR . '/models/' . $class . '.php',
        ROOT_DIR . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();

// --- Páginas públicas ---
$router->get('/', 'HomeController@index');
$router->get('/nosotros', 'PageController@nosotros');
$router->get('/contacto', 'PageController@showContacto');
$router->post('/contacto', 'PageController@enviarContacto');

// --- Autenticación ---
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// --- Propiedades (CRUD principal) ---
$router->get('/propiedades', 'PropertyController@index');
$router->get('/propiedades/nueva', 'PropertyController@showCreate');
$router->post('/propiedades', 'PropertyController@store');
$router->get('/propiedades/{id}', 'PropertyController@show');
$router->get('/propiedades/{id}/editar', 'PropertyController@showEdit');
$router->post('/propiedades/{id}', 'PropertyController@update');
$router->post('/propiedades/{id}/eliminar', 'PropertyController@destroy');

// --- Usuarios (solo administrador) ---
$router->get('/usuarios', 'UserController@index');
$router->get('/usuarios/nuevo', 'UserController@showCreate');
$router->post('/usuarios', 'UserController@store');
$router->get('/usuarios/{id}/editar', 'UserController@showEdit');
$router->post('/usuarios/{id}', 'UserController@update');
$router->post('/usuarios/{id}/eliminar', 'UserController@destroy');

// --- Mensajes de contacto (solo administrador) ---
$router->get('/mensajes', 'PageController@mensajes');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
