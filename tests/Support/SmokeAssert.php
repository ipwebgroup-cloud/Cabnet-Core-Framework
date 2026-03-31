<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

final class SmokeAssert
{
    public static function true(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new RuntimeException($message);
        }
    }

    public static function false(bool $condition, string $message): void
    {
        self::true(!$condition, $message);
    }

    public static function same(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException(
                $message . ' Expected: ' . var_export($expected, true) . ' Actual: ' . var_export($actual, true)
            );
        }
    }

    public static function contains(string $needle, string $haystack, string $message): void
    {
        if (!str_contains($haystack, $needle)) {
            throw new RuntimeException($message . ' Missing fragment: ' . $needle);
        }
    }

    public static function arrayHasKey(string|int $key, array $array, string $message): void
    {
        if (!array_key_exists($key, $array)) {
            throw new RuntimeException($message . ' Missing key: ' . (string)$key);
        }
    }
}
