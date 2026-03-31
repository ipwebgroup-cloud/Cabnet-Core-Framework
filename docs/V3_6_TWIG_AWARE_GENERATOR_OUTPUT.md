# V3_6_TWIG_AWARE_GENERATOR_OUTPUT.md

## Objective

Bring scaffold generation into better parity with the already-migrated rendering layer by allowing src-first CRUD module generation to emit Twig admin stubs as a first-class output.

## Why this phase

Before v3.6:

- the framework already had optional Twig rendering
- canonical shared Twig CRUD templates already existed under `src/Presentation/Views/twig/admin/crud`
- built-in modules like `services` already had Twig stubs
- but the src-first CRUD generator still emitted only PHP admin view stubs

That meant generated modules lagged behind the real rendering architecture.

## What changed

- `src/Generators/CrudScaffoldWriter.php`
  - now understands `view_engine` and `view_engines`
  - now normalizes renderer targets safely
  - now emits Twig admin stubs when requested
  - continues to emit PHP admin stubs by default for compatibility
- `scripts/generate-crud-pack.php`
  - now accepts `--twig`
  - now accepts `--twig-only`
  - now forwards renderer intent into the blueprint before generation
- `tests/Smoke/FrameworkSmokeTest.php`
  - now verifies Twig scaffold generation behavior

## Supported blueprint examples

Twig only:

```json
{
  "entity_key": "products",
  "singular_label": "Product",
  "plural_label": "Products",
  "table": "products",
  "view_engine": "twig"
}
```

PHP + Twig:

```json
{
  "entity_key": "products",
  "singular_label": "Product",
  "plural_label": "Products",
  "table": "products",
  "view_engines": ["php", "twig"]
}
```

## Compatibility notes

- PHP generation is still the default when no renderer target is supplied.
- Existing bootstrap and `config/app.php` defaults remain unchanged.
- Existing projects using PHP-only rendering do not need to change.
- Twig output generation does not force Twig at runtime; it only reduces manual follow-up for projects that enable it.

## Next strongest move after v3.6

- lightweight policy hooks
- generator/runtime parity cleanup
- clearer blueprint authoring examples
