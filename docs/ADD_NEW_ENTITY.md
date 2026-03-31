# ADD_NEW_ENTITY.md

## Goal

This guide shows the standard way to add a new CRUD entity to Cabnet Core quickly.

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

This will output suggested class and route names.

## 3. Create the definition class

Create:

- `app/Crud/Definitions/ProductEntityDefinition.php`

It should return a `CrudEntityDefinition` with:
- fields
- list columns
- searchable columns
- default order

## 4. Create the repository

Create:

- `app/Repositories/ProductRepository.php`

It should extend `BaseRepository` and provide:
- `table()`
- `create()`
- `updateById()`

## 5. Create the service

Create:

- `app/Services/ProductCrudService.php`

It should provide:
- `paginate()`
- `find()`
- `create()`
- `update()`
- `delete()`

## 6. Create the controller

Create:

- `app/Controllers/Admin/ProductController.php`

It should extend `BaseCrudController`.

## 7. Create the views

Create thin wrappers:

- `app/Views/php/admin/products/index.php`
- `app/Views/php/admin/products/create.php`
- `app/Views/php/admin/products/edit.php`

Each can point to the generic CRUD partials.

## 8. Add routes

In `bootstrap/routes.php`, add:

- index
- create
- store
- edit
- update
- delete

Use route names:
- `admin.products.index`
- `admin.products.create`
- `admin.products.store`
- `admin.products.edit`
- `admin.products.update`
- `admin.products.delete`

## 9. Register services

In `bootstrap/services.php`, register:

- `productRepository`
- `productCrud`

## 10. Add database schema

Create a schema file under:

- `database/schema/products.sql`

## 11. Add admin navigation

Add the module to the admin sidebar.

## 12. Recommended minimum checklist

- definition created
- repository created
- service created
- controller created
- routes added
- services registered
- views added
- schema added
- sidebar link added
- tested create/edit/delete

## Summary

The framework is now structured so that adding a new entity is mostly repetition of a known pattern, not architectural invention.
