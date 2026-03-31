# Cabnet Core Framework v3.9.0

Cabnet Core v3.9 adds a built-in blueprint library so scaffold authoring can start from verified framework examples instead of ad-hoc JSON memory.

## What this patch adds

- a canonical built-in blueprint library under `blueprints/examples/`
- example-aware generator commands via `example:<name>` and `--list-examples`
- built-in scaffold examples for simple content CRUD, upload/media CRUD, and richer localized service CRUD
- smoke coverage for example discovery, example resolution, and generation from a richer built-in blueprint

## Start here

- `docs/V3_9_BLUEPRINT_LIBRARY_AND_EXAMPLES.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
