# FRAMEWORK_HANDOFF.md

## Bundle Identity

**Name:** Cabnet Core Framework Bundle  
**Version:** v1.0.0  
**Type:** Consolidated reusable PHP MVC-lite starter framework  
**Status:** Stable starter bundle  
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
   - generate integration patches
   - review
   - merge

---

## Key Files to Know

### Core runtime
- `bootstrap/app.php`
- `bootstrap/routes.php`
- `bootstrap/services.php`

### Main framework classes
- `app/Core/App.php`
- `app/Core/Router.php`
- `app/Core/Response.php`
- `app/Core/Request.php`

### CRUD and admin reuse
- `app/Controllers/Admin/BaseCrudController.php`
- `app/Crud/CrudEntityDefinition.php`
- `app/Views/php/admin/crud/index_table.php`
- `app/Views/php/admin/crud/form_page.php`
- `app/Views/php/admin/crud/form_fields.php`

### Generators
- `app/Generators/EntityGenerator.php`
- `app/Generators/ScaffoldWriter.php`
- `app/Generators/IntegrationPatcher.php`
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

### Still starter-level
- admin auth credentials are simple demo credentials
- no password hashing/user management system yet
- no role/permission matrix yet
- no automatic live-file patching yet
- Twig support requires Composer installation
- no advanced media manager yet

---

## Recommended Next Steps

### High-priority hardening
- replace demo admin login with DB-backed users
- add password hashing
- add CSRF middleware enforcement
- add request validation helper classes
- add logging/error pages
- add .env or environment override strategy

### High-value framework upgrades
- module auto-registration
- richer admin sidebar/menu config
- reusable filter/sort definitions
- multilingual content tables
- media upload manager
- audit logs

---

## Suggested Project Startup Sequence

For a new project:

1. duplicate framework bundle
2. set project name and DB config
3. create `PROJECT_MASTER_CONTEXT.md`
4. create project-specific schema
5. keep or remove sample `services` entity
6. generate first real entity module
7. integrate patches
8. test CRUD flow
9. package first release

---

## Final Notes

This bundle is designed to solve a specific workflow problem:
**long software chats become unstable, but files and docs remain stable.**

Use this framework as a reusable source-of-truth artifact, not as something that only exists inside one conversation.
