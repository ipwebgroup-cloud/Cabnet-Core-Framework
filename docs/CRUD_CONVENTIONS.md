# CRUD_CONVENTIONS.md

## Canonical direction

- define module metadata in `config/modules.php`
- define field metadata in `src/Application/Crud/Definitions/*EntityDefinition.php`
- let services inherit shared validation flow from `DefinitionCrudService`
- let admin CRUD partials read display attributes from the same field metadata
- let module metadata declare per-action access roles and list-filter controls

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

## Module metadata conventions

Supported runtime keys now include:

- `permissions` for `view`, `create`, `edit`, and `delete`
- `filters` for list-only query controls
- `admin_middleware`
- `show_in_admin_menu`

## Validation direction

Prefer metadata-driven validation through `CrudEntityDefinition::validationRules()` before adding custom per-service rule arrays.

## Form direction

Prefer metadata-driven form rendering through the shared CRUD partials before creating custom hand-written field markup.

## List behavior direction

Prefer registry-driven list filtering through `config/modules.php` before hand-writing module-specific search/filter forms.

- Canonical CRUD presentation files now live under `src/Presentation/Views/php/admin`, with `app/Views/php` retained as a compatibility fallback.
