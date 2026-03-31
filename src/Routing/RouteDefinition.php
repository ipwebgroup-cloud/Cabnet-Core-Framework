<?php
declare(strict_types=1);

namespace Cabnet\Routing;

final class RouteDefinition
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly mixed $handler,
        public readonly ?string $name = null,
        public readonly array $middleware = []
    ) {
    }

    public static function fromArray(array $route): self
    {
        if (isset($route['method'], $route['path'], $route['handler'])) {
            return new self(
                method: (string)$route['method'],
                path: (string)$route['path'],
                handler: $route['handler'],
                name: isset($route['name']) ? (string)$route['name'] : null,
                middleware: isset($route['middleware']) && is_array($route['middleware']) ? $route['middleware'] : []
            );
        }

        return new self(
            method: (string)($route[0] ?? 'GET'),
            path: (string)($route[1] ?? '/'),
            handler: $route[2] ?? null,
            name: isset($route[3]) ? (string)$route[3] : null,
            middleware: isset($route[4]) && is_array($route[4]) ? $route[4] : []
        );
    }
}
