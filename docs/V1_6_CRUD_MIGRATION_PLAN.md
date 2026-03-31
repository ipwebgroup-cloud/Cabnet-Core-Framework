# V1_6_CRUD_MIGRATION_PLAN.md

## Objective

Cabnet Core v1.6 migrates the first full CRUD module end-to-end into the `src/` application and infrastructure layers.

## Migrated module

- `services`

## Included in this pack

- `src/Application/Controllers/Admin/ServiceController`
- `src/Application/Services/ServiceCrudService`
- `src/Application/Crud/Definitions/ServiceEntityDefinition`
- `src/Infrastructure/Repositories/ServiceRepository`

## Why this matters

This is the first complete proof that the new architecture can serve a real admin CRUD module, not just shell routes or shared services.

## Recommended v1.7 focus

- migrate auth controller into `src/`
- migrate more repositories into `src/Infrastructure`
- begin deleting or deprecating redundant legacy counterparts
- add integration tests for migrated CRUD routes
