# ROADMAP.md

## 1. Framework Roadmap Overview

This roadmap defines the staged build order for the Cabnet Core Framework.

The objective is to build the framework in safe, reusable phases rather than as one oversized implementation.

---

## 2. Current Status

### Completed
- `FRAMEWORK_MASTER_CONTEXT.md`
- `ARCHITECTURE.md`
- `ROADMAP.md`
- starter folder tree manifest

### In Progress
- framework definition and documentation pack

### Not Yet Started
- bootstrap implementation
- config system
- DB layer
- router
- renderer abstraction
- admin/public shell
- auth/session system
- CRUD engine
- multilingual layer
- SEO/meta layer
- release packaging system

---

## 3. Phase Plan

## Phase 1 — Framework Identity and Documentation
### Goal
Create the source-of-truth documentation for the framework.

### Deliverables
- `FRAMEWORK_MASTER_CONTEXT.md`
- `ARCHITECTURE.md`
- `ROADMAP.md`
- starter folder tree manifest

### Status
Completed / active

---

## Phase 2 — Folder Structure and Bootstrap Foundation
### Goal
Create the starter project structure and application bootstrap path.

### Deliverables
- base folder tree
- `public/index.php`
- `admin/index.php`
- `api/index.php`
- `bootstrap/app.php`
- `bootstrap/routes.php`
- `bootstrap/services.php`

### Key Decisions
- front controller pattern
- autoloading strategy
- application container/service registration direction
- cPanel-safe path assumptions

---

## Phase 3 — Configuration and Environment System
### Goal
Create a consistent config strategy for all projects.

### Deliverables
- `config/app.php`
- `config/database.php`
- `config/auth.php`
- `config/mail.php`
- `config/seo.php`
- `config/storage.php`

### Key Decisions
- production vs local overrides
- DB credentials handling
- mail transport placeholders
- storage path conventions

---

## Phase 4 — Core Layer
### Goal
Create the reusable core classes.

### Deliverables
- App bootstrap class
- Request class
- Response class
- Router class
- Session manager
- Flash message manager
- Validator base
- Auth guard base
- error handler

### Outcome
A minimal but complete framework runtime.

---

## Phase 5 — Database and Repository Layer
### Goal
Create DB access and repository conventions.

### Deliverables
- PDO connection wrapper
- DB helper
- repository base class
- migration conventions
- schema snapshot conventions

### Outcome
Clean, centralized database handling.

---

## Phase 6 — Renderer Abstraction
### Goal
Support interchangeable rendering engines.

### Deliverables
- render interface
- `TwigRenderer`
- `PhpRenderer`
- shared render service registration

### Outcome
Template engine flexibility without framework lock-in.

---

## Phase 7 — Public Shell
### Goal
Create the standard public-facing application shell.

### Deliverables
- public base layout
- header/footer partials
- navigation component
- homepage starter template
- generic content/list/detail templates
- asset loading conventions

### Outcome
Reusable public foundation for catalogs and service sites.

---

## Phase 8 — Admin Shell
### Goal
Create the standard admin experience.

### Deliverables
- admin base layout
- sidebar navigation
- dashboard starter page
- CRUD page shell
- form shell
- table/list shell
- flash/validation display components

### Outcome
Reusable back-office application interface.

---

## Phase 9 — Authentication and Session System
### Goal
Secure the admin application.

### Deliverables
- login/logout flow
- admin auth middleware
- session hardening
- role-ready guard structure
- remember-me option only if needed

### Outcome
Secure admin routing baseline.

---

## Phase 10 — CRUD Framework Conventions
### Goal
Standardize entity management.

### Deliverables
- entity definition pattern
- CRUD route conventions
- list/create/edit/delete controller pattern
- reusable form rendering helpers
- search/filter conventions

### Outcome
Rapid admin development for new entities.

---

## Phase 11 — Multilingual Layer
### Goal
Support multilingual content and interface text.

### Deliverables
- language files
- translation helper
- locale middleware
- Greek + English base dictionaries
- DB translation-group strategy notes

### Outcome
Projects are multilingual-ready from the start.

---

## Phase 12 — Media and SEO Systems
### Goal
Standardize uploads and metadata.

### Deliverables
- upload validation flow
- media storage rules
- `SeoMetaService`
- shared metadata head partial
- OG/Twitter/canonical helpers

### Outcome
Production-ready media and SEO support.

---

## Phase 13 — Documentation and Help System
### Goal
Make every project self-describing and handoff-safe.

### Deliverables
- project docs templates
- admin help/docs section
- deployment guide template
- QA checklist template
- release notes template

### Outcome
Projects remain understandable over time.

---

## Phase 14 — Release Pack System
### Goal
Formalize deployment packaging.

### Deliverables
- release directory conventions
- build identity file format
- patch chain format
- rollback notes template
- deploy zip packaging convention

### Outcome
Safer versioning and deployment workflow.

---

## 4. Build Order Summary

Recommended build order:

1. framework docs
2. folder tree
3. bootstrap
4. config system
5. core layer
6. DB/repositories
7. renderer abstraction
8. public shell
9. admin shell
10. auth/session
11. CRUD conventions
12. multilingual
13. media/SEO
14. docs/help
15. release packaging

---

## 5. Immediate Next Steps

The next strongest deliverables are:

1. generate the starter folder tree manifest
2. generate the base bootstrap files
3. generate the config files
4. generate the renderer abstraction
5. generate the public and admin shell starter structure

---

## 6. Working Method

To avoid long-chat instability, the framework should be developed in bounded stages.

### Rules
- one phase per chat or milestone
- each phase produces files
- each milestone is saved locally
- docs updated alongside code
- zip packaging only after a bounded set of files is complete

---

## 7. Success Criteria

The roadmap is successful when the framework provides:

- a reusable starter kit
- a clean public/admin split
- a configurable DB and rendering layer
- multilingual readiness
- repeatable CRUD development
- release-based packaging
- easy AI handoff through docs

---

## 8. Summary

The Cabnet Core roadmap emphasizes phased delivery, stable artifacts, and long-term reusability.

This roadmap should be treated as the official sequence for building the framework.
