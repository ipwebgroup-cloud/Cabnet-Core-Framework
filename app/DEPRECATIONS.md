# DEPRECATIONS.md

## Transitional compatibility layer

The `app/` layer still exists for compatibility, but canonical ownership has moved to `src/` for:

- controllers
- services
- repositories
- runtime helpers
- CRUD definitions

## v3.0 note

Validation arrays hardcoded inside individual CRUD services are no longer the preferred pattern. Prefer canonical field metadata plus `DefinitionCrudService`.
