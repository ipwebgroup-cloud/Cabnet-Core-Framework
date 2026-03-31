# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.9.0  
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
- lightweight module policy hooks for controller authorization and admin-menu visibility
- generator/runtime metadata parity for permissions, filters, middleware, menu visibility, and field-level filter shortcuts
- built-in blueprint library and example packs for safer scaffold authoring

## Key Files to Know

- `config/modules.php`
- `config/storage.php`
- `config/app.php`
- `src/Application/Crud/CrudModuleRegistry.php`
- `src/Application/Crud/CrudModulePolicy.php`
- `src/Application/Controllers/Admin/BaseCrudController.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Support/AdminMenu.php`
- `src/Support/UploadManager.php`
- `src/Http/Request.php`
- `src/Presentation/Views/php/admin/crud/form_fields.php`
- `src/Presentation/Views/twig/admin/crud/form_fields.twig`
- `src/Generators/CrudScaffoldWriter.php`
- `src/Generators/BlueprintLibrary.php`
- `scripts/generate-crud-pack.php`
- `scripts/generate-integration-patches.php`
- `blueprints/examples/`

## Current delivery model

- patch packages may include only changed files
- read `FRAMEWORK_STATUS.json`, `PATCH_MANIFEST.txt`, and this handoff file first when resuming from a patch-only zip
- `HANDOFF_PROMPT.txt` is continuity-critical and should be kept aligned with the current phase and delivery model

## Current phase notes

- role-array permissions remain the default safe authorization path
- modules may optionally declare a `policy_class` or in-memory `policy` object that implements `Cabnet\Application\Crud\CrudModulePolicy`
- controller authorization and admin-menu visibility now share the same policy-aware module access path
- src-first CRUD generation can preserve optional `access_roles`, per-action `permissions`, `admin_middleware`, `show_in_admin_menu`, and explicit `filters`
- field metadata may use `filter`, `filterable`, or `list_filter` shortcuts
- the framework now ships with built-in blueprint examples that can be listed or resolved directly via `example:<name>` when generating CRUD scaffolds or integration patch notes
