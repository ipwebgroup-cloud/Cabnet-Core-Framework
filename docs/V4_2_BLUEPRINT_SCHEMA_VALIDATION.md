# V4_2_BLUEPRINT_SCHEMA_VALIDATION.md

## Summary

v4.2 adds early blueprint schema validation for CRUD scaffold and integration-patch generation, while also reconciling earlier documented framework capabilities that were not fully present in the executable tree.

## What changed

- added `Cabnet\Generators\BlueprintValidator` for early validation of scaffold blueprints
- added `Cabnet\Generators\BlueprintLibrary` so generator scripts can list and resolve built-in examples via `example:<name>`
- added built-in example packs for `content-pages` and `media-assets`
- restored the lightweight `CrudModulePolicy` interface used by module registry and menu visibility hooks
- restored constructor-aware runtime resolution through `App::make()` and `Cabnet\Bootstrap\DependencyResolver`
- updated route dispatch and middleware execution to use constructor-aware resolution with compatibility fallback
- updated generator scripts so malformed blueprints fail early with clearer messages

## Validation behavior

The validator currently checks:

- required top-level strings: `entity_key`, `singular_label`, `plural_label`, `table`
- presence of a non-empty `fields` object
- field metadata shape and required `type`
- `translatable` fields must declare non-empty `locales`
- relation metadata must include `table`, `value_column`, and `label_column`
- upload size metadata must be integer-like when provided
- `view_engine` / `view_engines` must be limited to `php` and/or `twig`
- `filters` and `permissions` must be objects when provided

## CLI examples

```bash
php scripts/generate-crud-pack.php --list-examples
php scripts/generate-crud-pack.php example:localized-services generated/localized_services
php scripts/generate-integration-patches.php example:media-assets generated/media_asset_patches
```

If a blueprint is malformed, the script now fails early with a validation message before writing partial output.

## Compatibility notes

- existing valid blueprints remain compatible
- direct JSON file paths still work
- built-in examples are additive
- constructor-aware runtime resolution still falls back to direct instantiation if needed
