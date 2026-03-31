# V2_8_CRUD_METADATA_CONVERGENCE.md

## Goal
Move canonical CRUD entity-definition ownership into `src/` and start converging module metadata toward a single config-backed registry.

## What changed
- added `src/Application/Crud/CrudEntityDefinition.php`
- added `src/Application/Crud/CrudModuleRegistry.php`
- updated `src/Application/Crud/Definitions/ServiceEntityDefinition.php` to return the canonical src CRUD definition model
- updated `src/Application/Controllers/Admin/BaseCrudController.php` and `ServiceController.php` to type against the canonical src CRUD definition model
- updated `src/Generators/CrudScaffoldWriter.php` and `src/Generators/Templates/controller.stub` to generate src CRUD definitions/controllers against the canonical src model
- converted `app/Crud/CrudEntityDefinition.php` and `app/Crud/Definitions/ServiceEntityDefinition.php` into compatibility aliases
- expanded `config/modules.php` to hold CRUD metadata for the built-in `services` module
- registered `crudModuleRegistry` in `bootstrap/services.php`
- expanded smoke coverage for canonical CRUD definition ownership and module registry behavior

## Why this phase matters
This removes one of the last core places where `src/` code still depended on a legacy global model. It also creates a safer path for future generator and runtime alignment by putting CRUD module metadata in config rather than scattering it across docs and assumptions.

## Compatibility
Legacy global `CrudEntityDefinition` and `ServiceEntityDefinition` references remain valid through aliases, so existing legacy views and wrappers continue to function.

## Recommended next move
Adopt the new module registry more broadly inside generator integration patching and future generic CRUD helpers, while preserving stable routes and compatibility wrappers.
