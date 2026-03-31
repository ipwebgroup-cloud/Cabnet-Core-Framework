<?php

declare(strict_types=1);

namespace Tests\Smoke;

use AdminAuthMiddleware;
use Cabnet\Application\Controllers\Admin\AuthController;
use Cabnet\AppRuntime;
use Cabnet\Bootstrap\Kernel;
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
            'src_service_controller_uses_src_crud_base' => 'srcServiceControllerUsesSrcCrudBase',
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
