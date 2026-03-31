# V3_8_GENERATOR_METADATA_PARITY_CLEANUP.md

## Summary

v3.8 closes a practical scaffold gap: the runtime already understood richer module metadata, but the src-first CRUD generator still hardcoded several values and left follow-up edits behind after generation.

## What changed

### `src/Generators/CrudScaffoldWriter.php`

The src-first CRUD generator now preserves or derives more module metadata directly from the blueprint:

- `access_roles`
- `permissions`
- `admin_middleware`
- `show_in_admin_menu`
- explicit top-level `filters`
- field-level filter shortcuts via `filter`, `filterable`, and `list_filter`

It also keeps filter derivation conservative:

- upload fields do not auto-generate filters
- translatable fields do not auto-generate filters
- select filters remain `select` only when usable options exist
- explicit top-level filters override or complement derived field shortcuts

### `tests/Smoke/FrameworkSmokeTest.php`

Smoke coverage now verifies that generated scaffolds preserve:

- policy metadata
- module permission metadata
- admin middleware metadata
- admin menu visibility metadata
- explicit filter metadata
- derived filter metadata from field shortcuts

## Why this phase matters

This improves patch-to-patch consistency between:

- what `config/modules.php` can drive at runtime
- what the generator can preserve during new module authoring

That means less manual editing after each scaffold run and fewer chances for docs/code drift.

## Suggested blueprint patterns

### Explicit module metadata

```json
{
  "access_roles": ["admin", "editor"],
  "permissions": {
    "view": ["admin", "editor"],
    "create": ["admin"],
    "edit": ["admin"],
    "delete": ["admin"]
  },
  "admin_middleware": ["session", "admin.auth"],
  "show_in_admin_menu": true
}
```

### Explicit list filters

```json
{
  "filters": {
    "status": {
      "field": "status",
      "type": "select",
      "placeholder": "All statuses"
    }
  }
}
```

### Field shortcut filters

```json
{
  "fields": {
    "status": {
      "type": "select",
      "options": {
        "draft": "Draft",
        "published": "Published"
      },
      "filterable": true
    },
    "title": {
      "type": "text",
      "filter": {
        "placeholder": "Search title"
      }
    }
  }
}
```
