# V1_6_IMPLEMENTATION_NOTES.md

## Important note

Version 1.6 is a milestone because one real module is now fully migrated.

## Migrated pieces

The following `services` module pieces now live in `src/`:

- controller
- CRUD service
- entity definition
- repository

## What remains legacy

- generic CRUD base classes still live in `app/`
- validation, rendering, and some support helpers still live in `app/`
- views still remain under the legacy view tree

This is acceptable for this phase because the runtime and primary module behavior are now being proven in the new structure.
