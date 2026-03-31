# SAFE_INTEGRATION_MODE.md

## Purpose

This phase adds a safe integration-helper layer on top of the CRUD code generator.

## New components

- `IntegrationPatcher`
- `scripts/generate-integration-patches.php`

## What it generates

From the same blueprint JSON, the patch helper now emits reviewable snippets for:

- `bootstrap/routes.php`
- `bootstrap/services.php`
- admin sidebar navigation
- additional `require_once` lines for bootstrap loading

## Why this matters

The framework can now produce both:
- the new module code pack
- the integration snippets needed to wire it into the framework

This reduces manual copy-paste and lowers the chance of missing steps.

## Safety model

The system still does **not** auto-modify framework files in place.

Instead, it writes patch-ready text files for review first.

## Example usage

```bash
php scripts/generate-integration-patches.php app/Generators/Stubs/product-blueprint.json
```
