# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.2.0  
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
- layered src-first view resolution with compatibility fallback to app views
- src-owned admin CRUD presentation package files

---

## Key Files to Know

- `src/Application/Crud/CrudEntityDefinition.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Application/Services/ServiceCrudService.php`
- `src/Infrastructure/Repositories/CrudRepositoryContract.php`
- `src/Infrastructure/Repositories/BaseRepository.php`
- `src/View/TemplateResolver.php`
- `src/View/ViewEngineFactory.php`
- `src/Presentation/Views/php/admin/crud/*`
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
- canonical admin CRUD presentation views living under `src/Presentation/Views`

---

## Still starter-level

- role/permission matrix is not yet built
- advanced media workflows are not yet built
- legacy global CRUD entity definitions still exist for compatibility
- Twig support requires Composer installation
- some integration patching remains manual by design
- admin/public layouts still primarily live in `app/Views` until a later layout/partial convergence phase

---

## v3.1 view packaging convergence

- the renderer now prefers `src/Presentation/Views/*` before `app/Views/*`
- explicit `@src/...` and `@app/...` template targeting is supported for controlled fallback behavior
- shared admin CRUD presentation partials now live in `src/Presentation/Views/php/admin/crud`
- generated CRUD module views now target `src/Presentation/Views/php/admin/{module}` by default
