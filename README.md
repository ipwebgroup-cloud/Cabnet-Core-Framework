# Cabnet Core Framework v4.3.0

Cabnet Core v4.3 formalizes runtime service registration around `Cabnet\Bootstrap\ServiceRegistry`, so typed runtime resolution no longer depends only on the legacy `__service_types` alias array embedded in the bootstrap service map.

## What this patch adds

- `ServiceRegistry` now owns the default runtime service definitions
- formal type-to-service lookup through `ServiceRegistry::serviceNameForType()`
- first-class typed services for `serviceRegistry`, `clock`, and `adminMenuService`
- `App::serviceByType()` now prefers the formal registry path and falls back to legacy alias maps for compatibility
- smoke coverage proving typed runtime resolution still works even when the legacy alias array is absent

## Start here

- `docs/V4_3_SERVICE_REGISTRY_FORMALIZATION.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
