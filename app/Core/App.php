<?php
declare(strict_types=1);

use Cabnet\View\Renderer as ViewRenderer;

final class App
{
    private array $config;
    private array $services;
    private array $routes;
    private string $context;
    private Request $request;
    private Response $response;
    private Router $router;
    private array $serviceCache = [];

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
    }

    public function run(): void
    {
        $resolved = $this->router->match($this->request->method(), $this->request()->path());

        if ($resolved === null) {
            $this->response->html(
                '<h1>404 Not Found</h1><p>No route matched: ' . htmlspecialchars($this->request()->path(), ENT_QUOTES, 'UTF-8') . '</p>',
                404
            )->send();
            return;
        }

        foreach ($this->middleware() as $middleware) {
            $result = $middleware->handle($this);
            if ($result instanceof Response) {
                $result->send();
                return;
            }
        }

        $routeMiddleware = $resolved->route['middleware'] ?? [];
        foreach ($this->resolveRouteMiddleware($routeMiddleware) as $middleware) {
            $result = $middleware->handle($this);
            if ($result instanceof Response) {
                $result->send();
                return;
            }
        }

        $handler = $resolved->route['handler'] ?? null;
        $params = $resolved->params ?? [];

        if (is_callable($handler)) {
            $result = $handler($this, $params);
        } elseif (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            $controller = new $class();
            $result = $controller->{$action}($this, $params);
        } else {
            $result = null;
        }

        if ($result instanceof Response) {
            $result->send();
            return;
        }

        if (is_string($result)) {
            $this->response->html($result)->send();
            return;
        }

        $this->response->json([
            'status' => 'error',
            'message' => 'Invalid route response.',
        ], 500)->send();
    }

    protected function resolveRouteMiddleware(array $names): array
    {
        $aliases = $this->config('middleware.aliases', []);
        $resolved = [];

        foreach ($names as $name) {
            $class = $aliases[$name] ?? null;
            if (is_string($class) && class_exists($class)) {
                $resolved[] = new $class();
            }
        }

        return $resolved;
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

    public function session(): Session
    {
        $session = $this->service('session');
        if (!$session instanceof Session) {
            throw new RuntimeException('Session service must return Session.');
        }
        return $session;
    }

    public function flash(): Flash
    {
        $flash = $this->service('flash');
        if (!$flash instanceof Flash) {
            throw new RuntimeException('Flash service must return Flash.');
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

    public function csrf(): Csrf
    {
        $csrf = $this->service('csrf');
        if (!$csrf instanceof Csrf) {
            throw new RuntimeException('CSRF service must return Csrf.');
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

    public function viewState(): ViewState
    {
        $viewState = $this->service('viewState');
        if (!$viewState instanceof ViewState) {
            throw new RuntimeException('viewState service must return ViewState.');
        }
        return $viewState;
    }

    public function url(): UrlService
    {
        $url = $this->service('url');
        if (!$url instanceof UrlService) {
            throw new RuntimeException('url service must return UrlService.');
        }
        return $url;
    }

    public function middleware(): array
    {
        $stack = $this->service('middleware');
        return is_array($stack) ? $stack : [];
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
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
