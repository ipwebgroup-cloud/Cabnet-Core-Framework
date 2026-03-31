# V4_1_RELATION_FILTER_OPTION_HYDRATION.md

## Phase summary

v4.1 closes the gap between relation-backed form fields and relation-backed list filters.

Before this phase:
- relation metadata could hydrate select options for CRUD forms through `DefinitionCrudService`
- module list filters were resolved separately through `CrudModuleRegistry`
- relation-backed filters could therefore render as empty selects unless projects added custom glue
- src-first scaffold output could also downgrade relation-backed filters to text when no inline `options` array was present

After this phase:
- relation-backed filter definitions can hydrate options from relation metadata at runtime
- src-first generator output preserves relation-backed filters as `select` controls even when options are expected to come from the database
- the new relation option hydrator is reusable from the runtime bridge service map

## Main runtime changes

### New helper

- `src/Application/Crud/RelationOptionsHydrator.php`

Responsibilities:
- hydrate entity-definition fields with relation-driven options
- hydrate module filter definitions with relation-driven options
- cache relation option sets per request to avoid repeated identical queries
- validate table/column identifiers before building SQL

### Registry integration

- `src/Application/Crud/CrudModuleRegistry.php`
- `bootstrap/services.php`

The module registry now receives database access through its service registration and hydrates relation-backed filters before returning them to admin list views.

### Service integration

- `src/Application/Services/DefinitionCrudService.php`

Form-definition hydration now uses the shared relation option hydrator instead of carrying its own duplicated relation-option query logic.

## Generator change

- `src/Generators/CrudScaffoldWriter.php`

Relation-backed fields that derive filters through `filter`, `filterable`, or `list_filter` now remain `select` filters even when no inline `options` array is provided. This better matches runtime behavior, where options may be hydrated from relation metadata.

## Practical effect

Modules can now define a relation-backed field like this:

```php
'category_id' => [
    'type' => 'select',
    'label' => 'Category',
    'filterable' => true,
    'relation' => [
        'table' => 'categories',
        'value_column' => 'id',
        'label_column' => 'name',
        'order_by' => 'name',
    ],
],
```

and get:
- relation options on forms
- relation options on list filters
- scaffolded filter metadata that stays aligned with the actual runtime

## Validation added

Smoke coverage now checks that:
- relation-backed module filters hydrate options from relation metadata
- relation-backed scaffold filters remain `select` controls without requiring inline `options`
