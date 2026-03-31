<?php
declare(strict_types=1);

$adminRoutes = [
    ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\Admin\DashboardController::class, 'index'], 'name' => 'admin.dashboard', 'middleware' => ['session','admin.auth']],
    ['method' => 'GET', 'path' => '/login', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'loginForm'], 'name' => 'admin.login', 'middleware' => ['session']],
    ['method' => 'POST', 'path' => '/login', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'login'], 'name' => 'admin.login.submit', 'middleware' => ['session']],
    ['method' => 'POST', 'path' => '/logout', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'logout'], 'name' => 'admin.logout', 'middleware' => ['session','admin.auth']],
    ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'admin.health', 'middleware' => []],
];

$adminRoutes = \Cabnet\Application\Crud\CrudModuleBootstrap::appendAdminRoutes(
    $adminRoutes,
    require BASE_PATH . '/config/modules.php'
);

return [
    'public' => [
        ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\PublicSite\HomeController::class, 'index'], 'name' => 'public.home', 'middleware' => ['session']],
        ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'public.health', 'middleware' => []],
    ],

    'admin' => $adminRoutes,

    'api' => [
        ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'api.index', 'middleware' => []],
        ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'api.health', 'middleware' => []],
    ],
];
