# ARCHITECTURE.md

## 1. Overview

Cabnet Core uses an **MVC-lite modular architecture** intended for business websites and admin-heavy systems running on shared hosting or cPanel environments.

The architecture is designed around these priorities:

- predictable structure
- safe incremental growth
- public/admin separation
- reusable services and repositories
- template-engine abstraction
- documentation-first development

---

## 2. High-Level Layers

### Core Layer
Provides system foundations:

- application bootstrap
- configuration loading
- environment loading
- request/response helpers
- router
- session manager
- authentication guard
- validation helpers
- flash message handling
- error handling

### Application Layer
Contains business functionality:

- controllers
- services
- repositories
- models/entities
- middleware
- policy/permission rules

### Presentation Layer
Responsible only for rendering:

- Twig templates
- PHP templates
- layouts
- partials
- components
- email templates
- admin/public view shells

### Infrastructure Layer
Handles persistence and operations:

- database access
- file uploads
- caching
- logging
- export/import
- release packaging
- storage management

---

## 3. Core Architecture Rules

1. Controllers stay thin.
2. Services own business logic.
3. Repositories own SQL and database access patterns.
4. Templates only render data.
5. Public and admin interfaces remain structurally separate.
6. Shared helpers live in common support/core areas.
7. Every major release must be documented and packageable.
8. The project must be resumable from documents and files, not only chat history.

---

## 4. Request Flow

### Standard Request Lifecycle

1. User hits entry point:
   - `public/index.php`
   - `admin/index.php`
   - `api/index.php`

2. Bootstrap loads:
   - config
   - environment
   - autoloading
   - session
   - DB connection
   - services
   - route definitions

3. Router resolves:
   - method
   - path
   - route params
   - middleware

4. Middleware runs:
   - auth
   - admin access
   - CSRF
   - localization
   - maintenance mode if needed

5. Controller executes:
   - input collection
   - validation
   - service call
   - response selection

6. Service executes:
   - business logic
   - repository calls
   - secondary support calls

7. Repository executes:
   - prepared statements
   - data mapping
   - return normalized results

8. Response returns:
   - rendered HTML
   - redirect + flash message
   - JSON/API output

---

## 5. Directory Responsibilities

## `/app/Config`
App-level configuration structures or config objects.

## `/app/Core`
Core reusable classes:
- App
- Router
- Request
- Response
- Session
- Auth
- Validator
- Flash
- View manager

## `/app/Controllers`
Grouped by delivery surface:
- `Public`
- `Admin`
- `Api`

## `/app/Middleware`
Request filters such as:
- auth checks
- admin-only guards
- localization
- CSRF validation

## `/app/Models`
Simple model/entity objects or data structures.

## `/app/Repositories`
Database query classes per entity/domain.

## `/app/Services`
Business logic layer.

## `/app/Support`
Reusable helpers and utility classes.

## `/app/Validation`
Validation rules and request/form validators.

## `/app/Views`
All presentation files:
- `twig`
- `php`

## `/app/Lang`
Language dictionaries and translation keys.

## `/bootstrap`
System startup files:
- `app.php`
- `routes.php`
- `services.php`

## `/config`
Environment-specific configuration arrays.

## `/database`
Schema management:
- migrations
- seeds
- snapshots
- SQL schema files

## `/public`
Public web root:
- assets
- front controller
- rewrite rules

## `/admin`
Dedicated admin entry point.

## `/api`
Dedicated API entry point.

## `/storage`
Writable runtime files:
- logs
- sessions
- uploads
- cache
- exports

## `/docs`
Human-readable project source-of-truth documents.

## `/specs`
Release and deployment artifacts:
- markdown notes
- sql notes
- deploy packages

---

## 6. Rendering Architecture

Cabnet Core should support a **renderer abstraction**.

### Goal
Allow the application to switch between rendering engines without changing core logic.

### Planned Renderers

- `TwigRenderer`
- `PhpRenderer`

### Rules

- controllers call a shared render interface
- renderers receive templates + data
- template engine choice must not leak into services or repositories

---

## 7. Controller Pattern

Controllers should generally follow this pattern:

1. gather request data
2. validate request data
3. call the correct service
4. prepare response view model
5. render or redirect

### Example Responsibilities

#### Public Controllers
- home page
- service listing/detail
- catalog listing/detail
- contact/request pages

#### Admin Controllers
- dashboard
- CRUD list/create/edit/delete
- settings
- uploads/media
- documentation/help pages

#### API Controllers
- JSON endpoints
- authenticated or public API access
- structured machine-readable responses

---

## 8. Service Pattern

Services coordinate business logic.

### Responsibilities

- enforce business rules
- orchestrate repository calls
- normalize data for controllers
- trigger support utilities (SEO, uploads, logging)

### Examples

- `BookingService`
- `ServiceCatalogService`
- `TourPlanService`
- `UploadService`
- `SeoMetaService`
- `UserAuthService`

---

## 9. Repository Pattern

Repositories encapsulate SQL.

### Responsibilities

- build safe prepared statements
- read/write entity data
- centralize schema-specific access logic
- isolate SQL from controllers

### Example Repositories

- `ServiceRepository`
- `BookingRepository`
- `ProductRepository`
- `MediaRepository`
- `UserRepository`

---

## 10. Admin/Public UI Strategy

## Public UI
Built for visitors:
- clean navigation
- content clarity
- catalog/service browsing
- request/booking/contact flows
- SEO-friendly rendering

## Admin UI
Built as an application:
- dashboard
- CRUD interfaces
- search/filtering
- approval/state management
- translation support
- settings/help/logs

### Shared UI Base
- Bootstrap 5
- Font Awesome
- consistent layout rules
- responsive design
- readable form system

---

## 11. Data and Encoding Strategy

All projects using this architecture should standardize:

- `utf8mb4` database charset
- `utf8mb4` connection charset
- UTF-8 HTML output
- Greek-safe admin and public forms
- standardized timestamp fields
- documented schema snapshots

---

## 12. Documentation Architecture

Documentation is part of the system design, not external decoration.

### Mandatory Docs

- `PROJECT_MASTER_CONTEXT.md`
- `SCOPE.md`
- `ROADMAP.md`
- `ARCHITECTURE.md`
- `DB_SCHEMA.md`
- `DEPLOYMENT.md`
- `CHANGELOG.md`
- `QA_CHECKLIST.md`

### Reason
A fresh session, developer, or AI assistant must be able to resume the project from the docs pack.

---

## 13. Release Architecture

Each major milestone should produce a release package under:

```text
/specs/deploy/release-vX.Y.Z/
```

Each release should include:

- deploy files
- SQL changes
- release notes
- verification notes
- rollback notes
- build identity
- patch chain

---

## 14. Security Baseline

All implementations should include:

- prepared statements
- session hardening
- auth guards for admin routes
- CSRF protection for forms
- file upload validation
- output escaping
- role-aware access where needed

---

## 15. Extension Philosophy

The framework should be extensible without rewriting the core.

### Planned Extension Areas

- multilingual content management
- media library
- SEO manager
- docs viewer
- audit logs
- notifications
- lightweight API module
- module/entity generator patterns

---

## 16. Summary

Cabnet Core architecture is intentionally conservative, modular, and practical.

It is optimized for:

- stability
- clarity
- shared hosting compatibility
- public/admin dual applications
- repeatable CRUD patterns
- long-term maintainability
- AI-assisted phased development
