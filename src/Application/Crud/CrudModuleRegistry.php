<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

use InvalidArgumentException;

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
}
