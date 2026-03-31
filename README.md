# Cabnet Core Framework v3.8.0

Cabnet Core v3.8 cleans up generator/runtime metadata parity so src-first CRUD scaffolds preserve more of the module metadata that the runtime already understands.

## What this patch adds

- src-first CRUD generation now preserves optional `access_roles`, `permissions`, `admin_middleware`, and `show_in_admin_menu` metadata in generated module config stubs
- generated module config can now preserve explicit `filters` metadata from the blueprint instead of forcing manual post-generation edits
- field metadata can now declare lightweight filter shortcuts using `filter`, `filterable`, or `list_filter`
- derived filters skip upload/translatable fields and keep select typing only when usable options exist
- generated implementation notes now summarize which list filters were derived or preserved for the scaffold

## Start here

- `docs/V3_8_GENERATOR_METADATA_PARITY_CLEANUP.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
