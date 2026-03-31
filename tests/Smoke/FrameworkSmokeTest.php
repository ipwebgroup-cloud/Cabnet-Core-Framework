<?php

declare(strict_types=1);

namespace Tests\Smoke;

use AdminAuthMiddleware;
use Cabnet\Application\Controllers\Admin\AuthController;
use Cabnet\AppRuntime;
use Cabnet\Bootstrap\Kernel;
use Cabnet\Generators\BlueprintLibrary;
use Cabnet\Generators\BlueprintValidator;
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
            'crud_definition_derives_validation_rules_from_field_metadata' => 'crudDefinitionDerivesValidationRulesFromFieldMetadata',
            'definition_driven_service_rejects_invalid_select_option' => 'definitionDrivenServiceRejectsInvalidSelectOption',
            'crud_form_fields_render_metadata_driven_attributes' => 'crudFormFieldsRenderMetadataDrivenAttributes',
            'crud_module_registry_resolves_services_definition' => 'crudModuleRegistryResolvesServicesDefinition',
            'crud_module_bootstrap_registers_dynamic_services' => 'crudModuleBootstrapRegistersDynamicServices',
            'crud_module_bootstrap_appends_admin_routes' => 'crudModuleBootstrapAppendsAdminRoutes',
            'crud_module_bootstrap_appends_admin_menu_items' => 'crudModuleBootstrapAppendsAdminMenuItems',
            'crud_module_registry_resolves_permissions_and_filters' => 'crudModuleRegistryResolvesPermissionsAndFilters',
            'crud_module_policy_can_expand_action_access_beyond_roles' => 'crudModulePolicyCanExpandActionAccessBeyondRoles',
            'admin_menu_service_filters_items_by_role' => 'adminMenuServiceFiltersItemsByRole',
            'admin_menu_can_apply_policy_aware_visibility_rules' => 'adminMenuCanApplyPolicyAwareVisibilityRules',
            'service_create_form_redirects_when_role_lacks_permission' => 'serviceCreateFormRedirectsWhenRoleLacksPermission',
            'crud_index_view_renders_registry_filters_and_hides_disallowed_actions' => 'crudIndexViewRendersRegistryFiltersAndHidesDisallowedActions',
            'src_crud_generator_uses_namespaced_base_classes' => 'srcCrudGeneratorUsesNamespacedBaseClasses',
            'layered_php_renderer_prefers_src_views_before_app_fallback' => 'layeredPhpRendererPrefersSrcViewsBeforeAppFallback',
            'canonical_shared_templates_resolve_from_src_presentation_layer' => 'canonicalSharedTemplatesResolveFromSrcPresentationLayer',
            'legacy_app_view_shims_delegate_to_src_presentation_templates' => 'legacyAppViewShimsDelegateToSrcPresentationTemplates',
            'built_in_services_view_resolves_from_src_presentation_layer' => 'builtInServicesViewResolvesFromSrcPresentationLayer',
            'src_crud_generator_targets_src_presentation_views' => 'srcCrudGeneratorTargetsSrcPresentationViews',
            'src_crud_generator_can_emit_twig_presentation_views' => 'srcCrudGeneratorCanEmitTwigPresentationViews',
            'src_crud_generator_preserves_policy_class_metadata' => 'srcCrudGeneratorPreservesPolicyClassMetadata',
            'src_crud_generator_preserves_module_permission_metadata' => 'srcCrudGeneratorPreservesModulePermissionMetadata',
            'src_crud_generator_preserves_explicit_filter_metadata' => 'srcCrudGeneratorPreservesExplicitFilterMetadata',
            'src_crud_generator_derives_filter_metadata_from_field_shortcuts' => 'srcCrudGeneratorDerivesFilterMetadataFromFieldShortcuts',
            'blueprint_library_lists_built_in_examples' => 'blueprintLibraryListsBuiltInExamples',
            'blueprint_library_resolves_named_examples' => 'blueprintLibraryResolvesNamedExamples',
            'blueprint_validator_rejects_missing_required_top_level_keys' => 'blueprintValidatorRejectsMissingRequiredTopLevelKeys',
            'blueprint_validator_rejects_translatable_fields_without_locales' => 'blueprintValidatorRejectsTranslatableFieldsWithoutLocales',
            'blueprint_validator_accepts_builtin_localized_service_example' => 'blueprintValidatorAcceptsBuiltInLocalizedServiceExample',
            'src_crud_generator_can_build_from_builtin_localized_service_example' => 'srcCrudGeneratorCanBuildFromBuiltInLocalizedServiceExample',
            'twig_renderer_maps_logical_php_templates_to_twig' => 'twigRendererMapsLogicalPhpTemplatesToTwig',
            'layered_twig_resolution_prefers_src_views_before_app_fallback' => 'layeredTwigResolutionPrefersSrcViewsBeforeAppFallback',
            'legacy_twig_layout_shim_delegates_to_src_layout' => 'legacyTwigLayoutShimDelegatesToSrcLayout',
            'request_input_merges_uploaded_files_into_payload' => 'requestInputMergesUploadedFilesIntoPayload',
            'crud_form_page_uses_multipart_for_upload_fields' => 'crudFormPageUsesMultipartForUploadFields',
            'crud_form_fields_render_upload_relation_and_translatable_inputs' => 'crudFormFieldsRenderUploadRelationAndTranslatableInputs',
            'definition_driven_service_persists_uploads_and_translatable_values' => 'definitionDrivenServicePersistsUploadsAndTranslatableValues',
            'app_make_can_constructor_inject_registered_services' => 'appMakeCanConstructorInjectRegisteredServices',
            'route_dispatcher_uses_app_resolver_for_controller_construction' => 'routeDispatcherUsesAppResolverForControllerConstruction',
            'middleware_executor_uses_app_resolver_for_middleware_construction' => 'middlewareExecutorUsesAppResolverForMiddlewareConstruction',
            'crud_module_registry_hydrates_relation_filter_options' => 'crudModuleRegistryHydratesRelationFilterOptions',
            'src_crud_generator_preserves_relation_filter_select_type_without_inline_options' => 'srcCrudGeneratorPreservesRelationFilterSelectTypeWithoutInlineOptions',
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

    private function crudDefinitionDerivesValidationRulesFromFieldMetadata(): void
    {
        $definition = \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::make();
        $rules = $definition->validationRules();

        SmokeAssert::same(
            ['required', 'string', 'min:2', 'max:255'],
            $rules['title'] ?? null,
            'Title field rules should be derived from field metadata.'
        );

        SmokeAssert::same(
            ['required', 'string', 'slug', 'min:2', 'max:255'],
            $rules['slug'] ?? null,
            'Slug field rules should include the slug constraint derived from field metadata.'
        );

        SmokeAssert::same(
            ['required', 'string', 'in:draft,published'],
            $rules['status'] ?? null,
            'Select options should become an in: validation rule.'
        );
    }

    private function definitionDrivenServiceRejectsInvalidSelectOption(): void
    {
        $service = new \Cabnet\Application\Services\ServiceCrudService(
            new class extends \Cabnet\Infrastructure\Repositories\ServiceRepository {
                public function __construct()
                {
                }
            },
            new \Validator()
        );

        $result = $service->create([
            'title' => 'Valid Title',
            'slug' => 'valid-title',
            'status' => 'archived',
            'summary' => 'A valid summary',
        ]);

        SmokeAssert::false($result->valid(), 'Definition-driven CRUD service should reject values outside select metadata options.');
        SmokeAssert::contains('draft, published', (string)$result->firstError('status'), 'Status validation should explain the allowed metadata-driven values.');
    }

    private function crudFormFieldsRenderMetadataDrivenAttributes(): void
    {
        TestEnvironment::seedRequest('GET', '/services/create');
        $app = bootstrap_app('admin');
        $app->auth()->login([
            'name' => 'Smoke Admin',
            'username' => 'smoke_admin',
            'role' => 'admin',
        ]);
        $controller = new \Cabnet\Application\Controllers\Admin\ServiceController();

        $response = $controller->createForm($app);
        $snapshot = ResponseInspector::snapshot($response);
        $body = (string)$snapshot['body'];

        SmokeAssert::contains('placeholder="premium-island-transfers"', $body, 'Form renderer should expose placeholder metadata from the canonical definition.');
        SmokeAssert::contains('maxlength="255"', $body, 'Form renderer should expose max-length metadata from the canonical definition.');
        SmokeAssert::contains('Lowercase letters, numbers, and hyphens only.', $body, 'Form renderer should expose help text from the canonical definition.');
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

    private function crudModuleRegistryResolvesPermissionsAndFilters(): void
    {
        $app = bootstrap_app('admin');
        /** @var \Cabnet\Application\Crud\CrudModuleRegistry $registry */
        $registry = $app->service('crudModuleRegistry');

        $permissions = $registry->permissions('services');
        $filters = $registry->filters('services');
        $payload = $registry->filterPayload('services', ['status' => 'draft']);

        SmokeAssert::true(in_array('editor', $permissions['view'] ?? [], true), 'Services view permission should allow the editor role.');
        SmokeAssert::same(['admin'], $permissions['create'] ?? null, 'Services create permission should remain admin-only.');
        SmokeAssert::same('Status', $filters['status']['label'] ?? null, 'Services filter metadata should expose the configured filter label.');
        SmokeAssert::same('select', $filters['status']['type'] ?? null, 'Services filter metadata should preserve the select type.');
        SmokeAssert::same('draft', $payload['status'] ?? null, 'Module filter payload should normalize active filter values through the canonical CRUD definition.');
    }


    private function crudModulePolicyCanExpandActionAccessBeyondRoles(): void
    {
        $policy = new class () implements \Cabnet\Application\Crud\CrudModulePolicy {
            public function allows(
                string $moduleKey,
                string $action,
                mixed $user,
                array $moduleMeta,
                \Cabnet\Application\Crud\CrudEntityDefinition $definition,
                array $context = []
            ): ?bool {
                $role = is_array($user) ? ($user['role'] ?? null) : null;
                if ($moduleKey === 'services' && $action === 'create' && $role === 'editor') {
                    return true;
                }

                return null;
            }
        };

        $modules = require BASE_PATH . '/config/modules.php';
        $modules['services']['policy'] = $policy;
        $registry = new \Cabnet\Application\Crud\CrudModuleRegistry($modules);

        SmokeAssert::same(['admin'], $registry->permissions('services')['create'] ?? null, 'Role-array permissions should remain unchanged when a policy hook is attached.');
        SmokeAssert::true(
            $registry->allowsForUser('services', 'create', ['role' => 'editor'], ['surface' => 'create']),
            'Policy hooks should be able to expand module action access without controller rewrites.'
        );
        SmokeAssert::false(
            $registry->allowsForUser('services', 'delete', ['role' => 'editor']),
            'Policy hooks should still fall back to role arrays for actions they do not override.'
        );
    }

    private function adminMenuServiceFiltersItemsByRole(): void
    {
        $app = bootstrap_app('admin');
        /** @var \Cabnet\Support\AdminMenu $menu */
        $menu = $app->service('adminMenu');

        $editorItems = $menu->visibleFor(['role' => 'editor']);
        $viewerItems = $menu->visibleFor(['role' => 'viewer']);

        $editorLabels = array_map(static fn (array $item): string => (string)($item['label'] ?? ''), $editorItems);
        $viewerLabels = array_map(static fn (array $item): string => (string)($item['label'] ?? ''), $viewerItems);

        SmokeAssert::true(in_array('Services', $editorLabels, true), 'Editor role should see the Services admin menu item when module view permission allows it.');
        SmokeAssert::false(in_array('Services', $viewerLabels, true), 'Unknown viewer role should not see the Services admin menu item.');
    }

    private function adminMenuCanApplyPolicyAwareVisibilityRules(): void
    {
        $policy = new class () implements \Cabnet\Application\Crud\CrudModulePolicy {
            public function allows(
                string $moduleKey,
                string $action,
                mixed $user,
                array $moduleMeta,
                \Cabnet\Application\Crud\CrudEntityDefinition $definition,
                array $context = []
            ): ?bool {
                $role = is_array($user) ? ($user['role'] ?? null) : null;
                if (($context['surface'] ?? null) === 'admin_menu' && $moduleKey === 'services' && $action === 'view' && $role === 'viewer') {
                    return true;
                }

                return null;
            }
        };

        $modules = require BASE_PATH . '/config/modules.php';
        $modules['services']['policy'] = $policy;
        $registry = new \Cabnet\Application\Crud\CrudModuleRegistry($modules);

        $menu = new \Cabnet\Support\AdminMenu([
            [
                'label' => 'Services',
                'path' => '/services',
                'match' => '/services',
                'roles' => ['editor'],
                'module_key' => 'services',
                'permission_action' => 'view',
            ],
        ], static function (array $item, mixed $user) use ($registry): ?bool {
            $moduleKey = $item['module_key'] ?? null;
            $action = $item['permission_action'] ?? 'view';
            if (!is_string($moduleKey) || $moduleKey === '') {
                return null;
            }

            return $registry->allowsForUser($moduleKey, is_string($action) ? $action : 'view', $user, [
                'surface' => 'admin_menu',
                'menu_item' => $item,
            ]);
        });

        $viewerItems = $menu->visibleFor(['role' => 'viewer']);
        $labels = array_map(static fn (array $item): string => (string)($item['label'] ?? ''), $viewerItems);

        SmokeAssert::true(in_array('Services', $labels, true), 'Policy-aware menu visibility should be able to expose a module beyond its fallback role list.');
    }

    private function serviceCreateFormRedirectsWhenRoleLacksPermission(): void
    {
        TestEnvironment::seedRequest('GET', '/services/create');
        $app = bootstrap_app('admin');
        $app->auth()->login([
            'name' => 'Editor User',
            'username' => 'editor_user',
            'role' => 'editor',
        ]);

        $controller = new \Cabnet\Application\Controllers\Admin\ServiceController();
        $response = $controller->createForm($app);
        $snapshot = ResponseInspector::snapshot($response);
        $flash = $app->flash()->all();

        SmokeAssert::same(302, $snapshot['statusCode'], 'Disallowed create access should redirect instead of rendering the form.');
        SmokeAssert::same('/', $snapshot['headers']['Location'] ?? null, 'Disallowed create access should redirect to the admin dashboard.');
        SmokeAssert::contains('do not have permission to create service records', strtolower(implode(' ', $flash['danger'] ?? [])), 'Permission denial should explain why create access was blocked.');
    }

    private function crudIndexViewRendersRegistryFiltersAndHidesDisallowedActions(): void
    {
        $app = bootstrap_app('admin');
        $definition = \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::make();
        /** @var \Cabnet\Application\Crud\CrudModuleRegistry $registry */
        $registry = $app->service('crudModuleRegistry');

        $output = $app->renderer()->render('admin/crud/index_table.php', [
            'definition' => $definition,
            'rows' => [['id' => 1, 'title' => 'Smoke', 'slug' => 'smoke', 'status' => 'draft', 'summary' => 'Summary']],
            'search' => '',
            'activeFilters' => ['status' => 'draft'],
            'filterDefinitions' => $registry->filters('services'),
            'listPath' => '/services',
            'createPath' => '/services/create',
            'editRouteName' => 'admin.services.edit',
            'deleteRouteName' => 'admin.services.delete',
            'csrfToken' => 'token-123',
            'urlService' => $app->url(),
            'canCreate' => false,
            'canEdit' => true,
            'canDelete' => false,
        ]);

        SmokeAssert::contains('name="status"', $output, 'Shared CRUD list view should render the metadata-driven status filter control.');
        SmokeAssert::contains('All statuses', $output, 'Shared CRUD list view should render the configured filter placeholder.');
        SmokeAssert::contains('selected', $output, 'Shared CRUD list view should preserve the active filter state.');
        SmokeAssert::false(str_contains($output, 'btn btn-sm btn-outline-danger'), 'Shared CRUD list view should hide delete actions when module permissions deny deletion.');
        SmokeAssert::false(str_contains($output, '>New Service<'), 'Shared CRUD list view should hide the create action when module permissions deny creation.');
    }

    private function canonicalSharedTemplatesResolveFromSrcPresentationLayer(): void
    {
        $resolver = new \Cabnet\View\TemplateResolver([
            'src' => BASE_PATH . '/src/Presentation/Views/php',
            'app' => BASE_PATH . '/app/Views/php',
        ]);

        SmokeAssert::same(
            BASE_PATH . '/src/Presentation/Views/php/layouts/admin.php',
            $resolver->resolve('layouts/admin.php'),
            'Canonical admin layout should now resolve from the src presentation layer.'
        );

        SmokeAssert::same(
            BASE_PATH . '/src/Presentation/Views/php/partials/flash.php',
            $resolver->resolve('partials/flash.php'),
            'Canonical flash partial should now resolve from the src presentation layer.'
        );

        SmokeAssert::same(
            BASE_PATH . '/src/Presentation/Views/php/public/home.php',
            $resolver->resolve('public/home.php'),
            'Canonical public home view should now resolve from the src presentation layer.'
        );
    }

    private function legacyAppViewShimsDelegateToSrcPresentationTemplates(): void
    {
        $adminLayoutShim = (string) file_get_contents(BASE_PATH . '/app/Views/php/layouts/admin.php');
        $flashShim = (string) file_get_contents(BASE_PATH . '/app/Views/php/partials/flash.php');
        $serviceIndexShim = (string) file_get_contents(BASE_PATH . '/app/Views/php/admin/services/index.php');

        SmokeAssert::contains(
            "/src/Presentation/Views/php/layouts/admin.php",
            $adminLayoutShim,
            'Legacy app admin layout should now delegate to the src-owned layout.'
        );

        SmokeAssert::contains(
            "/src/Presentation/Views/php/partials/flash.php",
            $flashShim,
            'Legacy app flash partial should now delegate to the src-owned partial.'
        );

        SmokeAssert::contains(
            "/src/Presentation/Views/php/admin/services/index.php",
            $serviceIndexShim,
            'Legacy app services index view should now delegate to the src-owned view.'
        );
    }

    private function builtInServicesViewResolvesFromSrcPresentationLayer(): void
    {
        $resolver = new \Cabnet\View\TemplateResolver([
            'src' => BASE_PATH . '/src/Presentation/Views/php',
            'app' => BASE_PATH . '/app/Views/php',
        ]);

        SmokeAssert::same(
            BASE_PATH . '/src/Presentation/Views/php/admin/services/index.php',
            $resolver->resolve('admin/services/index.php'),
            'Built-in services index view should resolve from the src presentation layer.'
        );

        SmokeAssert::same(
            BASE_PATH . '/app/Views/php/admin/services/index.php',
            $resolver->resolve('@app/admin/services/index.php'),
            'Explicit app aliasing should still resolve the compatibility shim.'
        );
    }

    private function layeredPhpRendererPrefersSrcViewsBeforeAppFallback(): void
    {
        $tempBase = sys_get_temp_dir() . '/cabnet_layered_view_' . uniqid('', true);
        $srcRoot = $tempBase . '/src';
        $appRoot = $tempBase . '/app';

        if (!mkdir($srcRoot . '/admin', 0777, true) && !is_dir($srcRoot . '/admin')) {
            throw new \RuntimeException('Failed to create temporary src view directory.');
        }

        if (!mkdir($appRoot . '/admin', 0777, true) && !is_dir($appRoot . '/admin')) {
            throw new \RuntimeException('Failed to create temporary app view directory.');
        }

        file_put_contents($srcRoot . '/admin/example.php', '<p>src-layer</p>');
        file_put_contents($appRoot . '/admin/example.php', '<p>app-layer</p>');

        $renderer = new \Cabnet\View\PhpRenderer([
            'src' => $srcRoot,
            'app' => $appRoot,
        ]);

        SmokeAssert::contains('src-layer', $renderer->render('admin/example.php'), 'Layered renderer should prefer src views when both roots contain the same template.');
        SmokeAssert::contains('app-layer', $renderer->render('@app/admin/example.php'), 'Layered renderer should still allow explicit app-view fallback targeting.');
    }

    private function srcCrudGeneratorTargetsSrcPresentationViews(): void
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

        $indexView = $files['src/Presentation/Views/php/admin/products/index.php'] ?? '';
        $createView = $files['src/Presentation/Views/php/admin/products/create.php'] ?? '';
        $notes = $files['generated/products_implementation_notes.txt'] ?? '';

        SmokeAssert::contains("include BASE_PATH . '/src/Presentation/Views/php/admin/crud/index_table.php';", $indexView, 'Generated CRUD index view should target the src presentation CRUD partial.');
        SmokeAssert::contains("include BASE_PATH . '/src/Presentation/Views/php/admin/crud/form_page.php';", $createView, 'Generated CRUD form view should target the src presentation CRUD form partial.');
        SmokeAssert::contains('src/Presentation/Views/php/admin', $notes, 'Generated implementation notes should describe src-owned admin presentation views as the preferred target.');
    }

    private function srcCrudGeneratorCanEmitTwigPresentationViews(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'view_engines' => ['php', 'twig'],
            'fields' => [
                'title' => ['type' => 'text', 'required' => true],
                'slug' => ['type' => 'text', 'required' => true],
            ],
            'list_columns' => ['id', 'title', 'slug'],
            'searchable' => ['title', 'slug'],
            'default_order' => 'id DESC',
        ]);

        $twigIndex = $files['src/Presentation/Views/twig/admin/products/index.twig'] ?? '';
        $twigCreate = $files['src/Presentation/Views/twig/admin/products/create.twig'] ?? '';
        $notes = $files['generated/products_implementation_notes.txt'] ?? '';

        SmokeAssert::contains("{% extends '@src/admin/crud/index_table.twig' %}", $twigIndex, 'Generated Twig CRUD index view should extend the canonical src shared CRUD index template.');
        SmokeAssert::contains("{% extends '@src/admin/crud/form_page.twig' %}", $twigCreate, 'Generated Twig CRUD form view should extend the canonical src shared CRUD form template.');
        SmokeAssert::contains('Requested view engines: php, twig.', $notes, 'Generated implementation notes should record the requested presentation targets.');
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
        SmokeAssert::contains('extends DefinitionCrudService', $service, 'Generated src service should extend the canonical definition-driven CRUD service base.');
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


    private function srcCrudGeneratorPreservesPolicyClassMetadata(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'policy_class' => '\App\Policies\ProductPolicy',
        ]);

        $configSnippet = (string)($files['generated/products_module_config.php.txt'] ?? '');
        SmokeAssert::contains("'policy_class' => \App\Policies\ProductPolicy::class", $configSnippet, 'Generated module config should preserve optional policy_class metadata.');
    }

    private function srcCrudGeneratorPreservesModulePermissionMetadata(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'access_roles' => ['admin', 'editor'],
            'permissions' => [
                'view' => ['admin', 'editor'],
                'create' => ['admin'],
                'edit' => ['admin'],
                'delete' => ['admin'],
            ],
            'admin_middleware' => ['session', 'admin.auth', 'audit'],
            'show_in_admin_menu' => false,
        ]);

        $configSnippet = (string)($files['generated/products_module_config.php.txt'] ?? '');
        SmokeAssert::contains("'admin_middleware' => array (", $configSnippet, 'Generated module config should preserve admin_middleware metadata.');
        SmokeAssert::contains("'audit'", $configSnippet, 'Generated module config should preserve custom middleware entries.');
        SmokeAssert::contains("'access_roles' => array (", $configSnippet, 'Generated module config should preserve optional access_roles metadata.');
        SmokeAssert::contains("'editor'", $configSnippet, 'Generated module config should preserve non-admin access roles.');
        SmokeAssert::contains("'permissions' => array (", $configSnippet, 'Generated module config should preserve explicit permission metadata.');
        SmokeAssert::contains("'show_in_admin_menu' => false", $configSnippet, 'Generated module config should preserve explicit admin menu visibility metadata.');
    }

    private function srcCrudGeneratorPreservesExplicitFilterMetadata(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'fields' => [
                'status' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'options' => [
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ],
                ],
                'category' => [
                    'type' => 'select',
                    'label' => 'Category',
                    'options' => [
                        'a' => 'A',
                        'b' => 'B',
                    ],
                ],
            ],
            'filters' => [
                'status' => [
                    'field' => 'status',
                    'type' => 'select',
                    'placeholder' => 'All statuses',
                ],
                'category' => 'category',
            ],
        ]);

        $configSnippet = (string)($files['generated/products_module_config.php.txt'] ?? '');
        SmokeAssert::contains("'filters' => array (", $configSnippet, 'Generated module config should preserve explicit filter metadata.');
        SmokeAssert::contains("'field' => 'status'", $configSnippet, 'Generated module config should preserve named filters.');
        SmokeAssert::contains("'placeholder' => 'All statuses'", $configSnippet, 'Generated module config should preserve filter placeholders.');
        SmokeAssert::contains("'query_key' => 'category'", $configSnippet, 'Generated module config should support shorthand string filters.');
        SmokeAssert::contains("'field' => 'category'", $configSnippet, 'Generated module config should resolve shorthand filter fields correctly.');
    }

    private function srcCrudGeneratorDerivesFilterMetadataFromFieldShortcuts(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'products',
            'singular_label' => 'Product',
            'plural_label' => 'Products',
            'table' => 'products',
            'fields' => [
                'status' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'options' => [
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ],
                    'filterable' => true,
                ],
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'filter' => [
                        'placeholder' => 'Search title',
                    ],
                ],
                'image_path' => [
                    'type' => 'image',
                    'label' => 'Image',
                    'filterable' => true,
                ],
            ],
        ]);

        $configSnippet = (string)($files['generated/products_module_config.php.txt'] ?? '');
        $notes = (string)($files['generated/products_implementation_notes.txt'] ?? '');

        SmokeAssert::contains("'field' => 'status'", $configSnippet, 'Field-level filterable metadata should derive a status filter entry.');
        SmokeAssert::contains("'type' => 'select'", $configSnippet, 'Derived select filters should preserve select typing when options are available.');
        SmokeAssert::contains("'field' => 'title'", $configSnippet, 'Field-level filter metadata should derive named text filters.');
        SmokeAssert::contains("'placeholder' => 'Search title'", $configSnippet, 'Derived field filters should preserve custom placeholder metadata.');
        SmokeAssert::false(str_contains($configSnippet, "'image_path' => array ("), 'Upload fields should not automatically derive list filters.');
        SmokeAssert::contains('Generated filters: status, title.', $notes, 'Implementation notes should summarize derived filters for the scaffold.');
    }


    private function blueprintLibraryListsBuiltInExamples(): void
    {
        $examples = BlueprintLibrary::listExamples(BASE_PATH);

        SmokeAssert::arrayHasKey('content-pages', $examples, 'Blueprint library should expose the content-pages example.');
        SmokeAssert::arrayHasKey('media-assets', $examples, 'Blueprint library should expose the media-assets example.');
        SmokeAssert::arrayHasKey('localized-services', $examples, 'Blueprint library should expose the localized-services example.');
        SmokeAssert::same('services', $examples['localized-services']['entity_key'] ?? null, 'Localized services example should preserve its entity key.');
        SmokeAssert::contains('translatable', implode(', ', $examples['localized-services']['feature_tags'] ?? []), 'Localized services example should advertise translatable support.');
    }

    private function blueprintLibraryResolvesNamedExamples(): void
    {
        $resolved = BlueprintLibrary::resolvePath(BASE_PATH, 'example:localized-services');

        SmokeAssert::same(
            BASE_PATH . '/blueprints/examples/localized-services.json',
            $resolved,
            'Blueprint library should resolve built-in example aliases to canonical example files.'
        );
    }


    private function blueprintValidatorRejectsMissingRequiredTopLevelKeys(): void
    {
        $errors = BlueprintValidator::validate([
            'entity_key' => 'pages',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                ],
            ],
        ]);

        SmokeAssert::true($errors !== [], 'Blueprint validator should reject malformed top-level metadata.');
        SmokeAssert::contains('singular_label', implode(' ', $errors), 'Blueprint validator should report missing singular_label.');
        SmokeAssert::contains('plural_label', implode(' ', $errors), 'Blueprint validator should report missing plural_label.');
        SmokeAssert::contains('table', implode(' ', $errors), 'Blueprint validator should report missing table.');
    }

    private function blueprintValidatorRejectsTranslatableFieldsWithoutLocales(): void
    {
        $errors = BlueprintValidator::validate([
            'entity_key' => 'pages',
            'singular_label' => 'Page',
            'plural_label' => 'Pages',
            'table' => 'pages',
            'fields' => [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'translatable' => true,
                ],
            ],
        ]);

        SmokeAssert::true($errors !== [], 'Blueprint validator should reject translatable fields without locales.');
        SmokeAssert::contains('locales', implode(' ', $errors), 'Blueprint validator should explain the missing locales requirement.');
    }

    private function blueprintValidatorAcceptsBuiltInLocalizedServiceExample(): void
    {
        $blueprint = BlueprintLibrary::load(BASE_PATH, 'example:localized-services');
        $errors = BlueprintValidator::validate($blueprint);

        SmokeAssert::same([], $errors, 'Built-in localized service example should satisfy blueprint schema validation.');
    }

    private function srcCrudGeneratorCanBuildFromBuiltInLocalizedServiceExample(): void
    {
        $blueprint = BlueprintLibrary::load(BASE_PATH, 'example:localized-services');
        SmokeAssert::true(is_array($blueprint), 'Built-in localized service blueprint should load as an array.');

        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack($blueprint);

        $configSnippet = (string)($files['generated/services_module_config.php.txt'] ?? '');
        $definition = (string)($files['src/Application/Crud/Definitions/ServiceEntityDefinition.php'] ?? '');
        $notes = (string)($files['generated/services_implementation_notes.txt'] ?? '');

        SmokeAssert::contains("'policy_class' => \App\Policies\ServicePolicy::class", $configSnippet, 'Built-in localized service blueprint should preserve policy_class metadata in generated config.');
        SmokeAssert::contains("'admin_middleware' => array (", $configSnippet, 'Built-in localized service blueprint should preserve admin middleware metadata.');
        SmokeAssert::contains("'audit'", $configSnippet, 'Built-in localized service blueprint should preserve custom middleware entries.');
        SmokeAssert::contains("'translatable' => true", $definition, 'Built-in localized service blueprint should preserve translatable field metadata in the generated definition.');
        SmokeAssert::contains("'table' => 'service_categories'", $definition, 'Built-in localized service blueprint should preserve relation metadata in the generated definition.');
        SmokeAssert::arrayHasKey('src/Presentation/Views/twig/admin/services/index.twig', $files, 'Built-in localized service blueprint should emit Twig CRUD views.');
        SmokeAssert::contains('Requested view engines: php, twig.', $notes, 'Built-in localized service blueprint should preserve multi-engine view output in implementation notes.');
    }

    private function twigRendererMapsLogicalPhpTemplatesToTwig(): void
    {
        SmokeAssert::same(
            'admin/login.twig',
            \Cabnet\View\TwigRenderer::normalizeLogicalTemplate('admin/login.php'),
            'Twig renderer should map logical admin PHP templates to Twig names.'
        );

        SmokeAssert::same(
            '@src/admin/services/index.twig',
            \Cabnet\View\TwigRenderer::normalizeLogicalTemplate('@src/admin/services/index.php'),
            'Twig renderer should preserve aliases while mapping to Twig names.'
        );

        SmokeAssert::same(
            'public/home.twig',
            \Cabnet\View\TwigRenderer::normalizeLogicalTemplate('public/home'),
            'Twig renderer should append the Twig extension for logical extensionless template names.'
        );
    }

    private function layeredTwigResolutionPrefersSrcViewsBeforeAppFallback(): void
    {
        $resolver = new \Cabnet\View\TemplateResolver([
            'src' => BASE_PATH . '/src/Presentation/Views/twig',
            'app' => BASE_PATH . '/app/Views/twig',
        ]);

        $resolved = $resolver->resolve('admin/services/index.twig');
        SmokeAssert::same(
            BASE_PATH . '/src/Presentation/Views/twig/admin/services/index.twig',
            $resolved,
            'Layered Twig resolution should prefer the src-owned service index template.'
        );

        $appResolved = $resolver->resolve('@app/admin/services/index.twig');
        SmokeAssert::same(
            BASE_PATH . '/app/Views/twig/admin/services/index.twig',
            $appResolved,
            'Explicit app alias should still resolve the legacy Twig compatibility shim.'
        );
    }

    private function legacyTwigLayoutShimDelegatesToSrcLayout(): void
    {
        $shim = (string)file_get_contents(BASE_PATH . '/app/Views/twig/admin/layouts/admin.twig');

        SmokeAssert::contains(
            "@src/layouts/admin.twig",
            $shim,
            'Legacy Twig admin layout shim should delegate to the canonical src-owned Twig layout.'
        );

        SmokeAssert::true(
            is_file(BASE_PATH . '/src/Presentation/Views/twig/layouts/public.twig'),
            'Canonical src-owned Twig public layout should exist.'
        );
    }

    private function requestInputMergesUploadedFilesIntoPayload(): void
    {
        $upload = TestEnvironment::fakeUpload('hero.png', 'png-bytes', 'image/png');
        TestEnvironment::seedRequest('POST', '/services', ['title' => 'Smoke'], [], [
            'hero_image' => $upload,
        ]);

        $request = new \Cabnet\Http\Request();
        $input = $request->input();

        SmokeAssert::same('Smoke', $input['title'] ?? null, 'Request input should still include post values.');
        SmokeAssert::true(is_array($input['hero_image'] ?? null), 'Request input should merge uploaded files into the payload.');
        SmokeAssert::same('hero.png', $input['hero_image']['name'] ?? null, 'Merged upload payload should preserve file metadata.');
    }

    private function crudFormPageUsesMultipartForUploadFields(): void
    {
        $definition = new \Cabnet\Application\Crud\CrudEntityDefinition(
            key: 'media',
            label: 'Media',
            table: 'media',
            fields: [
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'hero_image' => ['type' => 'image', 'label' => 'Hero Image', 'upload' => true, 'accept' => 'image/*'],
            ]
        );

        $renderer = new \Cabnet\View\PhpRenderer(BASE_PATH . '/src/Presentation/Views/php');
        $output = $renderer->render('admin/crud/form_page.php', [
            'definition' => $definition,
            'mode' => 'create',
            'formAction' => '/media',
            'backPath' => '/media',
            'csrfToken' => 'csrf-token',
            'flashMessages' => [],
            'authUser' => ['name' => 'Smoke Admin'],
            'logoutCsrfToken' => 'logout-token',
        ]);

        SmokeAssert::contains('enctype="multipart/form-data"', $output, 'Upload-enabled CRUD forms should render multipart encoding.');
    }

    private function crudFormFieldsRenderUploadRelationAndTranslatableInputs(): void
    {
        $definition = new \Cabnet\Application\Crud\CrudEntityDefinition(
            key: 'articles',
            label: 'Articles',
            table: 'articles',
            fields: [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'required' => true,
                    'translatable' => true,
                    'locales' => ['en', 'el'],
                    'max' => 255,
                ],
                'category_id' => [
                    'type' => 'select',
                    'label' => 'Category',
                    'options' => ['1' => 'News', '2' => 'Guides'],
                    'placeholder' => 'Choose category',
                ],
                'hero_image' => [
                    'type' => 'image',
                    'label' => 'Hero Image',
                    'upload' => true,
                    'accept' => 'image/*',
                ],
            ]
        );

        $renderer = new \Cabnet\View\PhpRenderer(BASE_PATH . '/src/Presentation/Views/php');
        $output = $renderer->render('admin/crud/form_fields.php', [
            'definition' => $definition,
            'old' => ['title' => ['en' => 'Hello', 'el' => 'Γεια']],
            'errors' => [],
            'row' => ['hero_image' => '/assets/uploads/articles/hero.png'],
        ]);

        SmokeAssert::contains('name="title[en]"', $output, 'Translatable fields should render locale-specific inputs.');
        SmokeAssert::contains('name="category_id"', $output, 'Relation/select fields should render select controls.');
        SmokeAssert::contains('Choose category', $output, 'Select fields should render their placeholder option.');
        SmokeAssert::contains('type="file"', $output, 'Upload fields should render file inputs.');
    }

    private function definitionDrivenServicePersistsUploadsAndTranslatableValues(): void
    {
        $definition = new \Cabnet\Application\Crud\CrudEntityDefinition(
            key: 'articles',
            label: 'Articles',
            table: 'articles',
            fields: [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'required' => true,
                    'translatable' => true,
                    'locales' => ['en', 'el'],
                    'min' => 2,
                    'max' => 255,
                ],
                'category_id' => [
                    'type' => 'select',
                    'label' => 'Category',
                    'required' => true,
                    'relation' => [
                        'table' => 'categories',
                        'value_column' => 'id',
                        'label_column' => 'name',
                    ],
                ],
                'hero_image' => [
                    'type' => 'image',
                    'label' => 'Hero Image',
                    'upload' => true,
                    'image' => true,
                    'max_size_kb' => 512,
                    'upload_dir' => 'articles',
                ],
            ]
        );

        $repository = new class implements \Cabnet\Infrastructure\Repositories\CrudRepositoryContract {
            public array $created = [];
            public function findPage(array $searchColumns = [], string $search = '', int $page = 1, int $perPage = 15, array $filters = [], string $orderBy = 'id DESC'): array { return ['rows' => [], 'total' => 0, 'page' => 1, 'per_page' => 15]; }
            public function findById(int $id): ?array { return null; }
            public function create(array $data): bool { $this->created[] = $data; return true; }
            public function updateById(int $id, array $data): bool { return true; }
            public function deleteById(int $id): bool { return true; }
        };

        $db = new class {
            public function select(string $sql, array $params = []): array
            {
                return [
                    ['value' => '1', 'label' => 'News'],
                    ['value' => '2', 'label' => 'Guides'],
                ];
            }
        };

        $uploadPath = BASE_PATH . '/public/assets/uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $service = new class($definition, $repository, new \Validator(), $db, new \Cabnet\Support\UploadManager([
            'public_uploads_path' => $uploadPath,
            'public_uploads_url' => '/assets/uploads',
        ])) extends \Cabnet\Application\Services\DefinitionCrudService {};

        $formDefinition = $service->formDefinition();
        SmokeAssert::same('News', $formDefinition->field('category_id')['options']['1'] ?? null, 'Relation metadata should hydrate select options for forms.');

        $result = $service->create([
            'title' => ['en' => 'Hello', 'el' => 'Γεια'],
            'category_id' => '1',
            'hero_image' => TestEnvironment::fakeUpload('hero.png', 'png-bytes', 'image/png'),
        ]);

        SmokeAssert::true($result->valid(), 'Definition-driven service should accept valid upload and translatable payloads.');
        SmokeAssert::true(isset($repository->created[0]['title']) && is_string($repository->created[0]['title']), 'Translatable data should be prepared for persistence.');
        SmokeAssert::contains('"en":"Hello"', $repository->created[0]['title'], 'Translatable persistence payload should be JSON encoded.');
        SmokeAssert::contains('/assets/uploads/articles/', (string)($repository->created[0]['hero_image'] ?? ''), 'Upload manager should persist files into the configured public upload path.');
    }

    private function appMakeCanConstructorInjectRegisteredServices(): void
    {
        $app = bootstrap_app('admin');
        $controller = $app->make(TestConstructorInjectedController::class);

        SmokeAssert::true($controller instanceof TestConstructorInjectedController, 'App::make should instantiate constructor-injected controllers.');
        SmokeAssert::true($controller->registry instanceof \Cabnet\Application\Crud\CrudModuleRegistry, 'App::make should inject registered src services by type.');
        SmokeAssert::true($controller->app === $app, 'App::make should inject the legacy app bridge when requested.');
        SmokeAssert::same('recursive-ok', $controller->recursive->marker(), 'App::make should resolve plain instantiable src classes recursively.');
    }

    private function routeDispatcherUsesAppResolverForControllerConstruction(): void
    {
        $app = bootstrap_app('admin');
        $dispatcher = new \Cabnet\Routing\RouteDispatcher();
        $response = $dispatcher->dispatch([TestConstructorInjectedController::class, 'show'], $app, ['id' => '42']);
        $snapshot = ResponseInspector::snapshot($response);

        SmokeAssert::same(200, $snapshot['statusCode'], 'Route dispatcher should resolve constructor-injected controllers successfully.');
        SmokeAssert::contains('services|42|admin', (string)$snapshot['body'], 'Route dispatcher should dispatch constructor-injected controller actions correctly.');
    }

    private function middlewareExecutorUsesAppResolverForMiddlewareConstruction(): void
    {
        $app = bootstrap_app('admin');
        $executor = new \Cabnet\Middleware\MiddlewareExecutor([
            'constructor.injected' => TestConstructorInjectedMiddleware::class,
        ]);

        $response = $executor->run(['constructor.injected'], $app);
        $snapshot = ResponseInspector::snapshot($response);

        SmokeAssert::same(302, $snapshot['statusCode'], 'Middleware executor should resolve constructor-injected middleware successfully.');
        SmokeAssert::same('/services', $snapshot['headers']['Location'] ?? null, 'Constructor-injected middleware should be able to return a redirect response.');
    }

    private function crudModuleRegistryHydratesRelationFilterOptions(): void
    {
        $db = new class {
            public function select(string $sql, array $params = []): array
            {
                return [
                    ['value' => '1', 'label' => 'News'],
                    ['value' => '2', 'label' => 'Guides'],
                ];
            }
        };

        $registry = new \Cabnet\Application\Crud\CrudModuleRegistry([
            'articles' => [
                'definition_class' => RelationFilterEntityDefinition::class,
                'filters' => [
                    'category_id' => [
                        'field' => 'category_id',
                        'type' => 'select',
                        'placeholder' => 'All categories',
                    ],
                ],
            ],
        ], $db);

        $filters = $registry->filters('articles');

        SmokeAssert::same('select', $filters['category_id']['type'] ?? null, 'Relation-backed filters should preserve select typing.');
        SmokeAssert::same('News', $filters['category_id']['options']['1'] ?? null, 'Relation-backed filters should hydrate option labels from relation metadata.');
        SmokeAssert::same('Guides', $filters['category_id']['options']['2'] ?? null, 'Relation-backed filters should hydrate all available relation options.');
    }

    private function srcCrudGeneratorPreservesRelationFilterSelectTypeWithoutInlineOptions(): void
    {
        $writer = new CrudScaffoldWriter();
        $files = $writer->buildCrudPack([
            'entity_key' => 'articles',
            'singular_label' => 'Article',
            'plural_label' => 'Articles',
            'table' => 'articles',
            'fields' => [
                'category_id' => [
                    'type' => 'select',
                    'label' => 'Category',
                    'filterable' => true,
                    'relation' => [
                        'table' => 'categories',
                        'value_column' => 'id',
                        'label_column' => 'name',
                        'order_by' => 'name',
                    ],
                ],
            ],
        ]);

        $configSnippet = (string)($files['generated/articles_module_config.php.txt'] ?? '');
        $notes = (string)($files['generated/articles_implementation_notes.txt'] ?? '');

        SmokeAssert::contains("'field' => 'category_id'", $configSnippet, 'Relation-backed filterable fields should derive a filter entry.');
        SmokeAssert::contains("'type' => 'select'", $configSnippet, 'Relation-backed filters should remain select controls even without inline options.');
        SmokeAssert::contains('Generated filters: category_id.', $notes, 'Implementation notes should report derived relation filters.');
    }

}



