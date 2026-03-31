# V2.1 Runtime Bootstrap Convergence

## Objective

Reduce bootstrap fragility without breaking the current hybrid runtime.

## What changed

- replaced the long manual `require_once` chain in `bootstrap/app.php`
- added `bootstrap/autoload.php` as the framework-owned fallback autoloader
- added `bootstrap/legacy_classmap.php` for legacy global class resolution
- corrected the `PublicSite\\HomeController` PSR-4 path mismatch
- introduced `src/View/ViewEngineFactory` so renderer selection now begins from `src/`
- fixed `src/AppRuntime` dynamic property usage for PHP 8.2+

## Why this phase matters

Before this phase, adding or moving classes required hand-editing `bootstrap/app.php`, which made the framework easy to drift and hard to modernize safely.

After this phase, the framework still supports the transitional `app/` layer, but the runtime boot path is thinner and more maintainable.

## Compatibility notes

- `bootstrap_app()` and `bootstrap_kernel()` remain unchanged
- legacy global classes in `app/` still work through the fallback classmap
- views still live under `app/Views/*` for now
- Composer autoload remains supported when present

## Recommended next phase

- auth hardening and starter-credential removal
- POST-based logout + CSRF protection for logout
- first real smoke tests around bootstrap, auth, and route resolution
