# ADD_NEW_ENTITY.md

## Goal

This guide shows the standard way to add a new CRUD entity to Cabnet Core quickly.

## Preferred path

Use the `src/` path for all new framework-facing entities. Use the legacy `app/` path only for older forks that still require compatibility shims.

## 1. Decide the entity basics

Example:

- entity key: `products`
- singular label: `Product`
- plural label: `Products`
- table: `products`

## 2. Generate the naming blueprint

Use the helper script:

```bash
php scripts/generate-entity.php products Product Products products
```

This will output suggested class, route, and service names for the src-first architecture.

## 3. Create the definition class

Create:

- `src/Application/Crud/Definitions/ProductEntityDefinition.php`

It should return a `Cabnet\Application\Crud\CrudEntityDefinition` with:
- fields
- list columns
- searchable columns
- default order

## 4. Create the repository

Create:

- `src/Infrastructure/Repositories/ProductRepository.php`

It should extend `BaseRepository` and provide:
- `table()`
- `create()`
- `updateById()`

## 5. Create the service

Create:

- `src/Application/Services/ProductCrudService.php`

It should provide:
- `paginate()`
- `find()`
- `create()`
- `update()`
- `delete()`

## 6. Create the controller

Create:

- `src/Application/Controllers/Admin/ProductController.php`

It should extend `BaseCrudController`.

## 7. Create the views

Create thin wrappers under the active PHP view layer:

- `app/Views/php/admin/products/index.php`
- `app/Views/php/admin/products/create.php`
- `app/Views/php/admin/products/edit.php`

## 8. Register the module

Add the new entity to `config/modules.php`.

That metadata now drives:

- admin CRUD routes
- repository service registration
- CRUD service registration
- admin menu link registration

Use keys like:
- `definition_class`
- `controller_class`
- `repository_class`
- `service_class`
- `repository_service`
- `crud_service`
- `admin_route_base`
- `admin_view_path`

## 9. Add database schema

Create a schema file under:

- `database/schema/products.sql`

## 10. Recommended minimum checklist

- definition created
- repository created
- service created
- controller created
- module entry added to `config/modules.php`
- views added
- schema added
- admin menu exposure confirmed
- tested create/edit/delete

## Legacy compatibility note

If an older fork still depends on the legacy layer, keep `app/` classes as thin shims over the src implementation rather than duplicating the logic again.


## v2.8 note
After generating a new src CRUD pack, add or update the module entry in `config/modules.php` so the built-in CRUD module registry can expose the new entity metadata.


## v2.9 note
With v2.9, module metadata is no longer just documentation. It is now the preferred runtime integration seam for admin CRUD onboarding.
