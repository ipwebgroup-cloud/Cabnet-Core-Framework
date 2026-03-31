# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v2.9.0  
**Type:** Consolidated reusable PHP MVC-lite starter framework  
**Status:** Stable transitional baseline  
**Purpose:** Reusable base for business websites, catalogs, admin-heavy tools, and multilingual content systems

---

## What This Bundle Contains

This bundle consolidates the latest framework state from the iterative phase build process into a single reusable package.

### Included Framework Capabilities

- public/admin/API front controllers
- bootstrap loader
- config layer
- PDO database layer
- PHP renderer
- Twig integration point
- session and flash layer
- admin authentication starter
- middleware pipeline
- CSRF support
- validation layer
- repository/service structure
- CRUD entity definition system
- generic CRUD list and form partials
- searchable paginated admin list pattern
- named-route support
- URL helper
- entity/module generator foundation
- scaffold code generation
- integration patch generation
- documentation pack
- smoke-test baseline for bootstrap/runtime/generator regressions
- src-owned HTTP/runtime helpers with legacy shim compatibility
- module-registry-driven CRUD service, route, and admin menu bootstrapping

---

## Best Use Cases

This framework is best suited for:

- tours/service websites
- product catalogs
- admin-driven business applications
- multilingual company sites
- content-managed portals
- shared-hosting/cPanel PHP projects

---

## Recommended Workflow

1. Copy this bundle to a new project root.
2. Update:
   - `config/app.php`
   - `config/database.php`
   - branding/layout text
3. Create the first project schema.
4. Use the sample `services` module as a reference.
5. For new entities:
   - create a blueprint JSON
   - generate scaffold output
   - review
   - merge
6. Prefer `src/` for all new framework-facing code.
7. Use `--legacy` generation only when extending an older fork that still requires the compatibility layer.

---

## Key Files to Know

### Core runtime
- `bootstrap/app.php`
- `bootstrap/routes.php`
- `bootstrap/services.php`
- `src/Bootstrap/Kernel.php`

### Main framework classes
- `src/Application/Controllers/BaseController.php`
- `src/Application/Controllers/Admin/BaseCrudController.php`
- `src/Application/Services/BaseService.php`
- `src/Infrastructure/Repositories/BaseRepository.php`
- `app/Core/App.php`

### CRUD and admin reuse
- `src/Application/Crud/CrudModuleBootstrap.php`
- `src/Application/Crud/CrudModuleRegistry.php`
- `src/Application/Crud/Definitions/ServiceEntityDefinition.php`
- `src/Application/Services/ServiceCrudService.php`
- `src/Infrastructure/Repositories/ServiceRepository.php`
- `app/Views/php/admin/crud/index_table.php`
- `app/Views/php/admin/crud/form_page.php`
- `app/Views/php/admin/crud/form_fields.php`

### Generators
- `src/Generators/EntityGenerator.php`
- `src/Generators/CrudScaffoldWriter.php`
- `src/Generators/IntegrationPatcher.php`
- `scripts/generate-entity.php`
- `scripts/generate-crud-pack.php`
- `scripts/generate-integration-patches.php`

### Starter schema
- `database/schema/services.sql`

---

## What Is Production-Ready vs Starter-Level

### Production-ready direction
- framework structure
- public/admin split
- CRUD conventions
- validation/CSRF/session patterns
- reusable rendering flow
- generator-based extension workflow
- safe incremental `src/` migration path

### Still starter-level
- role/permission matrix is not yet built
- advanced media workflows are not yet built
- legacy global CRUD entity definitions still exist for compatibility
- Twig support requires Composer installation
- validation rules still live primarily in CRUD services instead of canonical field metadata
- some integration patching remains manual by design

---

## Recommended Next Steps

### High-priority convergence
- converge validation/form rules into canonical CRUD field metadata
- thin legacy admin controller wrappers further as more modules move to `moduleKey()` ownership
- continue expanding smoke coverage around generated module onboarding and registry-driven runtime behavior

### High-value framework upgrades
- module auto-registration
- richer admin sidebar/menu config
- reusable filter/sort definitions
- multilingual content tables
- media upload manager
- audit logs


## v2.8 CRUD metadata convergence
- canonical CRUD definition model now lives at `src/Application/Crud/CrudEntityDefinition.php`
- legacy global `CrudEntityDefinition` and `ServiceEntityDefinition` remain as compatibility aliases
- `config/modules.php` now carries CRUD module metadata for the built-in `services` module
- `crudModuleRegistry` resolves module metadata and canonical definition instances from config


## v2.9 module registry adoption
- `config/modules.php` now acts as the primary registration seam for built-in admin CRUD modules
- admin CRUD routes, repository services, CRUD services, and admin menu links now derive from module metadata
- canonical src CRUD controllers can now resolve runtime behavior through `moduleKey()` instead of hardcoded service/route names
- generator output now emits a module metadata config block for `config/modules.php`
