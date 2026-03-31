# FILE_WRITING_GENERATOR.md

## Purpose

This phase upgrades the generator from a naming helper into a file-writing scaffold generator.

## New components

- `ScaffoldWriter`
- `scripts/generate-crud-pack.php`
- example blueprint: `product-blueprint.json`

## What it generates

From one blueprint JSON file, the generator now creates starter scaffold artifacts such as:

- route snippet file
- service registration snippet file
- generated module notes

## Suggested usage

```bash
php scripts/generate-crud-pack.php app/Generators/Stubs/product-blueprint.json
```

## Output

By default, files are written under:

```text
/generated/scaffold_output/
```

## Safety note

This phase writes generated files into a separate output directory, not directly into the live framework tree.
