# Cabnet Core Framework v4.0.0

Cabnet Core v4.0 adds a lightweight runtime dependency-injection bridge so controllers and middleware can constructor-inject registered services and simple src-owned classes without replacing the transitional legacy `App` container.

## What this patch adds

- constructor-aware runtime resolution for route-dispatched controllers
- constructor-aware runtime resolution for named middleware aliases
- typed service bindings for the legacy app service map so common runtime services can be resolved by class/interface
- a lightweight dependency resolver for src-owned class construction with legacy-app injection support
- smoke coverage for app-level construction, controller dispatch, and middleware execution through the new bridge

## Start here

- `docs/V4_0_RUNTIME_DEPENDENCY_INJECTION_BRIDGE.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
