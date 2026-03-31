# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.0.0  
**Type:** Consolidated reusable PHP MVC-lite starter framework  
**Status:** Stable transitional baseline  
**Purpose:** Reusable base for business websites, catalogs, admin-heavy tools, and multilingual content systems

---

## Included Framework Capabilities

- public/admin/API front controllers
- bootstrap loader
- config layer
- PDO database layer
- PHP renderer
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

---

## Key Files to Know

- `src/Application/Crud/CrudEntityDefinition.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Application/Services/ServiceCrudService.php`
- `src/Infrastructure/Repositories/CrudRepositoryContract.php`
- `src/Infrastructure/Repositories/BaseRepository.php`
- `app/Views/php/admin/crud/form_fields.php`
- `src/Generators/CrudScaffoldWriter.php`

---

## Production-ready direction

- framework structure
- public/admin split
- CRUD conventions
- validation/CSRF/session patterns
- reusable rendering flow
- generator-based extension workflow
- safe incremental `src/` migration path
- canonical field metadata feeding validation and forms

---

## Still starter-level

- role/permission matrix is not yet built
- advanced media workflows are not yet built
- legacy global CRUD entity definitions still exist for compatibility
- Twig support requires Composer installation
- some integration patching remains manual by design

---

## v3.0 validation and form metadata convergence

- CRUD field metadata now acts as the canonical source for validation rules and form rendering hints
- `DefinitionCrudService` centralizes shared CRUD create/update validation flow
- `ServiceCrudService` is now a thin definition-driven wrapper
- generator output now emits richer field metadata instead of repeating validation logic in every service
