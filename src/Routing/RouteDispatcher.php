<?php
declare(strict_types=1);

namespace Cabnet\Routing;

class RouteDispatcher
{
    public function dispatch(mixed $handler, object $app, array $params = []): mixed
    {
        if (is_callable($handler)) {
            return $handler($app, $params);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            if (class_exists($class) && method_exists($class, $action)) {
                $controller = $this->makeInstance($class, $app);
                return $controller->{$action}($app, $params);
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
