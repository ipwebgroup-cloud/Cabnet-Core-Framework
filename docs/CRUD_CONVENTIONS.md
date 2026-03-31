# CRUD_CONVENTIONS.md

## Canonical direction

- define module metadata in `config/modules.php`
- define field metadata in `src/Application/Crud/Definitions/*EntityDefinition.php`
- let services inherit shared validation flow from `DefinitionCrudService`
- let admin CRUD partials read display attributes from the same field metadata

## Field metadata conventions

Supported keys include:

- `type`
- `label`
- `required`
- `default`
- `min`
- `max`
- `placeholder`
- `help`
- `rows`
- `options`
- `slug`
- `rules` for explicit overrides when inference is not enough

## Validation direction

Prefer metadata-driven validation through `CrudEntityDefinition::validationRules()` before adding custom per-service rule arrays.

## Form direction

Prefer metadata-driven form rendering through the shared CRUD partials before creating custom hand-written field markup.
