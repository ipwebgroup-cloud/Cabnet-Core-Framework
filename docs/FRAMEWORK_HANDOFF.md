# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.6.0  
**Type:** Consolidated reusable PHP MVC-lite starter framework  
**Status:** Stable transitional baseline

## Included Framework Capabilities

- public/admin/API front controllers
- bootstrap loader
- config layer
- PDO database layer
- PHP renderer
- optional Twig renderer
- session and flash layer
- admin authentication starter
- middleware pipeline
- CSRF support
- validation layer
- repository/service structure
- CRUD entity definition system
- generic CRUD list and form partials
- named-route support
- URL helper
- entity/module generator foundation
- scaffold code generation
- documentation pack
- smoke-test baseline for bootstrap/runtime/generator regressions
- src-owned HTTP/runtime helpers with legacy shim compatibility
- module-registry-driven CRUD service, route, and admin menu bootstrapping
- definition-driven CRUD validation rules
- metadata-driven CRUD form attributes
- metadata-driven upload fields
- metadata-driven relation selects
- metadata-driven translatable fields
- Twig-aware CRUD generator output for src-owned admin scaffolds

## Key Files to Know

- `config/modules.php`
- `config/storage.php`
- `config/app.php`
- `src/Application/Crud/CrudModuleRegistry.php`
- `src/Application/Crud/CrudEntityDefinition.php`
- `src/Application/Controllers/Admin/BaseCrudController.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Support/UploadManager.php`
- `src/Http/Request.php`
- `src/Presentation/Views/php/admin/crud/form_fields.php`
- `src/Presentation/Views/twig/admin/crud/form_fields.twig`
- `src/Generators/CrudScaffoldWriter.php`
- `scripts/generate-crud-pack.php`

## Current delivery model

- patch packages may include only changed files
- read `FRAMEWORK_STATUS.json`, `PATCH_MANIFEST.txt`, and this handoff file first when resuming from a patch-only zip
- `HANDOFF_PROMPT.txt` is continuity-critical and should be kept aligned with the current phase and delivery model
