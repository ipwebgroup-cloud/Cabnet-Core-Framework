<?php
declare(strict_types=1);

$services = [
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

    'uploadManager' => function (App $app): \Cabnet\Support\UploadManager {
        return new \Cabnet\Support\UploadManager((array)$app->config('storage', []));
    },

    'viewState' => function (App $app): \Cabnet\Support\ViewState {
        return new \Cabnet\Support\ViewState($app->session());
    },

    'crudModuleRegistry' => function (App $app): \Cabnet\Application\Crud\CrudModuleRegistry {
        return new \Cabnet\Application\Crud\CrudModuleRegistry((array)$app->config('modules', []));
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
        $serviceRegistry = new \Cabnet\Bootstrap\ServiceRegistry();
        $items = $serviceRegistry->makeAdminMenuService($app->config('admin_menu', []))->items();

        return new \Cabnet\Support\AdminMenu($items, static function (array $item, mixed $user) use ($app): ?bool {
            $moduleKey = $item['module_key'] ?? null;
            $action = $item['permission_action'] ?? 'view';

            if (!is_string($moduleKey) || $moduleKey === '') {
                return null;
            }

            $registry = $app->service('crudModuleRegistry');
            if (!$registry instanceof \Cabnet\Application\Crud\CrudModuleRegistry || !$registry->has($moduleKey)) {
                return null;
            }

            $permissionAction = is_string($action) && $action !== '' ? $action : 'view';
            return $registry->allowsForUser($moduleKey, $permissionAction, $user, [
                'surface' => 'admin_menu',
                'menu_item' => $item,
            ]);
        });
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

return \Cabnet\Application\Crud\CrudModuleBootstrap::registerServices(
    $services,
    require BASE_PATH . '/config/modules.php'
);
