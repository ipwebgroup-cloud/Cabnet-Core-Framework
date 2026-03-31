# V4.3 — Service Registry Formalization

## Summary

This phase formalizes runtime service registration around `Cabnet\Bootstrap\ServiceRegistry` instead of relying only on the ad-hoc `__service_types` array embedded in `bootstrap/services.php`.

The framework remains compatibility-first:

- the legacy service array still exists
- `__service_types` is still emitted for fallback compatibility
- runtime `App::serviceByType()` now prefers the formal service registry when available
- constructor-aware controller and middleware resolution continue to work without changing route or middleware definitions

## Why this phase

`v4.0` introduced constructor-aware runtime resolution, but type lookups were still effectively coupled to an implementation detail inside the raw service map. That made typed service extension harder than it needed to be and kept service registration logic scattered between `bootstrap/services.php`, `App`, and `ServiceRegistry`.

`v4.3` turns `ServiceRegistry` into the canonical source for:

- service definitions
- type aliases
- service-name lookup by type
- lightweight runtime extension points for future phases

## Key changes

### `src/Bootstrap/ServiceRegistry.php`

Now owns:

- full default service definitions through `definitions()` / `register()`
- formal type alias declarations through `serviceTypeBindings()`
- normalized service-name lookup through `serviceNameForType()`
- first-class registration of `serviceRegistry`, `clock`, and `adminMenuService`

### `bootstrap/services.php`

Now delegates service-map construction to `ServiceRegistry` and remains responsible only for applying CRUD module bootstrap overlays.

### `app/Core/App.php`

`App::serviceByType()` now:

1. checks if the requested type is the app bridge itself
2. prefers the formal `ServiceRegistry` lookup path
3. falls back to legacy `__service_types` alias resolution for compatibility

This allows typed runtime resolution to keep working even if older alias-array wiring is absent.

## New runtime implications

The following are now formal typed runtime services:

- `Cabnet\Bootstrap\ServiceRegistry`
- `Cabnet\Application\Services\ClockService`
- `Cabnet\Application\Services\AdminMenuService`
- previously exposed typed services such as `CrudModuleRegistry`, `Renderer`, `Csrf`, `Session`, `DbUserProvider`, etc.

## Compatibility

This phase is intentionally additive.

It does **not**:

- replace the legacy `App` container
- remove `__service_types`
- change route definitions
- change middleware aliases
- change CRUD module contracts

## Validation

Smoke coverage now verifies:

- formal service-registry type alias resolution
- `App::serviceByType()` still works when the legacy alias array is removed
- constructor-aware controller and middleware construction still pass through the formal registry path cleanly

## Recommended next move

The next strongest move is **controller/service autowiring refinement**, so more runtime constructions can rely on formal typed bindings without hand-maintained glue in transitional services.
