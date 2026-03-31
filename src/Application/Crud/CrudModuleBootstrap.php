<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

use App;
use InvalidArgumentException;

final class CrudModuleBootstrap
{
    /**
     * @param array<string, callable|mixed> $services
     * @param array<string, array<string, mixed>> $modules
     * @return array<string, callable|mixed>
     */
    public static function registerServices(array $services, array $modules): array
    {
        foreach ($modules as $key => $meta) {
            if (!self::enabled($meta)) {
                continue;
            }

            $repositoryService = self::stringMeta($meta, 'repository_service');
            $repositoryClass = self::stringMeta($meta, 'repository_class');
            $crudService = self::stringMeta($meta, 'crud_service');
            $serviceClass = self::stringMeta($meta, 'service_class');

            if ($repositoryService !== null && $repositoryClass !== null && !array_key_exists($repositoryService, $services)) {
                $services[$repositoryService] = static function (App $app) use ($repositoryClass): object {
                    if (!class_exists($repositoryClass)) {
                        throw new InvalidArgumentException("CRUD repository class [{$repositoryClass}] was not found.");
                    }

                    return new $repositoryClass($app->service('db'));
                };
            }

            if ($crudService !== null && $serviceClass !== null && !array_key_exists($crudService, $services)) {
                $services[$crudService] = static function (App $app) use ($serviceClass, $repositoryService): object {
                    if (!class_exists($serviceClass)) {
                        throw new InvalidArgumentException("CRUD service class [{$serviceClass}] was not found.");
                    }

                    if ($repositoryService === null || $repositoryService === '') {
                        throw new InvalidArgumentException("CRUD service class [{$serviceClass}] is missing a repository service mapping.");
                    }

                    return new $serviceClass(
                        $app->service($repositoryService),
                        $app->validator()
                    );
                };
            }
        }

        return $services;
    }

    /**
     * @param array<int, array<string, mixed>> $routes
     * @param array<string, array<string, mixed>> $modules
     * @return array<int, array<string, mixed>>
     */
    public static function appendAdminRoutes(array $routes, array $modules): array
    {
        $existing = [];
        foreach ($routes as $route) {
            $name = $route['name'] ?? null;
            if (is_string($name) && $name !== '') {
                $existing[$name] = true;
            }
        }

        foreach ($modules as $key => $meta) {
            if (!self::enabled($meta)) {
                continue;
            }

            $controllerClass = self::requiredStringMeta($meta, 'controller_class', $key);
            $routePrefix = self::normalizeRoutePrefix(self::requiredStringMeta($meta, 'route_prefix', $key));
            $routeBase = self::requiredStringMeta($meta, 'admin_route_base', $key);
            $middleware = self::middlewareMeta($meta);

            $moduleRoutes = [
                ['method' => 'GET', 'path' => $routePrefix, 'handler' => [$controllerClass, 'index'], 'name' => $routeBase . '.index', 'middleware' => $middleware],
                ['method' => 'GET', 'path' => $routePrefix . '/create', 'handler' => [$controllerClass, 'createForm'], 'name' => $routeBase . '.create', 'middleware' => $middleware],
                ['method' => 'POST', 'path' => $routePrefix, 'handler' => [$controllerClass, 'store'], 'name' => $routeBase . '.store', 'middleware' => $middleware],
                ['method' => 'GET', 'path' => $routePrefix . '/{id}/edit', 'handler' => [$controllerClass, 'editForm'], 'name' => $routeBase . '.edit', 'middleware' => $middleware],
                ['method' => 'POST', 'path' => $routePrefix . '/{id}/update', 'handler' => [$controllerClass, 'update'], 'name' => $routeBase . '.update', 'middleware' => $middleware],
                ['method' => 'POST', 'path' => $routePrefix . '/{id}/delete', 'handler' => [$controllerClass, 'destroy'], 'name' => $routeBase . '.delete', 'middleware' => $middleware],
            ];

            foreach ($moduleRoutes as $route) {
                $name = (string)($route['name'] ?? '');
                if ($name !== '' && isset($existing[$name])) {
                    continue;
                }

                $routes[] = $route;
                if ($name !== '') {
                    $existing[$name] = true;
                }
            }
        }

        return $routes;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<string, array<string, mixed>> $modules
     * @return array<int, array<string, mixed>>
     */
    public static function appendAdminMenu(array $items, array $modules): array
    {
        $existingPaths = [];
        foreach ($items as $item) {
            $path = $item['path'] ?? null;
            if (is_string($path) && $path !== '') {
                $existingPaths[$path] = true;
            }
        }

        $logoutItem = null;
        $menuItems = [];
        foreach ($items as $item) {
            if (($item['path'] ?? null) === '/logout') {
                $logoutItem = $item;
                continue;
            }

            $menuItems[] = $item;
        }

        foreach ($modules as $meta) {
            if (!self::enabled($meta) || empty($meta['show_in_admin_menu'])) {
                continue;
            }

            $path = self::normalizeRoutePrefix((string)($meta['route_prefix'] ?? '/'));
            if (isset($existingPaths[$path])) {
                continue;
            }

            $menuItems[] = [
                'label' => (string)($meta['label'] ?? ucfirst(trim($path, '/'))),
                'path' => $path,
                'match' => $path,
                'roles' => self::permissionRoles($meta, 'view'),
            ];
            $existingPaths[$path] = true;
        }

        if (is_array($logoutItem)) {
            $menuItems[] = $logoutItem;
        }

        return $menuItems;
    }

    /** @param array<string, mixed> $meta */
    private static function enabled(array $meta): bool
    {
        return (bool)($meta['enabled'] ?? false);
    }

    /** @param array<string, mixed> $meta */
    private static function stringMeta(array $meta, string $key): ?string
    {
        $value = $meta[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @param array<string, mixed> $meta */
    private static function requiredStringMeta(array $meta, string $key, string $moduleKey): string
    {
        $value = self::stringMeta($meta, $key);

        if ($value === null) {
            throw new InvalidArgumentException("CRUD module [{$moduleKey}] is missing required metadata [{$key}].");
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $meta
     * @return array<int, string>
     */
    private static function middlewareMeta(array $meta): array
    {
        $middleware = $meta['admin_middleware'] ?? ['session', 'admin.auth'];

        if (!is_array($middleware) || $middleware === []) {
            return ['session', 'admin.auth'];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $value): ?string => is_string($value) && $value !== '' ? $value : null,
            $middleware
        )));
    }

    private static function normalizeRoutePrefix(string $routePrefix): string
    {
        $routePrefix = '/' . trim($routePrefix, '/');
        return $routePrefix === '/' ? '/' : rtrim($routePrefix, '/');
    }

    /**
     * @param array<string, mixed> $meta
     * @return array<int, string>
     */
    private static function permissionRoles(array $meta, string $action): array
    {
        $configured = is_array($meta['permissions'] ?? null) ? (array)$meta['permissions'] : [];
        $roles = self::rolesMeta($configured[$action] ?? null);

        if ($roles !== []) {
            return $roles;
        }

        $access = self::rolesMeta($meta['access_roles'] ?? null);
        return $access !== [] ? $access : ['admin'];
    }

    /** @return array<int, string> */
    private static function rolesMeta(mixed $roles): array
    {
        if (is_string($roles) && $roles !== '') {
            return [$roles];
        }

        if (!is_array($roles) || $roles === []) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $value): ?string => is_string($value) && $value !== '' ? $value : null,
            $roles
        )));
    }
}
