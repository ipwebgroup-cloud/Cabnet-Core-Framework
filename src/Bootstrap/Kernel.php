<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use Cabnet\Core\ErrorHandler;
use Cabnet\Core\Logging\FileLogger;
use Cabnet\Core\Logging\LoggerInterface;
use Cabnet\Http\Request;
use Cabnet\Http\Response;
use Cabnet\Http\ResponseEmitter;
use Cabnet\Http\ResponseResolver;
use Cabnet\Middleware\MiddlewareExecutor;
use Cabnet\Routing\RouteDispatcher;
use Cabnet\Routing\Router;

final class Kernel
{
    private object $legacyApp;
    private Router $router;
    private LoggerInterface $logger;
    private ErrorHandler $errorHandler;
    private MiddlewareExecutor $middlewareExecutor;
    private RouteDispatcher $routeDispatcher;
    private ResponseResolver $responseResolver;
    private ResponseEmitter $responseEmitter;

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

        $this->middlewareExecutor = new MiddlewareExecutor((array)($this->config['middleware']['aliases'] ?? []));
        $this->routeDispatcher = new RouteDispatcher();
        $this->responseResolver = new ResponseResolver();
        $this->responseEmitter = new ResponseEmitter();
    }

    public function boot(): void
    {
        $this->errorHandler->register();
    }

    public function run(): void
    {
        $request = new Request();
        $response = new Response();

        $resolved = $this->router->match($request->method(), $request->path());

        if ($resolved === null) {
            $this->responseEmitter->emit(
                $response->html('<h1>404 Not Found</h1><p>No route matched.</p>', 404)
            );
            return;
        }

        $middlewareResponse = $this->middlewareExecutor->run($resolved->route->middleware ?? [], $this->legacyApp);
        if ($middlewareResponse !== null) {
            $this->responseEmitter->emit($middlewareResponse);
            return;
        }

        $result = $this->routeDispatcher->dispatch($resolved->route->handler, $this->legacyApp, $resolved->params);
        $this->responseEmitter->emit($this->responseResolver->resolve($result, $response));
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
