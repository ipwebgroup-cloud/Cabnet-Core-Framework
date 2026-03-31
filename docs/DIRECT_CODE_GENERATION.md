# DIRECT_CODE_GENERATION.md

## Purpose

This phase upgrades the scaffold generator so it emits actual starter PHP code and SQL schema files from a JSON blueprint.

## Generated outputs

The default generator now writes src-first framework files:

- CRUD entity definition class
- repository class
- CRUD service class
- admin controller class
- wrapper CRUD views
- route snippet file
- service registration snippet file
- schema SQL file

## Safety model

Generated files still go to a separate output folder for review first.

## Example usage

```bash
php scripts/generate-crud-pack.php app/Generators/Stubs/product-blueprint.json
```

## Legacy mode

For older forks only:

```bash
php scripts/generate-crud-pack.php app/Generators/Stubs/product-blueprint.json generated/legacy_output --legacy
```

## What still remains manual

- route insertion into `bootstrap/routes.php`
- service registration insertion into `bootstrap/services.php`
- sidebar navigation updates
- final review before merge


## v2.8 update
Generated src CRUD definitions should return `Cabnet\Application\Crud\CrudEntityDefinition`, not the legacy global model.
