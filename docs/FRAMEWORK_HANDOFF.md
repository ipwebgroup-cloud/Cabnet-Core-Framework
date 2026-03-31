# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v3.3.0  
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
- src-owned admin CRUD presentation package files
- src-owned Twig layouts, partials, built-in pages, and CRUD starter views
- logical `.php` to `.twig` template-name mapping for renderer-agnostic controllers

---

## Key Files to Know

- `src/Application/Crud/CrudEntityDefinition.php`
- `src/Application/Services/DefinitionCrudService.php`
- `src/Application/Services/ServiceCrudService.php`
- `src/Infrastructure/Repositories/CrudRepositoryContract.php`
- `src/Infrastructure/Repositories/BaseRepository.php`
- `src/View/TemplateResolver.php`
- `src/View/TwigRenderer.php`
- `src/View/ViewEngineFactory.php`
- `src/Presentation/Views/php/admin/crud/*`
- `src/Presentation/Views/twig/admin/crud/*`
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
- canonical Twig layouts and starter CRUD views living under `src/Presentation/Views/twig`

---

## Still starter-level

- role/permission matrix is not yet built
- advanced media workflows are not yet built
- legacy global CRUD entity definitions still exist for compatibility
- Twig support still requires Composer installation
- some integration patching remains manual by design
- generator output is still PHP-first even though the Twig runtime layer now has parity-ready starter templates

---

## Packaging rule

- recent releases may be **patch-only overlays**
- read `PATCH_MANIFEST.txt` before assuming the uploaded zip is a full repository
- read `FRAMEWORK_STATUS.json` and this file first in every new chat
- keep this file and `HANDOFF_PROMPT.txt` aligned whenever a new patch is produced

---

## v3.3 Twig layout and partial parity

- the canonical Twig layer now mirrors the src-owned PHP presentation layer
- `TwigRenderer` maps logical `.php` template names to `.twig`, so controllers do not need renderer-specific template names
- shared Twig layouts and partials now live under `src/Presentation/Views/twig/layouts` and `src/Presentation/Views/twig/partials`
- built-in admin/public Twig views and starter CRUD templates now live under `src/Presentation/Views/twig`
- `app/Views/twig` files act as compatibility wrappers over the canonical src-owned Twig templates
