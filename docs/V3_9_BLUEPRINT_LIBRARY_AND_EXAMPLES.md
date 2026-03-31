# V3_9_BLUEPRINT_LIBRARY_AND_EXAMPLES.md

## Summary

v3.9 turns blueprint authoring examples into a framework feature instead of leaving them as scattered JSON stubs and prose notes.

## What changed

### `src/Generators/BlueprintLibrary.php`

Adds a canonical resolver for built-in scaffold examples under `blueprints/examples/`.

It can now:

- list the built-in examples that ship with the framework
- resolve `example:<name>` aliases to canonical example JSON files
- load example metadata for generator and smoke-test use

### `scripts/generate-crud-pack.php`

The CRUD generator now supports:

- `--list-examples`
- built-in blueprint names via `example:<name>`
- path resolution through the blueprint library before generation
- reporting the blueprint source that was actually used

### `scripts/generate-integration-patches.php`

The integration patch helper now supports the same built-in example flow so scaffold and integration generation stay aligned.

### `blueprints/examples/*`

Canonical example packs now ship with the framework:

- `content-pages.json`
- `media-assets.json`
- `localized-services.json`

These are meant to be copied, adapted, or generated from directly.

### `tests/Smoke/FrameworkSmokeTest.php`

Smoke coverage now verifies:

- built-in example discovery
- built-in example path resolution
- generation from the richer `localized-services` example

## Why this phase matters

The framework had already become more metadata-driven, but blueprint authoring still depended too much on remembering the right JSON shape from earlier chats or old example files.

v3.9 reduces that ambiguity by shipping executable, current-state examples that reflect the real runtime and generator capabilities.

## Example usage

```bash
php scripts/generate-crud-pack.php --list-examples
php scripts/generate-crud-pack.php example:content-pages
php scripts/generate-crud-pack.php example:localized-services generated/localized_services --twig
php scripts/generate-integration-patches.php example:media-assets
```

## Current example coverage

### `content-pages`

Shows:

- simple text/textarea fields
- slug handling
- status filters
- role/permission metadata

### `media-assets`

Shows:

- file uploads
- image uploads
- upload directories and size limits
- explicit filter metadata for admin lists

### `localized-services`

Shows:

- PHP + Twig view targets
- translatable fields and locales
- relation metadata
- uploads/images
- permission metadata
- policy hook metadata
- custom admin middleware

## Suggested next move after v3.9

- runtime dependency-injection bridge
- relation-filter option hydration
- blueprint schema validation
