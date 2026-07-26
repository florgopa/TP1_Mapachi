<?php
/**
 * Router muy simple: mapea "método + ruta" a "Controlador->accion".
 * Soporta un parámetro dinámico por ruta, ej: /propiedades/editar/{id}
 */

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, string $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(string $method, string $uri): void
    {
        // Limpiamos query string y barras finales
        $uri = parse_url($uri, PHP_URL_PATH);

        // Si el proyecto vive en una subcarpeta de htdocs (BASE_PATH no vacío),
        // recortamos ese prefijo antes de matchear contra las rutas registradas.
        if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
            $uri = substr($uri, strlen(BASE_PATH));
        }

        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // saca el match completo, dejamos solo los parámetros
                [$controllerName, $action] = explode('@', $route['handler']);

                $controllerFile = ROOT_DIR . '/controllers/' . $controllerName . '.php';
                if (!file_exists($controllerFile)) {
                    $this->notFound();
                }
                require_once $controllerFile;

                $controller = new $controllerName();
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        $this->notFound();
    }

    private function notFound(): void
    {
        http_response_code(404);
        require VIEWS_DIR . '/errors/404.php';
    }
}
