# SAFE_INTEGRATION_MODE.md

## Purpose

This phase adds a safe integration-helper layer on top of the CRUD code generator.

## Current usage

The integration helper now accepts:

- a direct JSON blueprint path
- a built-in example alias like `example:media-assets`
- `--list-examples` to show the built-in example catalog

## Example usage

```bash
php scripts/generate-integration-patches.php --list-examples
php scripts/generate-integration-patches.php example:media-assets
```

## Safety model

The system still does **not** auto-modify framework files in place.

Instead, it writes patch-ready text files for review first.
