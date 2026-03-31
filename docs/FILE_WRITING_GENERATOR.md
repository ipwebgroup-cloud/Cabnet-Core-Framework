# FILE_WRITING_GENERATOR.md

## Purpose

This phase upgraded the generator from a naming helper into a file-writing scaffold generator.

## Current usage

You can now generate from:

- a direct JSON path
- a built-in example alias like `example:content-pages`

## Suggested usage

```bash
php scripts/generate-crud-pack.php --list-examples
php scripts/generate-crud-pack.php example:content-pages
php scripts/generate-crud-pack.php blueprints/examples/localized-services.json generated/localized_services
```

## Output

By default, files are written under:

```text
/generated/scaffold_output/
```

## Safety note

The generator writes into a separate output directory, not directly into the live framework tree.
