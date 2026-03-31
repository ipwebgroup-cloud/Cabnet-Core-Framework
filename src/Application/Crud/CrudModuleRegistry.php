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
        $meta = $this->meta($key);
        $class = $meta['definition_class'] ?? null;

        if (!is_string($class) || $class === '') {
            throw new InvalidArgumentException("CRUD module [{$key}] is missing a definition class.");
        }

        return $class;
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
}
