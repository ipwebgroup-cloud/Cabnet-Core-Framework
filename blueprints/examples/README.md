# Built-in Blueprint Examples

These JSON files are canonical scaffold examples for the src-first CRUD generator.

Use them directly with:

```bash
php scripts/generate-crud-pack.php example:localized-services
php scripts/generate-integration-patches.php example:content-pages
```

List all available examples:

```bash
php scripts/generate-crud-pack.php --list-examples
php scripts/generate-integration-patches.php --list-examples
```

Current examples:

- `content-pages` — simple editorial CRUD module
- `media-assets` — upload-driven CRUD module
- `localized-services` — richer blueprint with Twig, translatable fields, relation metadata, permissions, and policy hooks
