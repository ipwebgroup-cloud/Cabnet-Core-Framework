# ENTITY_DEFINITIONS.md

## Purpose

Entity definitions make CRUD behavior more reusable by moving display and metadata concerns into a dedicated definition class.

## Structure

Each definition should describe:

- key
- label
- table
- fields
- list columns
- searchable columns
- default order

## Example

```php
return new CrudEntityDefinition(
    key: 'services',
    label: 'Services',
    table: 'services',
    fields: [...],
    listColumns: ['id', 'title', 'slug', 'status'],
    searchable: ['title', 'slug'],
    defaultOrder: 'id DESC'
);
```

## Benefits

- keeps controller code slimmer
- supports reusable list rendering
- supports future auto-generated forms
- supports future generic CRUD controllers

## Current implementation

- `ServiceEntityDefinition`
- `BaseCrudController`
- paginated searchable list flow for `services`
