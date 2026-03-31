# DEPRECATIONS.md

## Transitional compatibility layer

The `app/` layer still exists for compatibility, but canonical ownership has moved to `src/` for:

- controllers
- services
- repositories
- runtime helpers
- CRUD definitions
- shared PHP layouts and partials
- built-in PHP admin/public view packages

## v3.0 note

Validation arrays hardcoded inside individual CRUD services are no longer the preferred pattern. Prefer canonical field metadata plus `DefinitionCrudService`.

## v3.2 note

`app/Views/php` is now primarily a compatibility shim layer for built-in framework presentation files. New shared shells, partials, and CRUD-facing PHP views should prefer `src/Presentation/Views/php`.
