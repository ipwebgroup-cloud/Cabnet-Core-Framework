<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use Cabnet\Application\Crud\CrudModuleRegistry;
use Cabnet\Application\Crud\RelationOptionsHydrator;
use Cabnet\Application\Services\AdminAuthService;
use Cabnet\Application\Services\AdminMenuService;
use Cabnet\Application\Services\ClockService;
use Cabnet\Core\ErrorHandler;
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
    public function register(object $app): void
    {
        // Transitional registry hook. Runtime construction now consults typed service bindings
        // so controllers and middleware can constructor-inject registered services safely.
    }

    /** @return array<string, array<int, string>> */
    public function serviceTypeBindings(): array
    {
        return [
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
            'adminMenu' => [AdminMenu::class],
            'userProvider' => [DbUserProvider::class],
            'adminAuthService' => [AdminAuthService::class],
        ];
    }

    public function makeClockService(): ClockService
    {
        return new ClockService();
    }

    public function makeAdminMenuService(array $items): AdminMenuService
    {
        return new AdminMenuService($items);
    }
}
