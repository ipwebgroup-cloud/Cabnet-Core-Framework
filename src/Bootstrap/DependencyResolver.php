<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use ReflectionIntersectionType;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;

final class DependencyResolver
{
    /** @var array<string, bool> */
    private array $resolving = [];

    public function __construct(private object $app)
    {
    }

    public function resolve(string $class): object
    {
        if (method_exists($this->app, 'serviceByType')) {
            $service = $this->app->serviceByType($class);
            if (is_object($service)) {
                return $service;
            }
        }

        if (isset($this->resolving[$class])) {
            throw new RuntimeException('Circular dependency detected while resolving ' . $class . '.');
        }

        if (!class_exists($class)) {
            throw new RuntimeException('Cannot resolve missing class ' . $class . '.');
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException('Cannot instantiate non-instantiable class ' . $class . '.');
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return $reflection->newInstance();
        }

        $this->resolving[$class] = true;

        try {
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

        if ($this->isAppParameter($parameter, $type)) {
            return $this->app;
        }

        foreach ($this->candidateTypes($type) as $candidate) {
            if (method_exists($this->app, 'serviceByType')) {
                $service = $this->app->serviceByType($candidate);
                if ($service !== null) {
                    return $service;
                }
            }

            if (class_exists($candidate)) {
                return $this->resolve($candidate);
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new RuntimeException(sprintf(
            'Unable to resolve constructor parameter $%s for %s.',
            $parameter->getName(),
            $parameter->getDeclaringClass()?->getName() ?? 'unknown class'
        ));
    }

    private function isAppParameter(ReflectionParameter $parameter, ?ReflectionType $type): bool
    {
        if ($parameter->getName() !== 'app') {
            foreach ($this->candidateTypes($type) as $candidate) {
                if ($candidate !== 'object' && is_a($this->app, $candidate)) {
                    return true;
                }
            }

            return false;
        }

        if ($type === null) {
            return true;
        }

        foreach ($this->candidateTypes($type) as $candidate) {
            if ($candidate === 'object' || is_a($this->app, $candidate)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<int, string> */
    private function candidateTypes(?ReflectionType $type): array
    {
        if ($type === null || $type instanceof ReflectionIntersectionType) {
            return [];
        }

        if ($type instanceof ReflectionNamedType) {
            return [$type->getName()];
        }

        if ($type instanceof ReflectionUnionType) {
            $names = [];
            foreach ($type->getTypes() as $namedType) {
                if ($namedType instanceof ReflectionNamedType) {
                    $names[] = $namedType->getName();
                }
            }

            return $names;
        }

        return [];
    }
}
