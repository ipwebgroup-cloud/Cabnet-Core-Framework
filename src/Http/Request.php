<?php
declare(strict_types=1);

namespace Cabnet\Http;

class Request
{
    public function method(): string
    {
        return strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function uri(): string
    {
        return (string)($_SERVER['REQUEST_URI'] ?? '/');
    }

    public function path(): string
    {
        $path = parse_url($this->uri(), PHP_URL_PATH);
        return is_string($path) && $path !== '' ? $path : '/';
    }

    public function fullUrl(): string
    {
        $scheme = $this->server('HTTPS') === 'on' ? 'https' : 'http';
        $host = (string)($this->server('HTTP_HOST') ?: $this->server('SERVER_NAME') ?: 'localhost');
        return $scheme . '://' . $host . $this->uri();
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public function server(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_SERVER;
        }

        return $_SERVER[$key] ?? $default;
    }
}
