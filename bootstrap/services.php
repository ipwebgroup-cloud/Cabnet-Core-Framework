<?php
declare(strict_types=1);

return [
    'time' => function (App $app): string {
        $registry = new \Cabnet\Bootstrap\ServiceRegistry();
        return $registry->makeClockService()->now();
    },

    'session' => function (App $app): \Cabnet\Session\Session {
        return new Session();
    },

    'flash' => function (App $app): \Cabnet\Session\Flash {
        return new Flash($app->session());
    },

    'auth' => function (App $app): AuthManager {
        return new AuthManager($app->session(), $app->config('auth', []));
    },

    'csrf' => function (App $app): \Cabnet\Security\Csrf {
        return new Csrf($app->session());
    },

    'validator' => function (App $app): Validator {
        return new Validator();
    },

    'viewState' => function (App $app): \Cabnet\Support\ViewState {
        return new \Cabnet\Support\ViewState($app->session());
    },

    'routeRegistry' => function (App $app): \Cabnet\Routing\RouteRegistry {
        return new \Cabnet\Routing\RouteRegistry($app->namedRoutes());
    },

    'url' => function (App $app): \Cabnet\Support\UrlGenerator {
        return new \Cabnet\Support\UrlGenerator($app, $app->service('routeRegistry'));
    },

    'renderer' => function (App $app): \Cabnet\View\Renderer {
        $engine = (string)$app->config('app.renderer', 'php');
        $factory = new \Cabnet\View\ViewEngineFactory(BASE_PATH, $engine);
        return $factory->make();
    },

    'db' => function (App $app): DatabaseManager {
        $connection = new Connection($app->config('database'));
        return new DatabaseManager($connection);
    },

    'serviceRepository' => function (App $app): \Cabnet\Infrastructure\Repositories\ServiceRepository {
        return new \Cabnet\Infrastructure\Repositories\ServiceRepository($app->service('db'));
    },

    'serviceCrud' => function (App $app): \Cabnet\Application\Services\ServiceCrudService {
        return new \Cabnet\Application\Services\ServiceCrudService(
            $app->service('serviceRepository'),
            $app->validator()
        );
    },

    'logger' => function (App $app): LoggerInterface {
        $channel = $app->config('logging.channels.file', []);
        return new FileLogger(
            $channel['path'] ?? (BASE_PATH . '/storage/logs/app.log'),
            $channel['level'] ?? 'error'
        );
    },

    'errorHandler' => function (App $app): ErrorHandler {
        return new ErrorHandler(
            $app->service('logger'),
            (bool)$app->config('app.debug', false)
        );
    },

    'adminMenu' => function (App $app): \Cabnet\Support\AdminMenu {
        $registry = new \Cabnet\Bootstrap\ServiceRegistry();
        $items = $registry->makeAdminMenuService($app->config('admin_menu', []))->items();
        return new \Cabnet\Support\AdminMenu($items);
    },

    'userProvider' => function (App $app): \Cabnet\Infrastructure\Auth\DbUserProvider {
        return new \Cabnet\Infrastructure\Auth\DbUserProvider($app->service('db'));
    },

    'adminAuthService' => function (App $app): \Cabnet\Application\Services\AdminAuthService {
        return new \Cabnet\Application\Services\AdminAuthService(
            $app->service('userProvider'),
            $app->auth()
        );
    },

    'middleware' => function (App $app): array {
        return [
            new StartSessionMiddleware(),
            new AdminAuthMiddleware(),
        ];
    },
];
