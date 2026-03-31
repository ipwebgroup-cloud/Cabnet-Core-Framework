<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class DependencyResolver
{
    /** @var array<string, bool> */
    private array $resolving = [];

    public function __construct(private object $app)
    {
    }

    public function make(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Cannot resolve [%s]; class does not exist.', $class));
        }

        if (isset($this->resolving[$class])) {
            throw new RuntimeException(sprintf('Circular dependency detected while resolving [%s].', $class));
        }

        $this->resolving[$class] = true;

        try {
            $reflection = new ReflectionClass($class);
            if (!$reflection->isInstantiable()) {
                throw new RuntimeException(sprintf('Cannot resolve [%s]; class is not instantiable.', $class));
            }

            $constructor = $reflection->getConstructor();
            if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                return $reflection->newInstance();
            }

            $arguments = [];
            foreach ($constructor->getParameters() as $parameter) {
                $arguments[] = $this->resolveParameter($parameter);
            }

            return $reflection->newInstanceArgs($arguments);
        } finally {
            unset($this->resolving[$class]);
        }
    }

    private function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $typeName = $type->getName();

            if ($this->isAppType($typeName)) {
                return $this->app;
            }

            if (method_exists($this->app, 'serviceByType')) {
                $service = $this->app->serviceByType($typeName);
                if ($service !== null) {
                    return $service;
                }
            }

            if (class_exists($typeName)) {
                return $this->make($typeName);
            }
        }

        if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
            if ($type->getName() === 'object' && $parameter->getName() === 'app') {
                return $this->app;
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new RuntimeException(sprintf(
            'Cannot resolve constructor parameter [$%s] for [%s].',
            $parameter->getName(),
            $parameter->getDeclaringClass()?->getName() ?? 'unknown'
        ));
    }

    private function isAppType(string $typeName): bool
    {
        return $typeName === 'App' || $typeName === '\\App' || is_a($this->app, $typeName, true);
    }
}
