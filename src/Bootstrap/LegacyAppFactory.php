<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

final class LegacyAppFactory
{
    public function make(string $basePath, string $context, array $config): object
    {
        $services = require $basePath . '/bootstrap/services.php';
        $routes = require $basePath . '/bootstrap/routes.php';
        return new \App($config, $services, $routes, $context);
    }
}
