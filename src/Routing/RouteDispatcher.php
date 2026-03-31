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
                $controller = new $class();
                return $controller->{$action}($app, $params);
            }
        }

        return null;
    }
}
