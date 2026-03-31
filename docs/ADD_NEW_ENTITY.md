# ADD_NEW_ENTITY.md

## Src-first CRUD workflow

1. create the entity definition in `src/Application/Crud/Definitions`
2. create the repository in `src/Infrastructure/Repositories`
3. create the CRUD service in `src/Application/Services`
4. create the admin controller in `src/Application/Controllers/Admin`
5. add the module metadata block to `config/modules.php`
6. add or generate admin views under `src/Presentation/Views/php/admin/<module>/` and optionally Twig views under `src/Presentation/Views/twig/admin/<module>/`
7. run smoke tests after integrating the module

## Optional policy extension

If the module needs authorization logic that cannot be expressed cleanly with role arrays alone, add a `policy_class`:

```php
'policy_class' => \App\Policies\ProductPolicy::class,
```

The policy should implement `Cabnet\Application\Crud\CrudModulePolicy` and return `true`, `false`, or `null`.

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

## Filter shortcut example

```json
{
  "fields": {
    "status": {
      "type": "select",
      "label": "Status",
      "options": {
        "draft": "Draft",
        "published": "Published"
      },
      "filterable": true
    },
    "title": {
      "type": "text",
      "label": "Title",
      "filter": {
        "placeholder": "Search title"
      }
    }
  }
}
```
