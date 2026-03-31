# V2_0_ARCHITECTURE_STATUS.md

## Current status summary

Cabnet Core v2.0 is a hybrid framework with a preferred modern core.

## Preferred runtime path

The preferred entry path is:

- front controller
- `bootstrap/app.php`
- `bootstrap_kernel()`
- `src/Bootstrap/Kernel`
- route resolution
- middleware execution
- controller dispatch

## Preferred application path

The preferred location for new framework logic is:

- `src/Application`
- `src/Infrastructure`
- `src/Support`
- `src/View` (canonical renderer ownership formalized in v2.4)

## Legacy compatibility path

The `app/` layer still exists for:

- compatibility
- shared helpers not yet migrated
- transitional rendering support
- transitional CRUD/view compatibility

## Governance rule

New major framework features should **default to `src/`**, not `app/`.

## Already migrated or intended to live in `src/`

- kernel/bootstrap direction
- core logging/error handler examples
- route definition/runtime pieces
- public home controller
- admin dashboard controller
- admin auth controller
- API health controller
- services CRUD module
- admin auth service
- DB user provider
- admin menu support
- view state support
- view engine selection factory
- view support helpers
- canonical controller base classes

## Still primarily legacy or bridged

- some generic CRUD support pieces
- validation internals
- some rendering bridge behavior (view files and wrappers still transitional)
- some validation and persistence primitives
- some view partial ownership
- some support/service container conventions
- integration test expansion beyond the native smoke baseline
