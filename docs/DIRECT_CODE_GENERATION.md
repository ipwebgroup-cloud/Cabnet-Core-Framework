# DIRECT_CODE_GENERATION.md

## Current src-first generator direction

Generated CRUD packs now aim for:

- `src/Application/Crud/Definitions/*`
- `src/Infrastructure/Repositories/*`
- `src/Application/Services/*`
- `src/Application/Controllers/Admin/*`
- `app/Views/php/admin/*` until view packaging convergence is completed

## v3.0 update

Generated field metadata now includes validation and form-rendering hints, and generated CRUD services extend `DefinitionCrudService` instead of repeating validation arrays inline.
