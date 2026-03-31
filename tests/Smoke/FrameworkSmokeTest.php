<?php

declare(strict_types=1);

namespace Tests\Smoke;

use AdminAuthMiddleware;
use Cabnet\Application\Controllers\Admin\AuthController;
use Cabnet\AppRuntime;
use Cabnet\Bootstrap\Kernel;
use Cabnet\Generators\CrudScaffoldWriter;
use Cabnet\Routing\Router;
use Tests\Support\ResponseInspector;
use Tests\Support\SmokeAssert;
use Tests\Support\TestEnvironment;
use Throwable;

final class FrameworkSmokeTest
{
    /** @return array<int, array{name:string, passed:bool, detail:string}> */
    public function run(): array
    {
        $results = [];

        foreach ($this->tests() as $name => $method) {
            TestEnvironment::reset();

            try {
                set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
                    throw new \ErrorException($message, 0, $severity, $file, $line);
                });

                $this->{$method}();

                $results[] = [
                    'name' => $name,
                    'passed' => true,
                    'detail' => 'ok',
                ];
            } catch (Throwable $e) {
                $results[] = [
                    'name' => $name,
                    'passed' => false,
                    'detail' => $e->getMessage(),
                ];
            } finally {
                restore_error_handler();
            }
        }

        return $results;
    }

    /** @return array<string, string> */
    private function tests(): array
    {
        return [
            'bootstrap_app_builds_admin_context' => 'bootstrapAppBuildsAdminContext',
            'bootstrap_kernel_registers_named_routes' => 'bootstrapKernelRegistersNamedRoutes',
            'router_matches_admin_edit_route_params' => 'routerMatchesAdminEditRouteParams',
            'login_form_renders_csrf_token' => 'loginFormRendersCsrfToken',
            'login_rejects_invalid_csrf_token' => 'loginRejectsInvalidCsrfToken',
            'starter_credentials_are_disabled_by_default' => 'starterCredentialsAreDisabledByDefault',
            'logout_requires_valid_post_and_clears_auth' => 'logoutRequiresValidPostAndClearsAuth',
            'admin_auth_middleware_redirects_guests' => 'adminAuthMiddlewareRedirectsGuests',
            'renderer_service_uses_src_contract' => 'rendererServiceUsesSrcContract',
            'legacy_php_renderer_wrapper_remains_compatible' => 'legacyPhpRendererWrapperRemainsCompatible',
            'app_runtime_named_routes_match_kernel_context' => 'appRuntimeNamedRoutesMatchKernelContext',
            'legacy_runtime_shims_extend_src_runtime_classes' => 'legacyRuntimeShimsExtendSrcRuntimeClasses',
            'app_container_resolves_src_runtime_services' => 'appContainerResolvesSrcRuntimeServices',
            'src_url_generator_builds_named_admin_edit_path' => 'srcUrlGeneratorBuildsNamedAdminEditPath',
            'invalid_service_store_csrf_redirects_to_create_route' => 'invalidServiceStoreCsrfRedirectsToCreateRoute',
            'invalid_service_update_csrf_redirects_to_edit_route' => 'invalidServiceUpdateCsrfRedirectsToEditRoute',
            'src_service_controller_uses_src_crud_base' => 'srcServiceControllerUsesSrcCrudBase',
            'legacy_service_repository_layer_remains_shimmed_to_src' => 'legacyServiceRepositoryLayerRemainsShimmedToSrc',
            'src_crud_definition_model_is_canonical' => 'srcCrudDefinitionModelIsCanonical',
            'crud_module_registry_resolves_services_definition' => 'crudModuleRegistryResolvesServicesDefinition',
            'crud_module_bootstrap_registers_dynamic_services' => 'crudModuleBootstrapRegistersDynamicServices',
            'crud_module_bootstrap_appends_admin_routes' => 'crudModuleBootstrapAppendsAdminRoutes',
            'crud_module_bootstrap_appends_admin_menu_items' => 'crudModuleBootstrapAppendsAdminMenuItems',
            'src_crud_generator_uses_namespaced_base_classes' => 'srcCrudGeneratorUsesNamespacedBaseClasses',
        ];
    }

    private function bootstrapAppBuildsAdminContext(): void
    {
        $app = bootstrap_app('admin');

        SmokeAssert::true($app instanceof \App, 'bootstrap_app should return App.');
        SmokeAssert::same('admin', $app->context(), 'Admin context should be preserved.');
        SmokeAssert::same('/login', (string)$app->config('auth.login_route'), 'Login route should remain stable.');
    }

    private function bootstrapKernelRegistersNamedRoutes(): void
    {
        $kernel = bootstrap_kernel('admin');

        SmokeAssert::true($kernel instanceof Kernel, 'bootstrap_kernel should return Kernel.');

        $namedRoutes = $kernel->namedRoutes();
        SmokeAssert::same('/login', $namedRoutes['admin.login'] ?? null, 'Admin login named route should resolve.');
        SmokeAssert::same('/logout', $namedRoutes['admin.logout'] ?? null, 'Admin logout named route should resolve.');
        SmokeAssert::same('/services', $namedRoutes['admin.services.index'] ?? null, 'Admin services index route should resolve.');
    }

    private function routerMatchesAdminEditRouteParams(): void
    {
        $routes = require BASE_PATH . '/bootstrap/routes.php';
        $router = new Router($routes['admin'] ?? []);
        $resolved = $router->match('GET', '/services/42/edit');

        SmokeAssert::true($resolved !== null, 'Router should match edit route.');
        SmokeAssert::same('admin.services.edit', $resolved->route->name, 'Matched route name should be stable.');
        SmokeAssert::same('42', $resolved->params['id'] ?? null, 'Router should extract numeric id parameter.');
    }

    private function loginFormRendersCsrfToken(): void
    {
        TestEnvironment::seedRequest('GET', '/login');
        $app = bootstrap_app('admin');
        $controller = new AuthController();

        $response = $controller->loginForm($app);
        $snapshot = ResponseInspector::snapshot($response);
        $token = $app->csrf()->token();

        SmokeAssert::same(200, $snapshot['statusCode'], 'Login form should render with 200 status.');
        SmokeAssert::contains('name="_token"', (string)$snapshot['body'], 'Login form should include CSRF input.');
        SmokeAssert::contains($token, (string)$snapshot['body'], 'Login form should include the active CSRF token.');
    }

    private function loginRejectsInvalidCsrfToken(): void
    {
        TestEnvironment::seedRequest('POST', '/login', [
            '_token' => 'invalid-token',
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $app = bootstrap_app('admin');
        $controller = new AuthController();

        $response = $controller->login($app);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Invalid login CSRF should redirect.');
        SmokeAssert::same('/login', $snapshot['headers']['Location'] ?? null, 'Invalid login CSRF should redirect to login route.');
        SmokeAssert::contains('Invalid login request token', implode(' ', $flash['danger'] ?? []), 'CSRF rejection should flash a danger message.');
        SmokeAssert::false($app->auth()->check(), 'CSRF rejection must not authenticate the user.');
    }

    private function starterCredentialsAreDisabledByDefault(): void
    {
        TestEnvironment::seedRequest('POST', '/login');
        $app = bootstrap_app('admin');
        $token = $app->csrf()->token();

        $_POST = [
            '_token' => $token,
            'username' => 'admin',
            'password' => 'admin123',
        ];
        $_REQUEST = $_POST;

        $controller = new AuthController();
        $response = $controller->login($app);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Failed starter login should redirect.');
        SmokeAssert::same('/login', $snapshot['headers']['Location'] ?? null, 'Failed starter login should redirect to login route.');
        SmokeAssert::contains('Invalid login credentials', implode(' ', $flash['danger'] ?? []), 'Disabled starter credentials should fail closed.');
        SmokeAssert::false($app->auth()->check(), 'Starter credentials must not log in when disabled.');
    }

    private function logoutRequiresValidPostAndClearsAuth(): void
    {
        TestEnvironment::seedRequest('POST', '/logout');
        $app = bootstrap_app('admin');
        $app->auth()->login([
            'name' => 'Smoke Admin',
            'username' => 'smoke_admin',
            'role' => 'admin',
        ]);

        $token = $app->csrf()->token();
        $_POST = ['_token' => $token];
        $_REQUEST = $_POST;

        $controller = new AuthController();
        $response = $controller->logout($app);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Logout should redirect after success.');
        SmokeAssert::same('/login', $snapshot['headers']['Location'] ?? null, 'Logout should redirect to login route.');
        SmokeAssert::contains('signed out', strtolower(implode(' ', $flash['info'] ?? [])), 'Logout should flash a signed-out message.');
        SmokeAssert::false($app->auth()->check(), 'Logout should clear authenticated admin state.');
    }

    private function rendererServiceUsesSrcContract(): void
    {
        $app = bootstrap_app('public');
        $renderer = $app->service('renderer');

        SmokeAssert::true($renderer instanceof \Cabnet\View\Renderer, 'Renderer service should implement the src view contract.');
        SmokeAssert::same(\Cabnet\View\PhpRenderer::class, $renderer::class, 'Default renderer should be the src PHP renderer.');

        $output = $renderer->render('public/home.php', [
            'appName' => 'Smoke Render',
            'context' => 'public',
            'now' => '2026-03-31 00:00:00',
            'flashMessages' => [],
        ]);

        SmokeAssert::contains('Smoke Render', $output, 'Renderer should render the existing public home view.');
    }

    private function legacyPhpRendererWrapperRemainsCompatible(): void
    {
        $tempDir = sys_get_temp_dir() . '/cabnet_view_smoke_' . uniqid('', true);
        if (!mkdir($tempDir, 0777, true) && !is_dir($tempDir)) {
            throw new \RuntimeException('Failed to create temporary view directory.');
        }

        $template = $tempDir . '/sample.php';
        file_put_contents($template, "<h1><?= htmlspecialchars(\$title, ENT_QUOTES, 'UTF-8') ?></h1>");

        $renderer = new \PhpRenderer($tempDir);
        $output = $renderer->render('sample.php', ['title' => 'Legacy Wrapper']);

        SmokeAssert::true($renderer instanceof \RendererInterface, 'Legacy renderer wrapper should still satisfy the legacy interface.');
        SmokeAssert::true($renderer instanceof \Cabnet\View\Renderer, 'Legacy renderer wrapper should also satisfy the src renderer contract.');
        SmokeAssert::contains('Legacy Wrapper', $output, 'Legacy renderer wrapper should delegate rendering successfully.');
    }

    private function appRuntimeNamedRoutesMatchKernelContext(): void
    {
        $app = bootstrap_app('admin');
        $routes = require BASE_PATH . '/bootstrap/routes.php';
        $runtime = new AppRuntime($app, $routes, (array)$app->config('middleware.aliases', []));

        SmokeAssert::same(
            '/services',
            $runtime->namedRoutes()['admin.services.index'] ?? null,
            'AppRuntime compatibility facade should expose admin named routes through the kernel.'
        );
    }

    private function legacyRuntimeShimsExtendSrcRuntimeClasses(): void
    {
        SmokeAssert::true(is_subclass_of('Request', \Cabnet\Http\Request::class), 'Legacy Request should remain a shim over the src request class.');
        SmokeAssert::true(is_subclass_of('Response', \Cabnet\Http\Response::class), 'Legacy Response should remain a shim over the src response class.');
        SmokeAssert::true(is_subclass_of('Router', \Cabnet\Routing\Router::class), 'Legacy Router should remain a shim over the src router class.');
        SmokeAssert::true(is_subclass_of('Session', \Cabnet\Session\Session::class), 'Legacy Session should remain a shim over the src session class.');
        SmokeAssert::true(is_subclass_of('Flash', \Cabnet\Session\Flash::class), 'Legacy Flash should remain a shim over the src flash class.');
        SmokeAssert::true(is_subclass_of('Csrf', \Cabnet\Security\Csrf::class), 'Legacy Csrf should remain a shim over the src CSRF class.');
        SmokeAssert::true(is_subclass_of('RouteRegistry', \Cabnet\Routing\RouteRegistry::class), 'Legacy RouteRegistry should remain a shim over the src route registry.');
        SmokeAssert::true(is_subclass_of('UrlService', \Cabnet\Support\UrlGenerator::class), 'Legacy UrlService should remain a shim over the src URL generator.');
        SmokeAssert::true(is_subclass_of('ViewState', \Cabnet\Support\ViewState::class), 'Legacy ViewState should remain a shim over the src view-state helper.');
    }

    private function appContainerResolvesSrcRuntimeServices(): void
    {
        $app = bootstrap_app('admin');

        SmokeAssert::true($app->request() instanceof \Cabnet\Http\Request, 'App request should resolve through the src HTTP request contract.');
        SmokeAssert::true($app->response() instanceof \Cabnet\Http\Response, 'App response should resolve through the src HTTP response contract.');
        SmokeAssert::true($app->session() instanceof \Cabnet\Session\Session, 'App session should resolve through the src session contract.');
        SmokeAssert::true($app->flash() instanceof \Cabnet\Session\Flash, 'App flash should resolve through the src flash contract.');
        SmokeAssert::true($app->csrf() instanceof \Cabnet\Security\Csrf, 'App CSRF should resolve through the src CSRF contract.');
        SmokeAssert::true($app->viewState() instanceof \Cabnet\Support\ViewState, 'App view-state should resolve through the src support helper.');
        SmokeAssert::true($app->url() instanceof \Cabnet\Support\UrlGenerator, 'App URL helper should resolve through the src URL generator.');
        SmokeAssert::true($app->service('routeRegistry') instanceof \Cabnet\Routing\RouteRegistry, 'Route registry service should resolve through the src route registry.');
    }

    private function srcUrlGeneratorBuildsNamedAdminEditPath(): void
    {
        $app = bootstrap_app('admin');

        SmokeAssert::same(
            '/services/42/edit',
            $app->url()->route('admin.services.edit', ['id' => 42]),
            'Src URL generator should expand named admin edit routes with parameters.'
        );
    }

    private function invalidServiceStoreCsrfRedirectsToCreateRoute(): void
    {
        TestEnvironment::seedRequest('POST', '/services', [
            '_token' => 'invalid-token',
            'title' => 'Smoke Service',
            'slug' => 'smoke-service',
        ]);

        $app = bootstrap_app('admin');
        $app->auth()->login([
            'name' => 'Smoke Admin',
            'username' => 'smoke_admin',
            'role' => 'admin',
        ]);

        $controller = new \Cabnet\Application\Controllers\Admin\ServiceController();
        $response = $controller->store($app);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Invalid create CSRF should redirect.');
        SmokeAssert::same('/services/create', $snapshot['headers']['Location'] ?? null, 'Invalid create CSRF should redirect back to the create route.');
        SmokeAssert::contains('Invalid CSRF token', implode(' ', $flash['danger'] ?? []), 'Invalid create CSRF should flash a danger message.');
    }

    private function invalidServiceUpdateCsrfRedirectsToEditRoute(): void
    {
        TestEnvironment::seedRequest('POST', '/services/42/update', [
            '_token' => 'invalid-token',
            'title' => 'Smoke Service',
            'slug' => 'smoke-service',
        ]);

        $app = bootstrap_app('admin');
        $app->auth()->login([
            'name' => 'Smoke Admin',
            'username' => 'smoke_admin',
            'role' => 'admin',
        ]);

        $controller = new \Cabnet\Application\Controllers\Admin\ServiceController();
        $response = $controller->update($app, ['id' => 42]);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Invalid update CSRF should redirect.');
        SmokeAssert::same('/services/42/edit', $snapshot['headers']['Location'] ?? null, 'Invalid update CSRF should redirect back to the edit route.');
        SmokeAssert::contains('Invalid CSRF token', implode(' ', $flash['danger'] ?? []), 'Invalid update CSRF should flash a danger message.');
    }

    private function srcServiceControllerUsesSrcCrudBase(): void
    {
        $controller = new \Cabnet\Application\Controllers\Admin\ServiceController();

        SmokeAssert::true(
            $controller instanceof \Cabnet\Application\Controllers\Admin\BaseCrudController,
            'Canonical src admin service controller should extend the src CRUD base controller.'
        );

        SmokeAssert::true(
            is_subclass_of('BaseCrudController', \Cabnet\Application\Controllers\Admin\BaseCrudController::class),
            'Legacy global BaseCrudController should remain a shim over the src CRUD base controller.'
        );

        SmokeAssert::true(
            is_subclass_of('BaseController', \Cabnet\Application\Controllers\BaseController::class),
            'Legacy global BaseController should remain a shim over the src base controller.'
        );
    }

    private function legacyServiceRepositoryLayerRemainsShimmedToSrc(): void
    {
        $app = bootstrap_app('admin');

        SmokeAssert::true(
            is_subclass_of('BaseService', \Cabnet\Application\Services\BaseService::class),
            'Legacy BaseService should remain a shim over the src service base.'
        );

        SmokeAssert::true(
            is_subclass_of('BaseRepository', \Cabnet\Infrastructure\Repositories\BaseRepository::class),
            'Legacy BaseRepository should remain a shim over the src repository base.'
        );

        SmokeAssert::true(
            is_subclass_of('ServiceCrudService', \Cabnet\Application\Services\ServiceCrudService::class),
            'Legacy service CRUD class should remain a shim over the src CRUD service.'
        );

        SmokeAssert::true(
            is_subclass_of('ServiceRepository', \Cabnet\Infrastructure\Repositories\ServiceRepository::class),
            'Legacy service repository should remain a shim over the src repository.'
        );

        SmokeAssert::true(
            is_subclass_of('AdminAuthService', \Cabnet\Application\Services\AdminAuthService::class),
            'Legacy admin auth service should remain a shim over the src auth service.'
        );

        SmokeAssert::true(
            is_subclass_of('DbUserProvider', \Cabnet\Infrastructure\Auth\DbUserProvider::class),
            'Legacy DB user provider should remain a shim over the src infrastructure provider.'
        );

        SmokeAssert::true(
            $app->service('serviceRepository') instanceof \Cabnet\Infrastructure\Repositories\ServiceRepository,
            'Active service repository registration should resolve to the src repository implementation.'
        );

        SmokeAssert::true(
            $app->service('serviceCrud') instanceof \Cabnet\Application\Services\ServiceCrudService,
            'Active service CRUD registration should resolve to the src service implementation.'
        );
    }

    private function srcCrudDefinitionModelIsCanonical(): void
    {
        $definition = \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::make();

        SmokeAssert::true(
            $definition instanceof \Cabnet\Application\Crud\CrudEntityDefinition,
            'Canonical service entity definition should return the src CRUD definition model.'
        );

        SmokeAssert::true(
            $definition instanceof \CrudEntityDefinition,
            'Legacy global CrudEntityDefinition references should remain compatible through the alias.'
        );

        SmokeAssert::same('services', $definition->key(), 'Canonical CRUD definition should preserve the service key.');
        SmokeAssert::same('services', $definition->table(), 'Canonical CRUD definition should preserve the table name.');

        SmokeAssert::true(
            is_a('ServiceEntityDefinition', \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::class, true),
            'Legacy global ServiceEntityDefinition should remain an alias to the src definition class.'
        );
    }

    private function crudModuleRegistryResolvesServicesDefinition(): void
    {
        $app = bootstrap_app('admin');
        $registry = $app->service('crudModuleRegistry');

        SmokeAssert::true(
            $registry instanceof \Cabnet\Application\Crud\CrudModuleRegistry,
            'CRUD module registry service should resolve from the container.'
        );

        SmokeAssert::true($registry->has('services'), 'CRUD module registry should expose the built-in services module.');

        $meta = $registry->meta('services');
        $definition = $registry->definition('services');

        SmokeAssert::same(
            \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::class,
            $meta['definition_class'] ?? null,
            'Services module metadata should point to the canonical src entity definition class.'
        );

        SmokeAssert::same('serviceCrud', $meta['crud_service'] ?? null, 'Services module metadata should preserve the CRUD service key.');
        SmokeAssert::same('admin.services', $meta['admin_route_base'] ?? null, 'Services module metadata should preserve the admin route base.');
        SmokeAssert::same('Services', $definition->label(), 'Module registry should resolve the canonical services definition instance.');
    }

    private function crudModuleBootstrapRegistersDynamicServices(): void
    {
        $services = require BASE_PATH . '/bootstrap/services.php';

        SmokeAssert::true(isset($services['serviceRepository']), 'Module bootstrap should register the repository service from config/modules.php.');
        SmokeAssert::true(isset($services['serviceCrud']), 'Module bootstrap should register the CRUD service from config/modules.php.');

        $app = bootstrap_app('admin');

        SmokeAssert::true(
            $app->service('serviceRepository') instanceof \Cabnet\Infrastructure\Repositories\ServiceRepository,
            'Dynamic repository registration should resolve the canonical src repository class.'
        );

        SmokeAssert::true(
            $app->service('serviceCrud') instanceof \Cabnet\Application\Services\ServiceCrudService,
            'Dynamic CRUD registration should resolve the canonical src service class.'
        );
    }

    private function crudModuleBootstrapAppendsAdminRoutes(): void
    {
        $routes = require BASE_PATH . '/bootstrap/routes.php';
        $adminRoutes = $routes['admin'] ?? [];

        SmokeAssert::true(is_array($adminRoutes) && $adminRoutes !== [], 'Admin route table should remain an array after module bootstrapping.');

        $serviceIndex = array_values(array_filter($adminRoutes, static fn (array $route): bool => ($route['name'] ?? null) === 'admin.services.index'));
        SmokeAssert::same(1, count($serviceIndex), 'Module bootstrap should append exactly one admin services index route.');
        SmokeAssert::same('/services', $serviceIndex[0]['path'] ?? null, 'Module bootstrap should preserve the admin services index path.');
        SmokeAssert::same(
            \Cabnet\Application\Controllers\Admin\ServiceController::class,
            $serviceIndex[0]['handler'][0] ?? null,
            'Module bootstrap should preserve the canonical src service controller binding.'
        );
    }

    private function crudModuleBootstrapAppendsAdminMenuItems(): void
    {
        $items = require BASE_PATH . '/config/admin_menu.php';

        SmokeAssert::same('Dashboard', $items[0]['label'] ?? null, 'Dashboard should remain the first admin menu item.');
        SmokeAssert::same('Services', $items[1]['label'] ?? null, 'Module bootstrap should append the services menu item from config/modules.php.');
        SmokeAssert::same('/services', $items[1]['path'] ?? null, 'Module bootstrap should preserve the services admin menu path.');

        $last = $items[count($items) - 1] ?? [];
        SmokeAssert::same('/logout', $last['path'] ?? null, 'Logout should remain the trailing admin menu action.');
    }

    private function srcCrudGeneratorUsesNamespacedBaseClasses(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'fields' => [
                'title' => ['type' => 'text', 'required' => true],
                'slug' => ['type' => 'text', 'required' => true],
            ],
            'list_columns' => ['id', 'title', 'slug'],
            'searchable' => ['title', 'slug'],
            'default_order' => 'id DESC',
        ]);

        $definition = $files['src/Application/Crud/Definitions/ProductEntityDefinition.php'] ?? '';
        $repository = $files['src/Infrastructure/Repositories/ProductRepository.php'] ?? '';
        $service = $files['src/Application/Services/ProductCrudService.php'] ?? '';
        $controller = $files['src/Application/Controllers/Admin/ProductController.php'] ?? '';
        $moduleConfig = $files['generated/products_module_config.php.txt'] ?? '';

        SmokeAssert::contains('namespace Cabnet\Application\Crud\Definitions;', $definition, 'Generated src definition should stay namespaced.');
        SmokeAssert::contains('Cabnet\Application\Crud\CrudEntityDefinition', $definition, 'Generated src definition should use the canonical src CRUD definition model.');
        SmokeAssert::true(str_contains($definition, 'new \CrudEntityDefinition') === false, 'Generated src definition should not instantiate the legacy global CRUD definition model.');

        SmokeAssert::contains('namespace Cabnet\Infrastructure\Repositories;', $repository, 'Generated src repository should stay namespaced.');
        SmokeAssert::contains('extends BaseRepository', $repository, 'Generated src repository should extend the src repository base.');
        SmokeAssert::true(str_contains($repository, 'extends \BaseRepository') === false, 'Generated src repository should not fall back to the legacy global base.');

        SmokeAssert::contains('namespace Cabnet\Application\Services;', $service, 'Generated src service should stay namespaced.');
        SmokeAssert::contains('extends BaseService', $service, 'Generated src service should extend the src service base.');
        SmokeAssert::true(str_contains($service, 'extends \BaseService') === false, 'Generated src service should not fall back to the legacy global base.');

        SmokeAssert::contains('namespace Cabnet\Application\Controllers\Admin;', $controller, 'Generated src controller should stay namespaced.');
        SmokeAssert::contains('extends BaseCrudController', $controller, 'Generated src controller should extend the canonical src CRUD base.');
        SmokeAssert::contains("return 'products';", $controller, 'Generated src controller should now resolve CRUD behavior by module key.');
        SmokeAssert::true(str_contains($controller, 'extends \BaseCrudController') === false, 'Generated src controller should not fall back to the legacy global CRUD base.');

        SmokeAssert::contains("'products' => [", $moduleConfig, 'Generated CRUD pack should emit a module metadata block for config/modules.php.');
        SmokeAssert::contains("'repository_service' => 'productRepository'", $moduleConfig, 'Generated module metadata should preserve the repository service key.');
        SmokeAssert::contains("'crud_service' => 'productCrud'", $moduleConfig, 'Generated module metadata should preserve the CRUD service key.');
    }

    private function adminAuthMiddlewareRedirectsGuests(): void
    {
        TestEnvironment::seedRequest('GET', '/services');
        $app = bootstrap_app('admin');
        $middleware = new AdminAuthMiddleware();

        $response = $middleware->handle($app);
        SmokeAssert::true($response instanceof \Response, 'Guest access to admin service routes should redirect.');

        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Guest admin access should redirect.');
        SmokeAssert::same('/login', $snapshot['headers']['Location'] ?? null, 'Guest admin access should redirect to login route.');
        SmokeAssert::contains('Please sign in', implode(' ', $flash['warning'] ?? []), 'Middleware should explain why access was blocked.');
    }
}
