<?php
declare(strict_types=1);

use Cabnet\Http\Request as RuntimeRequest;
use Cabnet\Http\Response as RuntimeResponse;
use Cabnet\Http\ResponseEmitter;
use Cabnet\Http\ResponseResolver;
use Cabnet\Middleware\MiddlewareExecutor;
use Cabnet\Routing\RouteDispatcher;
use Cabnet\Routing\Router as RuntimeRouter;
use Cabnet\Security\Csrf as RuntimeCsrf;
use Cabnet\Session\Flash as RuntimeFlash;
use Cabnet\Session\Session as RuntimeSession;
use Cabnet\Support\UrlGenerator;
use Cabnet\Support\ViewState as RuntimeViewState;
use Cabnet\View\Renderer as ViewRenderer;

final class App
{
    private array $config;
    private array $services;
    private array $routes;
    private string $context;
    private RuntimeRequest $request;
    private RuntimeResponse $response;
    private RuntimeRouter $router;
    private array $serviceCache = [];
    private MiddlewareExecutor $middlewareExecutor;
    private RouteDispatcher $routeDispatcher;
    private ResponseResolver $responseResolver;
    private ResponseEmitter $responseEmitter;

    public function __construct(array $config, array $services, array $routes, string $context = 'public')
    {
        $this->config = $config;
        $this->services = $services;
        $this->routes = $routes;
        $this->context = $context;

        date_default_timezone_set($this->config('app.timezone', 'UTC'));

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->routes[$this->context] ?? []);
        $this->middlewareExecutor = new MiddlewareExecutor((array)$this->config('middleware.aliases', []));
        $this->routeDispatcher = new RouteDispatcher();
        $this->responseResolver = new ResponseResolver();
        $this->responseEmitter = new ResponseEmitter();
    }

    public function run(): void
    {
        $resolved = $this->router->match($this->request->method(), $this->request()->path());

        if ($resolved === null) {
            $this->responseEmitter->emit(
                $this->response->html(
                    '<h1>404 Not Found</h1><p>No route matched: ' . htmlspecialchars($this->request()->path(), ENT_QUOTES, 'UTF-8') . '</p>',
                    404
                )
            );
            return;
        }

        $globalMiddlewareResult = $this->runMiddlewareStack($this->middleware());
        if ($globalMiddlewareResult !== null) {
            $this->responseEmitter->emit($globalMiddlewareResult);
            return;
        }

        $routeMiddlewareResult = $this->runMiddlewareNames($resolved->route->middleware);
        if ($routeMiddlewareResult !== null) {
            $this->responseEmitter->emit($routeMiddlewareResult);
            return;
        }

        $result = $this->routeDispatcher->dispatch($resolved->route->handler, $this, $resolved->params);
        $this->responseEmitter->emit($this->responseResolver->resolve($result, $this->response));
    }

    private function runMiddlewareStack(array $stack): ?RuntimeResponse
    {
        foreach ($stack as $middleware) {
            if (!is_object($middleware) || !method_exists($middleware, 'handle')) {
                continue;
            }

            $result = $middleware->handle($this);
            if ($result instanceof RuntimeResponse || $result instanceof \Response) {
                return $result;
            }
        }

        return null;
    }

    private function runMiddlewareNames(array $names): ?RuntimeResponse
    {
        if ($names === []) {
            return null;
        }

        return $this->middlewareExecutor->run($names, $this);
    }

    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function service(string $name): mixed
    {
        if (array_key_exists($name, $this->serviceCache)) {
            return $this->serviceCache[$name];
        }

        $service = $this->services[$name] ?? null;

        if (is_callable($service)) {
            $service = $service($this);
        }

        $this->serviceCache[$name] = $service;
        return $service;
    }

    public function renderer(): ViewRenderer
    {
        $renderer = $this->service('renderer');
        if (!$renderer instanceof ViewRenderer) {
            throw new RuntimeException('Renderer service must implement Cabnet\\View\\Renderer.');
        }
        return $renderer;
    }

    public function session(): RuntimeSession
    {
        $session = $this->service('session');
        if (!$session instanceof RuntimeSession) {
            throw new RuntimeException('Session service must return Cabnet\\Session\\Session.');
        }
        return $session;
    }

    public function flash(): RuntimeFlash
    {
        $flash = $this->service('flash');
        if (!$flash instanceof RuntimeFlash) {
            throw new RuntimeException('Flash service must return Cabnet\\Session\\Flash.');
        }
        return $flash;
    }

    public function auth(): AuthManager
    {
        $auth = $this->service('auth');
        if (!$auth instanceof AuthManager) {
            throw new RuntimeException('Auth service must return AuthManager.');
        }
        return $auth;
    }

    public function csrf(): RuntimeCsrf
    {
        $csrf = $this->service('csrf');
        if (!$csrf instanceof RuntimeCsrf) {
            throw new RuntimeException('CSRF service must return Cabnet\\Security\\Csrf.');
        }
        return $csrf;
    }

    public function validator(): Validator
    {
        $validator = $this->service('validator');
        if (!$validator instanceof Validator) {
            throw new RuntimeException('Validator service must return Validator.');
        }
        return $validator;
    }

    public function viewState(): RuntimeViewState
    {
        $viewState = $this->service('viewState');
        if (!$viewState instanceof RuntimeViewState) {
            throw new RuntimeException('viewState service must return Cabnet\\Support\\ViewState.');
        }
        return $viewState;
    }

    public function url(): UrlGenerator
    {
        $url = $this->service('url');
        if (!$url instanceof UrlGenerator) {
            throw new RuntimeException('url service must return Cabnet\\Support\\UrlGenerator.');
        }
        return $url;
    }

    public function middleware(): array
    {
        $stack = $this->service('middleware');
        return is_array($stack) ? $stack : [];
    }

    public function request(): RuntimeRequest
    {
        return $this->request;
    }

    public function response(): RuntimeResponse
    {
        return $this->response;
    }

    public function context(): string
    {
        return $this->context;
    }

    public function routeParams(): array
    {
        return $this->router->params();
    }

    public function namedRoutes(): array
    {
        return $this->router->namedRoutes();
    }
}
