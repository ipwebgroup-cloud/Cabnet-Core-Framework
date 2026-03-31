<?php
declare(strict_types=1);

namespace Cabnet\Support;

use Cabnet\Routing\RouteRegistry;
use RuntimeException;

class UrlGenerator
{
    public function __construct(
        private object $app,
        private ?RouteRegistry $routes = null
    ) {
    }

    public function to(string $path = '/', array $query = []): string
    {
        $base = rtrim((string)$this->app->config('app.base_url', ''), '/');
        $path = '/' . ltrim($path, '/');
        $url = $base !== '' ? $base . $path : $path;

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public function route(string $name, array $params = [], array $query = []): string
    {
        $pattern = $this->routes?->get($name);

        if (!is_string($pattern) || $pattern === '') {
            throw new RuntimeException('Route not found: ' . $name);
        }

        $path = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', static function (array $matches) use ($params): string {
            $key = $matches[1];
            return rawurlencode((string)($params[$key] ?? $matches[0]));
        }, $pattern);

        return $this->to($path ?? '/', $query);
    }

    public function currentPath(): string
    {
        return $this->app->request()->path();
    }

    public function is(string $path): bool
    {
        return $this->currentPath() === '/' . ltrim($path, '/');
    }
}
