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

## Optional policy hooks

For modules that need richer access logic, add a policy class:

```php
'policy_class' => \App\Policies\ServicePolicy::class,
```

Policy classes must implement `Cabnet\Application\Crud\CrudModulePolicy`.

- return `true` to allow the action
- return `false` to deny the action
- return `null` to fall back to the configured role arrays

## Blueprint authoring rule

Prefer starting from a built-in example whenever the module is broadly similar to one of the shipped scaffold packs. This reduces drift between docs, generator behavior, and actual framework runtime expectations.
