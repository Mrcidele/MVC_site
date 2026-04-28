<?php
declare(strict_types=1);
namespace App\Core;

// Engine de Roteamento: Mapeia URIs para Controllers.
final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void { $this->add('GET', $pattern, $handler); }
    public function post(string $pattern, callable|array $handler): void { $this->add('POST', $pattern, $handler); }
    public function put(string $pattern, callable|array $handler): void { $this->add('PUT', $pattern, $handler); }
    public function delete(string $pattern, callable|array $handler): void { $this->add('DELETE', $pattern, $handler); }

    private function add(string $method, string $pattern, callable|array $handler): void
    {
        $pattern = '/' . ltrim($pattern, '/');
        $this->routes[strtoupper($method)][] = [
            'regex' => $this->patternToRegex($pattern),
            'handler' => $handler,
        ];
    }

    // Resolve a requisição e invoca o handler correspondente.
    public function dispatch(string $method, string $uri): void
    {
        $method = $this->resolveMethod($method);
        $path = '/' . ltrim((string) parse_url($uri, PHP_URL_PATH), '/');
        $routes = $this->routes[$method] ?? [];

        if (empty($routes)) {
            $this->abortNotFound();
            return;
        }

        foreach ($routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $this->invokeHandler($route['handler'], $matches);
            return;
        }

        $this->abortNotFound();
    }

    private function resolveMethod(string $method): string
    {
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'DELETE', 'PATCH'], true)) {
                return $spoofed;
            }
        }
        return strtoupper($method);
    }

    private function invokeHandler(callable|array $handler, array $matches): void
    {
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

        if (isset($params['id']) && ctype_digit($params['id'])) {
            $params['id'] = (int) $params['id'];
        }

        if (is_array($handler)) {
            $controller = new $handler[0]();
            $controller->{$handler[1]}(...array_values($params));
            return;
        }

        $handler(...array_values($params));
    }

    private function abortNotFound(): void
    {
        http_response_code(404);
        echo 'Página não encontrada.';
        exit; // Garante que a execução morre aqui
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace_callback('#\{([a-zA-Z0-9_]+)\}#', function ($m) {
            return $m[1] === 'id' ? '(?P<id>\d+)' : '(?P<' . $m[1] . '>[^/]+)';
        }, $pattern);
        return '#^' . $regex . '/?$#';
    }
}