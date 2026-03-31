# CRUD_CONVENTIONS.md

## Canonical CRUD ownership

- entity definition: `src/Application/Crud/Definitions/*EntityDefinition.php`
- controller: `src/Application/Controllers/Admin/*Controller.php`
- service: `src/Application/Services/*CrudService.php`
- repository: `src/Infrastructure/Repositories/*Repository.php`
- admin views: `src/Presentation/Views/php/admin/<module>/` or `src/Presentation/Views/twig/admin/<module>/`
- module registry metadata: `config/modules.php`
- built-in blueprint examples: `blueprints/examples/*.json`

## Module metadata keys

Common keys:

- `enabled`
- `label`
- `singular_label`
- `route_prefix`
- `table`
- `definition_class`
- `controller_class`
- `repository_class`
- `service_class`
- `repository_service`
- `crud_service`
- `admin_route_base`
- `admin_view_path`
- `admin_middleware`
- `access_roles`
- `permissions`
- `filters`
- `policy_class` (optional)
- `show_in_admin_menu`
- `generator_target`

## Permission model

The default permission model is still role-array based:

```php
'permissions' => [
    'view' => ['admin', 'editor'],
    'create' => ['admin'],
    'edit' => ['admin'],
    'delete' => ['admin'],
],
```

If a module wants the same fallback role list across all actions, it can also declare:

```php
'access_roles' => ['admin', 'editor'],
```

## Filter model

List filters live in module metadata and are resolved against the entity definition:

```php
'filters' => [
    'status' => [
        'field' => 'status',
        'type' => 'select',
        'placeholder' => 'All statuses',
    ],
],
```

The generator can also derive filters from field metadata shortcuts:

- `filterable: true`
- `list_filter: true`
- `filter: { ... }`

Relation-backed select filters can now hydrate their options directly from field relation metadata, so modules do not need custom list-filter glue when the field already declares a valid relation:

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

## Optional policy hooks

For modules that need richer access logic, add a policy class:

```php
'policy_class' => \App\Policies\ServicePolicy::class,
```

Policy classes must implement `Cabnet\Application\Crud\CrudModulePolicy`.

- return `true` to allow the action
- return `false` to deny the action
- return `null` to fall back to the configured role arrays

## Runtime construction rule

Route-dispatched controllers and named middleware aliases may now use lightweight constructor injection.

Safe constructor targets:

- registered runtime services resolved by class or interface
- the transitional `App` bridge
- simple instantiable src-owned helper classes with recursively resolvable dependencies

Avoid using constructor injection for heavy runtime graphs or configuration arrays that are not backed by registered services yet.

## Blueprint authoring rule

Prefer starting from a built-in example whenever the module is broadly similar to one of the shipped scaffold packs. This reduces drift between docs, generator behavior, and actual framework runtime expectations.

## Blueprint validation rule

Custom scaffold blueprints should pass the built-in validator before generation. At minimum, define: `entity_key`, `singular_label`, `plural_label`, `table`, and a non-empty `fields` object with valid field `type` metadata.
