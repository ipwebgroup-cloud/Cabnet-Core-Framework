<?php
declare(strict_types=1);

require_once BASE_PATH . '/bootstrap/autoload.php';

function cabnet_load_config(): array
{
    $loader = new \Cabnet\Bootstrap\ConfigLoader();
    return $loader->load(BASE_PATH);
}

function bootstrap_app(string $context = 'public'): App
{
    $config = cabnet_load_config();
    $factory = new \Cabnet\Bootstrap\LegacyAppFactory();

    /** @var App $app */
    $app = $factory->make(BASE_PATH, $context, $config);
    return $app;
}

function bootstrap_kernel(string $context = 'public'): \Cabnet\Bootstrap\Kernel
{
    $config = cabnet_load_config();
    $factory = new \Cabnet\Bootstrap\LegacyAppFactory();
    $legacyApp = $factory->make(BASE_PATH, $context, $config);
    $routes = require BASE_PATH . '/bootstrap/routes.php';

    $kernel = new \Cabnet\Bootstrap\Kernel($config, $routes, $legacyApp);
    $kernel->boot();

    return $kernel;
}
