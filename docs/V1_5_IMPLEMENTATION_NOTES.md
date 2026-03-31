# V1_5_IMPLEMENTATION_NOTES.md

## Important note

Version 1.5 intentionally migrates only a small number of high-value controllers first.

This avoids a risky all-at-once move.

## Migrated routes

The following now use `src/` controllers:

- public home
- admin dashboard
- health endpoints

## Migrated service behavior

The framework clock service and admin menu service now have namespaced `src/` implementations, even though the legacy service container still bridges them.

## Current hybrid reality

At this point:
- runtime entry is `src/Bootstrap/Kernel`
- some controllers are `src/`
- many admin CRUD flows are still in `app/`

That is expected and acceptable for this phase.
