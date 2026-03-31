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
        $input = $this->mergedInput();

        if ($key === null) {
            return $input;
        }

        return $input[$key] ?? $default;
    }

    public function files(?string $key = null, mixed $default = null): mixed
    {
        $files = $this->normalizedFiles();

        if ($key === null) {
            return $files;
        }

        return $files[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $this->mergedInput());
    }

    public function server(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_SERVER;
        }

        return $_SERVER[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    private function mergedInput(): array
    {
        return array_replace_recursive($_POST, $this->normalizedFiles());
    }

    /** @return array<string, mixed> */
    private function normalizedFiles(): array
    {
        return $this->normalizeFilesArray($_FILES);
    }

    /**
     * @param array<string, mixed> $files
     * @return array<string, mixed>
     */
    private function normalizeFilesArray(array $files): array
    {
        $normalized = [];

        foreach ($files as $field => $spec) {
            if (!is_array($spec)) {
                continue;
            }

            $normalized[$field] = $this->normalizeFileSpec($spec);
        }

        return $normalized;
    }

    /** @param array<string, mixed> $spec */
    private function normalizeFileSpec(array $spec): mixed
    {
        if (isset($spec['name'], $spec['type'], $spec['tmp_name'], $spec['error'], $spec['size']) && !is_array($spec['name'])) {
            return [
                'name' => $spec['name'],
                'type' => $spec['type'],
                'tmp_name' => $spec['tmp_name'],
                'error' => $spec['error'],
                'size' => $spec['size'],
            ];
        }

        $normalized = [];
        $keys = ['name', 'type', 'tmp_name', 'error', 'size'];

        foreach ($spec['name'] ?? [] as $index => $_unused) {
            $child = [];
            foreach ($keys as $key) {
                $child[$key] = $spec[$key][$index] ?? null;
            }

            $normalized[$index] = is_array($child['name'] ?? null)
                ? $this->normalizeFileSpec($child)
                : [
                    'name' => $child['name'],
                    'type' => $child['type'],
                    'tmp_name' => $child['tmp_name'],
                    'error' => $child['error'],
                    'size' => $child['size'],
                ];
        }

        return $normalized;
    }
}
