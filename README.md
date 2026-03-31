# Cabnet Core Framework v4.1.0

Cabnet Core v4.1 adds relation-filter option hydration so relation-backed list filters can behave like first-class select controls without per-project glue, while keeping scaffold output aligned with that runtime behavior.

## What this patch adds

- shared relation-option hydration for both CRUD forms and module list filters
- runtime hydration of relation-backed filter options through `CrudModuleRegistry`
- a reusable relation option hydrator registered in the runtime service map
- src-first generator behavior that preserves relation-backed derived filters as `select` controls even when options are expected to come from the database
- smoke coverage for registry-side relation filter hydration and scaffold parity

## Start here

- `docs/V4_1_RELATION_FILTER_OPTION_HYDRATION.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
