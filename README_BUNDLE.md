# Cabnet Core Framework Bundle v1.0.0

This is the consolidated framework bundle built from the phase-by-phase starter process.

## Included
- latest framework code
- docs pack
- generators
- integration patch helpers
- example generated artifacts
- handoff documentation

## Recommended entry docs
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/FRAMEWORK_MASTER_CONTEXT.md`
- `docs/ARCHITECTURE.md`
- `docs/ROADMAP.md`

## Recommended next action
Use this as the baseline repository/project starter for your future apps.


## v1.1 Hardening additions
- error handler and file logger
- framework/modules/admin-menu/logging config split
- DB-backed auth service path
- users schema
- migration runner
- FormRequest base
- Composer project file and .env example


## v1.2 Modernization additions
- PSR-4 autoload direction
- namespaced `src/` layer
- route middleware metadata
- stub-template generator path
- middleware config file


## v1.3 Runtime migration additions
- executable `src/` HTTP and routing runtime pieces
- route-specific middleware execution in active runtime
- deeper hybrid migration toward namespaced core


## v1.4 Core unification additions
- `src/Bootstrap/Kernel.php`
- preferred kernel-based front controllers
- centralized config loader
- legacy bridge factory
- CLI admin user creation helper


## v1.5 Service/controller migration additions
- active `src/Application` controllers for home, dashboard, and health
- namespaced clock and admin-menu services
- service registry bridge for incremental migration


## v1.6 CRUD migration additions
- first full migrated CRUD module in `src/`
- `services` controller/service/repository/definition now use the new layers
- stronger proof that the new architecture is viable for real admin modules


## v1.7 Legacy reduction additions
- auth moved into `src/`
- DB user provider moved into `src/Infrastructure`
- view state and admin menu support moved into `src/Support`
- deprecation guidance for the legacy layer
