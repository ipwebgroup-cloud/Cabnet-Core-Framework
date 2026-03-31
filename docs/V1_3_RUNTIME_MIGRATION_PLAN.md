# V1_3_RUNTIME_MIGRATION_PLAN.md

## Objective

Cabnet Core v1.3 begins actual runtime migration from the legacy `app/` layer toward the namespaced `src/` layer.

## Included in this pack

- namespaced HTTP request/response classes
- namespaced routing runtime
- namespaced middleware executor
- route-specific middleware execution in the active legacy runtime
- stub-template generator groundwork extended
- updated composer autoload direction retained

## Why this matters

This is the first phase where runtime modernization is no longer only structural metadata. Parts of the request lifecycle now move toward the future core.

## Recommended next steps for v1.4

- migrate active logger and error handler usage fully to `src/`
- migrate URL/routing helpers to `src/`
- migrate generator implementation from legacy `app/Generators` to `src/Generators`
- add CLI user creation/seeding tool
- convert the active app bootstrap to prefer namespaced runtime first
