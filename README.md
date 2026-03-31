# Cabnet Core Framework v4.2.0

Cabnet Core v4.2 adds early blueprint schema validation for scaffold generation and integration patch generation, while reconciling earlier documented policy-hook, built-in-example, and constructor-aware runtime features so the executable tree now matches the continuity files.

## What this patch adds

- early blueprint schema validation through `Cabnet\Generators\BlueprintValidator`
- executable built-in blueprint library support through `Cabnet\Generators\BlueprintLibrary`
- shipped example packs for `content-pages`, `media-assets`, and `localized-services`
- restored `CrudModulePolicy` contract used by module registry and admin-menu visibility hooks
- restored constructor-aware `App::make()` resolution for route-dispatched controllers and named middleware aliases
- smoke coverage for malformed blueprints and reconciled runtime features

## Start here

- `docs/V4_2_BLUEPRINT_SCHEMA_VALIDATION.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
