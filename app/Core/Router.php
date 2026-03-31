<?php
declare(strict_types=1);

final class Router
{
    private array $routes = [];
    private array $params = [];
    private array $namedRoutes = [];

    public function __construct(array $routes = [])
    {
        $this->routes = [];

        foreach ($routes as $route) {
            $normalized = $this->normalizeRoute($route);
            $this->routes[] = $normalized;

            if (!empty($normalized['name'])) {
                $this->namedRoutes[$normalized['name']] = $normalized['path'];
            }
        }
    }

    public function match(string $method, string $path): ?object
    {
        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) !== strtoupper($method)) {
                continue;
            }

            if (!$this->matches($route['path'], $path)) {
                continue;
            }

            return (object)[
                'route' => $route,
                'params' => $this->params,
            ];
        }

        return null;
    }

    public function dispatch(string $method, string $path, App $app): mixed
    {
        $resolved = $this->match($method, $path);

        if ($resolved === null) {
            return $app->response()->html(
                '<h1>404 Not Found</h1><p>No route matched: ' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '</p>',
                404
            );
        }

        $handler = $resolved->route['handler'];

        if (is_callable($handler)) {
            return $handler($app, $resolved->params);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;

            if (class_exists($class) && method_exists($class, $action)) {
                $controller = new $class();
                return $controller->{$action}($app, $resolved->params);
            }
        }

        return null;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function namedRoutes(): array
    {
        return $this->namedRoutes;
    }

    private function normalizeRoute(array $route): array
    {
        if (isset($route['method'], $route['path'], $route['handler'])) {
            return [
                'method' => $route['method'],
                'path' => $route['path'],
                'handler' => $route['handler'],
                'name' => $route['name'] ?? null,
                'middleware' => $route['middleware'] ?? [],
            ];
        }

        return [
            'method' => $route[0] ?? 'GET',
            'path' => $route[1] ?? '/',
            'handler' => $route[2] ?? null,
            'name' => $route[3] ?? null,
            'middleware' => $route[4] ?? [],
        ];
    }

    private function matches(string $routePath, string $actualPath): bool
    {
        $this->params = [];

        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $routePath);

        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $actualPath, $matches)) {
            return false;
        }

        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $this->params[$key] = $value;
            }
        }

        return true;
    }
}
