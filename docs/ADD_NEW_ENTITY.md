# ADD_NEW_ENTITY.md

## Src-first CRUD workflow

1. start from a built-in example under `blueprints/examples/` when possible
2. create or adapt the blueprint JSON for your module
3. generate the scaffold pack in a separate output directory
4. create or review the entity definition in `src/Application/Crud/Definitions`
5. review the repository in `src/Infrastructure/Repositories`
6. review the CRUD service in `src/Application/Services`
7. review the admin controller in `src/Application/Controllers/Admin`
8. add the module metadata block to `config/modules.php`
9. add or generate admin views under `src/Presentation/Views/php/admin/<module>/` and optionally Twig views under `src/Presentation/Views/twig/admin/<module>/`
10. run smoke tests after integrating the module

## Built-in example workflow

List the current examples:

```bash
php scripts/generate-crud-pack.php --list-examples
```

Generate from a built-in example:

```bash
php scripts/generate-crud-pack.php example:localized-services generated/localized_services
php scripts/generate-integration-patches.php example:localized-services generated/localized_services_patches
```

## Optional policy extension

If the module needs authorization logic that cannot be expressed cleanly with role arrays alone, add a `policy_class`:

```php
'policy_class' => \App\Policies\ProductPolicy::class,
```

The policy should implement `Cabnet\Application\Crud\CrudModulePolicy` and return `true`, `false`, or `null`.

## Constructor injection reminder

Runtime-dispatched controllers and named middleware aliases can now constructor-inject:

- the transitional `App` bridge via an `App`, `object`, or `$app` constructor parameter
- registered runtime services such as `Cabnet\Application\Crud\CrudModuleRegistry`, `Cabnet\Support\UrlGenerator`, `Cabnet\View\Renderer`, `Cabnet\Security\Csrf`, and similar typed services
- simple instantiable src-owned classes that can themselves be resolved recursively

Keep constructor injection light and runtime-safe. Prefer services and small helper objects over deep graph construction.

## Relation-filter reminder

Relation-backed filters no longer need per-project manual option hydration. A field like this is now enough:

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

The framework can hydrate relation options for both forms and list filters from the same metadata.

## Generator reminder

The src-first generator can now preserve:

- field metadata
- upload metadata
- relation metadata
- translatable metadata
- requested PHP and/or Twig view targets
- optional `policy_class` metadata in generated module config stubs
- optional `access_roles`, `permissions`, `admin_middleware`, and `show_in_admin_menu` module metadata
- explicit top-level `filters` metadata or field-level filter shortcuts via `filter`, `filterable`, or `list_filter`
- relation-backed derived filters that stay `select` controls without requiring inline `options`
- built-in example selection via `example:<name>`

## Validation reminder

The generator entry scripts now validate blueprint structure before writing output. If a blueprint is malformed, fix the reported schema errors before integrating any generated files.

## Runtime note

As of v4.3, typed runtime services are registered through `src/Bootstrap/ServiceRegistry.php`. When a generated module or custom controller needs constructor-injected framework services, prefer formal typed bindings there instead of adding ad-hoc alias wiring directly in `bootstrap/services.php`.
