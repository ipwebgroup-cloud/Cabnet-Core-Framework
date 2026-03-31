# V4_0_RUNTIME_DEPENDENCY_INJECTION_BRIDGE.md

## Summary

v4.0 adds a lightweight runtime dependency-injection bridge without replacing the transitional legacy `App` container.

The goal is simple:

- keep the current runtime stable
- stop hard-coding `new Controller()` and `new Middleware()` as the only construction path
- allow src-owned controller and middleware classes to request useful runtime dependencies through their constructors
- preserve direct-instantiation fallback while the framework remains hybrid

## What changed

### New runtime resolver

- `src/Bootstrap/DependencyResolver.php`

This resolver can instantiate classes by reflection and inject:

- the transitional `App` bridge
- registered runtime services resolved by class or interface
- simple instantiable classes that can be recursively resolved

### Typed service bindings

- `src/Bootstrap/ServiceRegistry.php`
- `bootstrap/services.php`
- `app/Core/App.php`

The service map now exposes typed service bindings so the runtime can resolve common services by their class or interface.

Examples include:

- `Cabnet\Application\Crud\CrudModuleRegistry`
- `Cabnet\Support\UrlGenerator`
- `Cabnet\View\Renderer`
- `Cabnet\Security\Csrf`
- `Cabnet\Session\Session`
- `Cabnet\Infrastructure\Auth\DbUserProvider`

### Dispatch bridge

- `src/Routing/RouteDispatcher.php`
- `src/Middleware/MiddlewareExecutor.php`

Both now try constructor-aware resolution first and still fall back to direct instantiation for compatibility.

## Why this phase was next

The framework already had stronger metadata, generator parity, and blueprint examples.

The main remaining runtime gap was that controller and middleware construction still depended on direct `new` calls, which blocked safe constructor injection and kept runtime ownership more legacy-bound than the rest of the framework direction.

This phase improves runtime extensibility without forcing a full container rewrite.

## Supported constructor patterns

### Controller example

```php
final class ProductController extends BaseCrudController
{
    public function __construct(
        private \Cabnet\Application\Crud\CrudModuleRegistry $registry,
        private \Cabnet\Support\UrlGenerator $url,
        private object $app,
    ) {
    }
}
```

### Middleware example

```php
final class AuditMiddleware
{
    public function __construct(
        private \Cabnet\Application\Crud\CrudModuleRegistry $registry
    ) {
    }

    public function handle(\App $app): ?\Response
    {
        return null;
    }
}
```

### Recursive helper example

```php
final class ModuleContextHelper
{
    public function __construct(
        private \Cabnet\Application\Crud\CrudModuleRegistry $registry
    ) {
    }
}
```

If a controller or middleware depends on `ModuleContextHelper`, the resolver can build it recursively.

## Safe boundaries

This is intentionally a bridge, not a full DI container.

Do not assume it currently supports:

- arbitrary scalar or array constructor arguments
- complicated interface graphs without registered service bindings
- lifecycle scopes beyond the existing legacy app service cache
- advanced autowiring features or compile-time container behavior

## Validation added

Smoke coverage now verifies:

- `App::make()` can constructor-inject registered services
- route dispatch uses constructor-aware controller construction
- middleware execution uses constructor-aware middleware construction
- fallback compatibility remains intact because existing runtime paths still pass the unchanged smoke suite

## Recommended next steps

- relation filter option hydration
- blueprint schema validation
- service registry formalization
