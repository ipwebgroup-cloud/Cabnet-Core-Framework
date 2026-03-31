<?php
declare(strict_types=1);

namespace Cabnet\Middleware;

use Cabnet\Http\Response;

final class MiddlewareExecutor
{
    public function __construct(
        private array $aliases = []
    ) {
    }

    public function run(array $middlewareNames, object $app): ?Response
    {
        foreach ($middlewareNames as $name) {
            $class = $this->aliases[$name] ?? null;
            if (!is_string($class) || !class_exists($class)) {
                continue;
            }

            $instance = $this->makeInstance($class, $app);
            if (!method_exists($instance, 'handle')) {
                continue;
            }

            $result = $instance->handle($app);
            if ($result instanceof Response || $result instanceof \Response) {
                return $result;
            }
        }

        return null;
    }

    private function makeInstance(string $class, object $app): object
    {
        if (method_exists($app, 'make')) {
            try {
                return $app->make($class);
            } catch (\Throwable) {
                // Fall back to direct instantiation to preserve transitional compatibility.
            }
        }

        return new $class();
    }
}
