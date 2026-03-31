# V2_0_SRC_FIRST_GENERATOR_ALIGNMENT.md

## Objective

Align the framework's scaffolding tools with the actual v2.0 direction.

## Why this phase exists

After v2.0, the runtime already prefers `src/Bootstrap`, `src/Application`, and `src/Infrastructure`, but the CRUD scaffolding scripts still generated new module code into `app/`.

That meant every new scaffold deepened legacy debt even though the framework governance said new major work should default to `src/`.

## What changed

- Added `src`-first generator classes under `src/Generators`
- Updated CLI scripts to default to `src`-first generation
- Kept `--legacy` mode so older integration flows still work
- Kept admin PHP views under `app/Views/php/admin` until rendering ownership is migrated fully
- Updated Composer autoloading to map the full `Cabnet\\` namespace to `src/`

## Current generator policy

### Default target
- `src/Application`
- `src/Infrastructure`
- `app/Views/php/admin` (temporary rendering bridge)

### Legacy target
Use `--legacy` only when maintaining older integration examples or older projects that still depend on `app/` class generation.

## Follow-up recommendation

The next cleanup after this phase should be runtime/rendering convergence:

- reduce manual `require_once` sprawl in `bootstrap/app.php`
- move rendering ownership toward `src/View`
- formalize a lightweight testing baseline for router, middleware, and generator smoke tests
