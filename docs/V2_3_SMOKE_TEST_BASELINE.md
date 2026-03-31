# Cabnet Core Framework v2.3 — Smoke Test Baseline

## Objective

Add a lightweight native smoke-test layer that validates the most fragile framework paths without forcing PHPUnit or a larger test dependency decision yet.

## Why this phase now

After v2.2 auth hardening, the framework needed a fast regression check for:

- bootstrap creation
- kernel route registration
- route parameter matching
- login CSRF rejection
- starter-credential lockout
- logout flow
- admin guest guard behavior

This gives the framework a repeatable verification path before deeper rendering and runtime convergence work continues.

## Files added

- `tests/bootstrap.php`
- `tests/Support/SmokeAssert.php`
- `tests/Support/ResponseInspector.php`
- `tests/Support/TestEnvironment.php`
- `tests/Smoke/FrameworkSmokeTest.php`
- `scripts/run-smoke-tests.php`

## Composer command

```bash
composer smoke-test
```

## Direct command

```bash
php scripts/run-smoke-tests.php
```

## Current smoke coverage

1. `bootstrap_app('admin')` builds correctly
2. `bootstrap_kernel('admin')` exposes stable named routes
3. route parameter extraction works for CRUD edit paths
4. login form renders the active CSRF token
5. invalid login CSRF requests fail closed
6. starter credentials remain disabled by default
7. logout requires a valid POST flow and clears auth state
8. admin middleware redirects guests to the login route

## Design notes

This is intentionally not a full unit-test framework. It is a small native harness built for shared-hosting-safe projects and incremental modernization.

The runner uses reflection only inside the smoke layer to inspect `Response` internals without changing runtime response behavior just for testing.

## Next strongest move

Use this baseline to support **rendering convergence** and **legacy runtime reduction** with faster regression detection after each incremental change.
