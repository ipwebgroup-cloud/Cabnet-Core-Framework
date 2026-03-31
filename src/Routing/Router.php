<?php
declare(strict_types=1);

namespace Cabnet\Routing;

class Router
{
    /** @var RouteDefinition[] */
    private array $routes = [];
    private array $namedRoutes = [];
    private array $lastParams = [];

    public function __construct(array $routes = [])
    {
        foreach ($routes as $route) {
            $definition = $route instanceof RouteDefinition ? $route : RouteDefinition::fromArray($route);
            $this->routes[] = $definition;

            if ($definition->name !== null) {
                $this->namedRoutes[$definition->name] = $definition->path;
            }
        }
    }

    public function match(string $method, string $path): ?ResolvedRoute
    {
        $this->lastParams = [];

        foreach ($this->routes as $route) {
            if (strtoupper($route->method) !== strtoupper($method)) {
                continue;
            }

            $params = $this->matchPath($route->path, $path);
            if ($params === null) {
                continue;
            }

            $this->lastParams = $params;
            return new ResolvedRoute($route, $params);
        }

        return null;
    }

    public function namedRoutes(): array
    {
        return $this->namedRoutes;
    }

    public function params(): array
    {
        return $this->lastParams;
    }

    private function matchPath(string $routePath, string $actualPath): ?array
    {
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $matches): string {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $routePath);

        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $actualPath, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
