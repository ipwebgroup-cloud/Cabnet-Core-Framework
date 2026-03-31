<?php

declare(strict_types=1);

namespace Tests\Support;

final class TestEnvironment
{
    public static function reset(): void
    {
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_REQUEST = [];
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'HTTP_HOST' => 'localhost',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '80',
            'HTTPS' => 'off',
        ];

        if (function_exists('header_remove')) {
            @header_remove();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        } else {
            $_SESSION = [];
        }
    }

    public static function seedRequest(string $method, string $uri, array $post = [], array $get = [], array $files = []): void
    {
        self::reset();

        $path = (string)(parse_url($uri, PHP_URL_PATH) ?: '/');
        $query = [];
        parse_str((string)(parse_url($uri, PHP_URL_QUERY) ?? ''), $query);

        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;
        $_GET = array_merge($query, $get);
        $_POST = $post;
        $_FILES = $files;
        $_REQUEST = array_merge($_GET, $_POST);

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }

        $_SERVER['PATH_INFO'] = $path;
    }

    /** @return array<string, mixed> */
    public static function fakeUpload(string $filename, string $contents, string $mimeType = 'application/octet-stream'): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'cabnet_upload_');
        if ($tmp === false) {
            throw new \RuntimeException('Failed to create temporary upload file.');
        }

        file_put_contents($tmp, $contents);

        return [
            'name' => $filename,
            'type' => $mimeType,
            'tmp_name' => $tmp,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($tmp) ?: strlen($contents),
        ];
    }
}
