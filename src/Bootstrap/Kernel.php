<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use Cabnet\Core\ErrorHandler;
use Cabnet\Core\Logging\FileLogger;
use Cabnet\Core\Logging\LoggerInterface;
use Cabnet\Http\Response;
use Cabnet\Routing\Router;
use RuntimeException;

final class Kernel
{
    private object $legacyApp;
    private Router $router;
    private LoggerInterface $logger;
    private ErrorHandler $errorHandler;

    public function __construct(
        private array $config,
        private array $routes,
        object $legacyApp
    ) {
        $this->legacyApp = $legacyApp;
        $this->router = new Router($this->routes[$this->legacyApp->context()] ?? []);

        $logging = $this->config['logging']['channels']['file'] ?? [];
        $this->logger = new FileLogger(
            $logging['path'] ?? (BASE_PATH . '/storage/logs/app.log'),
            $logging['level'] ?? 'error'
        );

        $this->errorHandler = new ErrorHandler(
            $this->logger,
            (bool)($this->config['app']['debug'] ?? false)
        );
    }

    public function boot(): void
    {
        $this->errorHandler->register();
    }

    public function run(): void
    {
        $request = new \Cabnet\Http\Request();
        $response = new Response();

        $resolved = $this->router->match($request->method(), $request->path());

        if ($resolved === null) {
            $response->html('<h1>404 Not Found</h1><p>No route matched.</p>', 404)->send();
            return;
        }

        $routeMiddleware = $resolved->route->middleware ?? [];
        foreach ($this->resolveRouteMiddleware($routeMiddleware) as $middleware) {
            $result = $middleware->handle($this->legacyApp);
            if ($result instanceof \Response) {
                $result->send();
                return;
            }
            if ($result instanceof Response) {
                $result->send();
                return;
            }
        }

        $handler = $resolved->route->handler;
        $result = null;

        if (is_callable($handler)) {
            $result = $handler($this->legacyApp, $resolved->params);
        } elseif (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            if (class_exists($class) && method_exists($class, $action)) {
                $controller = new $class();
                $result = $controller->{$action}($this->legacyApp, $resolved->params);
            }
        }

        if ($result instanceof \Response) {
            $result->send();
            return;
        }

        if ($result instanceof Response) {
            $result->send();
            return;
        }

        if (is_string($result)) {
            $response->html($result)->send();
            return;
        }

        if ($result === null) {
            $response->json(['status' => 'ok'])->send();
            return;
        }

        throw new RuntimeException('Invalid route result from Kernel.');
    }

    private function resolveRouteMiddleware(array $names): array
    {
        $aliases = $this->config['middleware']['aliases'] ?? [];
        $resolved = [];

        foreach ($names as $name) {
            $class = $aliases[$name] ?? null;
            if (is_string($class) && class_exists($class)) {
                $resolved[] = new $class();
            }
        }

        return $resolved;
    }

    public function namedRoutes(): array
    {
        return $this->router->namedRoutes();
    }

    public function logger(): LoggerInterface
    {
        return $this->logger;
    }
}
