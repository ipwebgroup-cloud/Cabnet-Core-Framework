<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use Cabnet\Application\Crud\CrudModuleRegistry;
use Cabnet\Application\Crud\RelationOptionsHydrator;
use Cabnet\Application\Services\AdminAuthService;
use Cabnet\Application\Services\AdminMenuService;
use Cabnet\Application\Services\ClockService;
use Cabnet\Core\ErrorHandler;
use Cabnet\Core\Logging\FileLogger;
use Cabnet\Core\Logging\LoggerInterface;
use Cabnet\Infrastructure\Auth\DbUserProvider;
use Cabnet\Routing\RouteRegistry;
use Cabnet\Security\Csrf;
use Cabnet\Session\Flash;
use Cabnet\Session\Session;
use Cabnet\Support\AdminMenu;
use Cabnet\Support\UploadManager;
use Cabnet\Support\UrlGenerator;
use Cabnet\Support\ViewState;
use Cabnet\View\Renderer;

final class ServiceRegistry
{
    /** @var array<string, string>|null */
    private ?array $typeAliasIndex = null;

    /**
     * Transitional compatibility hook.
     *
     * @return array<string, mixed>
     */
    public function register(): array
    {
        return $this->definitions();
    }

    /**
     * @return array<string, mixed>
     */
    public function definitions(): array
    {
        $registry = $this;

        return [
            '__service_registry' => $registry,
            '__service_types' => $registry->serviceTypeBindings(),

            'serviceRegistry' => static fn (\App $app): self => $registry,

            'clock' => static fn (\App $app): ClockService => $registry->makeClockService(),

            'time' => static fn (\App $app): string => $app->service('clock')->now(),

            'session' => static fn (\App $app): Session => new \Session(),

            'flash' => static fn (\App $app): Flash => new \Flash($app->session()),

            'auth' => static fn (\App $app): \AuthManager => new \AuthManager($app->session(), $app->config('auth', [])),

            'csrf' => static fn (\App $app): Csrf => new \Csrf($app->session()),

            'validator' => static fn (\App $app): \Validator => new \Validator(),

            'uploadManager' => static fn (\App $app): UploadManager => new UploadManager((array) $app->config('storage', [])),

            'viewState' => static fn (\App $app): ViewState => new ViewState($app->session()),

            'relationOptionsHydrator' => static fn (\App $app): RelationOptionsHydrator => new RelationOptionsHydrator($app->service('db')),

            'crudModuleRegistry' => static fn (\App $app): CrudModuleRegistry => new CrudModuleRegistry(
                (array) $app->config('modules', []),
                $app->service('db')
            ),

            'routeRegistry' => static fn (\App $app): RouteRegistry => new RouteRegistry($app->namedRoutes()),

            'url' => static fn (\App $app): UrlGenerator => new UrlGenerator($app, $app->service('routeRegistry')),

            'renderer' => static function (\App $app): Renderer {
                $engine = (string) $app->config('app.renderer', 'php');
                $factory = new \Cabnet\View\ViewEngineFactory(BASE_PATH, $engine);

                return $factory->make();
            },

            'db' => static fn (\App $app): \DatabaseManager => new \DatabaseManager(new \Connection($app->config('database'))),

            'logger' => static function (\App $app): LoggerInterface {
                $channel = $app->config('logging.channels.file', []);

                return new FileLogger(
                    $channel['path'] ?? (BASE_PATH . '/storage/logs/app.log'),
                    $channel['level'] ?? 'error'
                );
            },

            'errorHandler' => static fn (\App $app): ErrorHandler => new ErrorHandler(
                $app->service('logger'),
                (bool) $app->config('app.debug', false)
            ),

            'adminMenuService' => static fn (\App $app): AdminMenuService => $registry->makeAdminMenuService((array) $app->config('admin_menu', [])),

            'adminMenu' => static function (\App $app): AdminMenu {
                $items = $app->service('adminMenuService')->items();

                return new AdminMenu($items, static function (array $item, mixed $user) use ($app): ?bool {
                    $moduleKey = $item['module_key'] ?? null;
                    $action = $item['permission_action'] ?? 'view';

                    if (!is_string($moduleKey) || $moduleKey === '') {
                        return null;
                    }

                    $registry = $app->service('crudModuleRegistry');
                    if (!$registry instanceof CrudModuleRegistry || !$registry->has($moduleKey)) {
                        return null;
                    }

                    $permissionAction = is_string($action) && $action !== '' ? $action : 'view';

                    return $registry->allowsForUser($moduleKey, $permissionAction, $user, [
                        'surface' => 'admin_menu',
                        'menu_item' => $item,
                    ]);
                });
            },

            'userProvider' => static fn (\App $app): DbUserProvider => new DbUserProvider($app->service('db')),

            'adminAuthService' => static fn (\App $app): AdminAuthService => new AdminAuthService(
                $app->service('userProvider'),
                $app->auth()
            ),

            'middleware' => static fn (\App $app): array => [
                new \StartSessionMiddleware(),
                new \AdminAuthMiddleware(),
            ],
        ];
    }

    /** @return array<string, array<int, string>> */
    public function serviceTypeBindings(): array
    {
        return [
            'serviceRegistry' => [self::class],
            'clock' => [ClockService::class],
            'session' => [Session::class, 'Session'],
            'flash' => [Flash::class, 'Flash'],
            'auth' => ['AuthManager'],
            'csrf' => [Csrf::class, 'Csrf'],
            'validator' => ['Validator'],
            'uploadManager' => [UploadManager::class],
            'viewState' => [ViewState::class, 'ViewState'],
            'crudModuleRegistry' => [CrudModuleRegistry::class],
            'relationOptionsHydrator' => [RelationOptionsHydrator::class],
            'routeRegistry' => [RouteRegistry::class, 'RouteRegistry'],
            'url' => [UrlGenerator::class, 'UrlService'],
            'renderer' => [Renderer::class, 'RendererInterface'],
            'db' => ['DatabaseManager'],
            'logger' => [LoggerInterface::class],
            'errorHandler' => [ErrorHandler::class],
            'adminMenuService' => [AdminMenuService::class],
            'adminMenu' => [AdminMenu::class],
            'userProvider' => [DbUserProvider::class],
            'adminAuthService' => [AdminAuthService::class],
        ];
    }

    public function serviceNameForType(string $type): ?string
    {
        $normalized = $this->normalizeType($type);
        if ($normalized === '') {
            return null;
        }

        return $this->typeAliasIndex()[$normalized] ?? null;
    }

    public function makeClockService(): ClockService
    {
        return new ClockService();
    }

    public function makeAdminMenuService(array $items): AdminMenuService
    {
        return new AdminMenuService($items);
    }

    /** @return array<string, string> */
    private function typeAliasIndex(): array
    {
        if ($this->typeAliasIndex !== null) {
            return $this->typeAliasIndex;
        }

        $index = [];
        foreach ($this->serviceTypeBindings() as $serviceName => $aliases) {
            foreach ($aliases as $alias) {
                if (!is_string($alias) || $alias === '') {
                    continue;
                }

                $index[$this->normalizeType($alias)] = (string) $serviceName;
            }
        }

        return $this->typeAliasIndex = $index;
    }

    private function normalizeType(string $type): string
    {
        return ltrim(trim($type), '\\');
    }
}
