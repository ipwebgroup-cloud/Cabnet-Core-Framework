<?php
declare(strict_types=1);

final class Router extends \Cabnet\Routing\Router
{
    public function dispatch(string $method, string $path, App $app): mixed
    {
        $resolved = $this->match($method, $path);

        if ($resolved === null) {
            return $app->response()->html(
                '<h1>404 Not Found</h1><p>No route matched: ' . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . '</p>',
                404
            );
        }

        $dispatcher = new \Cabnet\Routing\RouteDispatcher();
        return $dispatcher->dispatch($resolved->route->handler, $app, $resolved->params);
    }
}
