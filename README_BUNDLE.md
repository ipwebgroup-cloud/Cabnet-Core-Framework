# Cabnet Core Framework Bundle v3.0.0

This patch continues the consolidated framework bundle with the next convergence phase.

## Included in this patch
- definition-driven CRUD validation base
- metadata-driven CRUD form rendering
- updated service/repository contracts
- updated src CRUD generator output
- release documentation and handoff notes
- expanded smoke coverage

## v3.0 highlights
- canonical CRUD field definitions now derive validator rule sets automatically
- canonical CRUD services can inherit shared create/update validation behavior from `DefinitionCrudService`
- canonical admin CRUD forms now render attributes like `required`, `maxlength`, placeholders, and help text from the definition metadata
- generated src CRUD packs now emit field metadata suitable for both validation and form rendering