final class TestRecursiveDependency
{
    public function marker(): string
    {
        return 'recursive-ok';
    }
}

final class TestConstructorInjectedController
{
    public function __construct(
        public \Cabnet\Application\Crud\CrudModuleRegistry $registry,
        public TestRecursiveDependency $recursive,
        public object $app
    ) {
    }

    public function show(object $app, array $params = []): \Response
    {
        return $app->response()->html(sprintf(
            '%s|%s|%s',
            $this->registry->meta('services')['table'] ?? null,
            $params['id'] ?? 'missing',
            $app->context()
        ));
    }
}

final class TestConstructorInjectedMiddleware
{
    public function __construct(private \Cabnet\Application\Crud\CrudModuleRegistry $registry)
    {
    }

    public function handle(\App $app): ?\Response
    {
        if ($this->registry->meta('services')['table'] ?? null === 'services') {
            return $app->response()->redirect('/services');
        }

        return null;
    }

}


final class RelationFilterEntityDefinition
{
    public static function make(): \Cabnet\Application\Crud\CrudEntityDefinition
    {
        return new \Cabnet\Application\Crud\CrudEntityDefinition(
            key: 'articles',
            label: 'Articles',
            table: 'articles',
            fields: [
                'category_id' => [
                    'type' => 'select',
                    'label' => 'Category',
                    'relation' => [
                        'table' => 'categories',
                        'value_column' => 'id',
                        'label_column' => 'name',
                        'order_by' => 'name',
                    ],
                ],
            ],
            listColumns: ['category_id'],
            searchable: [],
            defaultOrder: 'id DESC'
        );
    }
}
