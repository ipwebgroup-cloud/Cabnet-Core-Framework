# Cabnet Core Framework Bundle v2.6.0

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
- `FRAMEWORK_MASTER_CONTEXT.md`
- `ARCHITECTURE.md`
- `ROADMAP.md`

## Recommended next action
Use this as the baseline repository/project starter for your future apps.

## v2.1 Runtime bootstrap convergence additions
- preferred kernel/bootstrap path active through front controllers
- compatibility factory for the legacy app container
- config loader convergence around `src/Bootstrap`

## v2.2 Auth hardening additions
- safer admin credential defaults
- DB-backed auth service path retained as the primary direction
- smoke coverage for login/logout and CSRF handling

## v2.3 Smoke baseline additions
- native smoke runner under `scripts/run-smoke-tests.php`
- reusable response inspection helpers for framework checks

## v2.4 Rendering convergence additions
- canonical renderer ownership in `src/View`
- legacy renderer wrappers preserved for compatibility

## v2.5 Legacy runtime reduction additions
- canonical controller base classes moved into `src/Application/Controllers`
- `AppRuntime` reduced to a compatibility facade over `src/Bootstrap/Kernel`

## v2.6 Service/repository convergence additions
- canonical service base in `src/Application/Services/BaseService.php`
- canonical repository base in `src/Infrastructure/Repositories/BaseRepository.php`
- legacy service/repository classes narrowed into compatibility shims
- src-first generator output aligned to src-owned base inheritance
