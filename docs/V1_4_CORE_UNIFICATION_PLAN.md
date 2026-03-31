# V1_4_CORE_UNIFICATION_PLAN.md

## Objective

Cabnet Core v1.4 makes the `src/` layer the preferred runtime path while keeping a legacy compatibility bridge.

## Included in this pack

- `Kernel` in `src/Bootstrap`
- `ConfigLoader`
- `LegacyAppFactory`
- front controllers now prefer `bootstrap_kernel()`
- CLI admin user creation helper
- continued hybrid fallback through the legacy app

## Why this matters

The framework no longer treats `src/` as only future scaffolding. It is now the preferred request entry path.

## What still remains hybrid

- controllers still live in `app/`
- most services and repositories still live in `app/`
- rendering still uses the legacy application services

## Recommended v1.5 focus

- migrate controllers/services incrementally into `src/`
- unify logger/error handler usage completely
- reduce duplicate runtime classes
- add tests for kernel-based routing and middleware
