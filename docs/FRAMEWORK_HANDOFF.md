# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.4.0  
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
- layered src-first view resolution with compatibility fallback to app views
- src-owned PHP and Twig presentation package files
- module action permissions derived from `config/modules.php`
- module list filters derived from `config/modules.php`

## Key Files to Know

- `config/modules.php`
- `src/Application/Crud/CrudModuleRegistry.php`
- `src/Application/Crud/CrudEntityDefinition.php`
- `src/Application/Controllers/Admin/BaseCrudController.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Infrastructure/Repositories/BaseRepository.php`
- `src/Support/AdminMenu.php`
- `src/Presentation/Views/php/admin/crud/index_table.php`
- `src/Presentation/Views/twig/admin/crud/index_table.twig`
- `src/Generators/CrudScaffoldWriter.php`

## Production-ready direction

- framework structure
- public/admin/API split
- CRUD conventions
- validation/CSRF/session patterns
- reusable rendering flow
- generator-based extension workflow
- safe incremental `src/` migration path
- canonical field metadata feeding validation and forms
- canonical admin CRUD presentation views living under `src/Presentation/Views`
- module metadata feeding CRUD access control and list filtering

## Still starter-level

- role/permission matrix is lightweight and module-scoped, not a full policy system
- advanced media workflows are not yet built
- legacy global CRUD entity definitions still exist for compatibility
- Twig support requires Composer installation
- some integration patching remains manual by design

## Current delivery model

- patch packages may include only changed files
- read `FRAMEWORK_STATUS.json`, `PATCH_MANIFEST.txt`, and this handoff file first when resuming from a patch-only zip
- `HANDOFF_PROMPT.txt` is continuity-critical and should be kept aligned with the current phase and delivery model
