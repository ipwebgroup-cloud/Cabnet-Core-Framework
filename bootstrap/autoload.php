<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
    throw new RuntimeException('BASE_PATH must be defined before loading bootstrap/autoload.php');
}

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

spl_autoload_register(static function (string $class): void {
    if (
        class_exists($class, false)
        || interface_exists($class, false)
        || trait_exists($class, false)
        || (function_exists('enum_exists') && enum_exists($class, false))
    ) {
        return;
    }

    $normalized = ltrim($class, '\\');

    if (str_starts_with($normalized, 'Cabnet\\')) {
        $relativePath = str_replace('\\', '/', substr($normalized, strlen('Cabnet\\')));
        $file = BASE_PATH . '/src/' . $relativePath . '.php';

        if (is_file($file)) {
            require_once $file;
        }

        return;
    }

    static $legacyMap = null;
    if ($legacyMap === null) {
        /** @var array<string, string> $legacyMap */
        $legacyMap = require BASE_PATH . '/bootstrap/legacy_classmap.php';
    }

    $file = $legacyMap[$normalized] ?? null;
    if (is_string($file) && is_file(BASE_PATH . '/' . $file)) {
        require_once BASE_PATH . '/' . $file;
    }
});

// Eagerly register legacy CRUD aliases so instanceof/type checks remain stable
// while canonical ownership lives under src/.
class_exists('CrudEntityDefinition');
class_exists('ServiceEntityDefinition');
