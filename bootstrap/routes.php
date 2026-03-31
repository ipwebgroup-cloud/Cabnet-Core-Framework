<?php
declare(strict_types=1);

return [
    'public' => [
        ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\PublicSite\HomeController::class, 'index'], 'name' => 'public.home', 'middleware' => ['session']],
        ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'public.health', 'middleware' => []],
    ],

    'admin' => [
        ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\Admin\DashboardController::class, 'index'], 'name' => 'admin.dashboard', 'middleware' => ['session','admin.auth']],
        ['method' => 'GET', 'path' => '/login', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'loginForm'], 'name' => 'admin.login', 'middleware' => ['session']],
        ['method' => 'POST', 'path' => '/login', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'login'], 'name' => 'admin.login.submit', 'middleware' => ['session']],
        ['method' => 'POST', 'path' => '/logout', 'handler' => [\Cabnet\Application\Controllers\Admin\AuthController::class, 'logout'], 'name' => 'admin.logout', 'middleware' => ['session','admin.auth']],
        ['method' => 'GET', 'path' => '/services', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'index'], 'name' => 'admin.services.index', 'middleware' => ['session','admin.auth']],
        ['method' => 'GET', 'path' => '/services/create', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'createForm'], 'name' => 'admin.services.create', 'middleware' => ['session','admin.auth']],
        ['method' => 'POST', 'path' => '/services', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'store'], 'name' => 'admin.services.store', 'middleware' => ['session','admin.auth']],
        ['method' => 'GET', 'path' => '/services/{id}/edit', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'editForm'], 'name' => 'admin.services.edit', 'middleware' => ['session','admin.auth']],
        ['method' => 'POST', 'path' => '/services/{id}/update', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'update'], 'name' => 'admin.services.update', 'middleware' => ['session','admin.auth']],
        ['method' => 'POST', 'path' => '/services/{id}/delete', 'handler' => [\Cabnet\Application\Controllers\Admin\ServiceController::class, 'destroy'], 'name' => 'admin.services.delete', 'middleware' => ['session','admin.auth']],
        ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'admin.health', 'middleware' => []],
    ],

    'api' => [
        ['method' => 'GET', 'path' => '/', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'api.index', 'middleware' => []],
        ['method' => 'GET', 'path' => '/health', 'handler' => [\Cabnet\Application\Controllers\Api\HealthController::class, 'index'], 'name' => 'api.health', 'middleware' => []],
    ],
];
