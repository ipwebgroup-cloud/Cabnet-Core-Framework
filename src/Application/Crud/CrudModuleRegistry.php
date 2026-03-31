<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

use InvalidArgumentException;
use Throwable;

final class CrudModuleRegistry
{
    /** @param array<string, array<string, mixed>> $modules */
    public function __construct(private array $modules)
    {
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return $this->modules;
    }

    /** @return array<string, mixed> */
    public function meta(string $key): array
    {
        if (!isset($this->modules[$key])) {
            throw new InvalidArgumentException("Unknown CRUD module [{$key}].");
        }

        return $this->modules[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->modules[$key]);
    }

    public function definitionClass(string $key): string
    {
        return $this->requiredString($key, 'definition_class');
    }

    public function controllerClass(string $key): string
    {
        return $this->requiredString($key, 'controller_class');
    }

    public function repositoryClass(string $key): string
    {
        return $this->requiredString($key, 'repository_class');
    }

    public function serviceClass(string $key): string
    {
        return $this->requiredString($key, 'service_class');
    }

    public function repositoryService(string $key): string
    {
        return $this->requiredString($key, 'repository_service');
    }

    public function crudService(string $key): string
    {
        return $this->requiredString($key, 'crud_service');
    }

    public function routePrefix(string $key): string
    {
        $prefix = '/' . trim($this->requiredString($key, 'route_prefix'), '/');
        return $prefix === '/' ? '/' : rtrim($prefix, '/');
    }

    public function adminRouteBase(string $key): string
    {
        return $this->requiredString($key, 'admin_route_base');
    }

    public function adminViewPath(string $key): string
    {
        return trim($this->requiredString($key, 'admin_view_path'), '/');
    }

    public function label(string $key): string
    {
        return $this->requiredString($key, 'label');
    }

    public function singularLabel(string $key): string
    {
        $meta = $this->meta($key);
        $value = $meta['singular_label'] ?? null;

        if (is_string($value) && $value !== '') {
            return $value;
        }

        $label = $this->label($key);
        return str_ends_with($label, 's') ? substr($label, 0, -1) : $label;
    }

    /** @return array<int, string> */
    public function adminMiddleware(string $key): array
    {
        $meta = $this->meta($key);
        $middleware = $meta['admin_middleware'] ?? ['session', 'admin.auth'];

        if (!is_array($middleware) || $middleware === []) {
            return ['session', 'admin.auth'];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $value): ?string => is_string($value) && $value !== '' ? $value : null,
            $middleware
        )));
    }

    /** @return array<string, array<int, string>> */
    public function permissions(string $key): array
    {
        $meta = $this->meta($key);
        $configured = $meta['permissions'] ?? null;
        $accessRoles = $this->rolesFromMeta($meta['access_roles'] ?? null);
        $fallback = $accessRoles !== [] ? $accessRoles : ['admin'];

        $permissions = [];
        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            $roles = [];
            if (is_array($configured) && array_key_exists($action, $configured)) {
                $roles = $this->rolesFromMeta($configured[$action]);
            }

            $permissions[$action] = $roles !== [] ? $roles : $fallback;
        }

        return $permissions;
    }

    /** @return array<int, string> */
    public function actionRoles(string $key, string $action): array
    {
        $permissions = $this->permissions($key);
        return $permissions[$action] ?? ['admin'];
    }

    public function allows(string $key, string $action, ?string $role): bool
    {
        $user = $role !== null && $role !== '' ? ['role' => $role] : null;
        return $this->allowsForUser($key, $action, $user);
    }

    /** @param array<string, mixed> $context */
    public function allowsForUser(string $key, string $action, mixed $user = null, array $context = []): bool
    {
        $policy = $this->policy($key);
        if ($policy instanceof CrudModulePolicy) {
            try {
                $decision = $policy->allows($key, $action, $user, $this->meta($key), $this->definition($key), $context);
            } catch (Throwable $e) {
                throw new InvalidArgumentException(
                    sprintf('CRUD policy evaluation failed for module [%s] action [%s]: %s', $key, $action, $e->getMessage()),
                    0,
                    $e
                );
            }

            if (is_bool($decision)) {
                return $decision;
            }
        }

        $roles = $this->actionRoles($key, $action);
        if (in_array('*', $roles, true)) {
            return true;
        }

        $role = $this->roleFromUser($user);
        if ($role === null || $role === '') {
            return false;
        }

        return in_array($role, $roles, true);
    }


    public function policyClass(string $key): ?string
    {
        $meta = $this->meta($key);
        $value = $meta['policy_class'] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @return array<string, array<string, mixed>> */
    public function filters(string $key): array
    {
        $meta = $this->meta($key);
        $configured = $meta['filters'] ?? [];
        if (!is_array($configured) || $configured === []) {
            return [];
        }

        $definition = $this->definition($key);
        $filters = [];

        foreach ($configured as $filterKey => $filterMeta) {
            if (is_string($filterMeta)) {
                $field = $filterMeta;
                $filterMeta = [];
            } else {
                $field = (string)((is_array($filterMeta) ? ($filterMeta['field'] ?? $filterKey) : $filterKey));
            }

            if (!$definition->hasField($field)) {
                throw new InvalidArgumentException("CRUD module [{$key}] declares filter [{$filterKey}] for unknown field [{$field}].");
            }

            $metaArray = is_array($filterMeta) ? $filterMeta : [];
            $filters[(string)$filterKey] = $definition->listFilter($field, $metaArray);
        }

        return $filters;
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function filterPayload(string $key, array $input): array
    {
        return $this->definition($key)->filterPayload($input, $this->filters($key));
    }

    public function definition(string $key): CrudEntityDefinition
    {
        $class = $this->definitionClass($key);

        if (!class_exists($class)) {
            throw new InvalidArgumentException("CRUD definition class [{$class}] was not found for module [{$key}].");
        }

        if (!method_exists($class, 'make')) {
            throw new InvalidArgumentException("CRUD definition class [{$class}] must expose a static make() method.");
        }

        $definition = $class::make();

        if (!$definition instanceof CrudEntityDefinition) {
            throw new InvalidArgumentException("CRUD definition class [{$class}] must return a CrudEntityDefinition instance.");
        }

        return $definition;
    }

    private function requiredString(string $key, string $metaKey): string
    {
        $meta = $this->meta($key);
        $value = $meta[$metaKey] ?? null;

        if (!is_string($value) || $value === '') {
            throw new InvalidArgumentException("CRUD module [{$key}] is missing required metadata [{$metaKey}].");
        }

        return $value;
    }


    private function roleFromUser(mixed $user): ?string
    {
        if (is_string($user) && $user !== '') {
            return $user;
        }

        if (!is_array($user)) {
            return null;
        }

        $role = $user['role'] ?? null;
        return is_string($role) && $role !== '' ? $role : null;
    }

    private function policy(string $key): ?CrudModulePolicy
    {
        $meta = $this->meta($key);
        $candidate = $meta['policy'] ?? ($meta['policy_class'] ?? null);

        if ($candidate === null) {
            return null;
        }

        if (is_string($candidate) && $candidate !== '') {
            if (!class_exists($candidate)) {
                throw new InvalidArgumentException("CRUD policy class [{$candidate}] was not found for module [{$key}].");
            }

            $candidate = new $candidate();
        }

        if (!$candidate instanceof CrudModulePolicy) {
            throw new InvalidArgumentException("CRUD policy for module [{$key}] must implement " . CrudModulePolicy::class . '.');
        }

        return $candidate;
    }

    /** @return array<int, string> */
    private function rolesFromMeta(mixed $roles): array
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
