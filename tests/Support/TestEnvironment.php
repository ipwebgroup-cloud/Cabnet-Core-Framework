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

    public static function seedRequest(string $method, string $uri, array $post = [], array $get = []): void
    {
        self::reset();

        $path = (string)(parse_url($uri, PHP_URL_PATH) ?: '/');
        $query = [];
        parse_str((string)(parse_url($uri, PHP_URL_QUERY) ?? ''), $query);

        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;
        $_GET = array_merge($query, $get);
        $_POST = $post;
        $_REQUEST = array_merge($_GET, $_POST);

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }

        $_SERVER['PATH_INFO'] = $path;
    }
}
