# FRAMEWORK_MASTER_CONTEXT.md

## 1. Framework Identity

**Name:** Cabnet Core Framework  
**Type:** Reusable MVC-lite modular boilerplate for PHP business applications  
**Primary Use Cases:** Tours, Turbo Catalog, Sage-style content portals, service businesses, admin-heavy websites, catalog systems  
**Primary Goal:** Provide a stable, repeatable, cPanel-safe development foundation that works well with ChatGPT-assisted software development

---

## 2. Purpose

This framework exists to solve recurring problems across projects:

- inconsistent architecture
- repeated reinvention of folder structures
- unstable long ChatGPT conversations
- weak project handoff between sessions
- messy separation between public and admin code
- inconsistent CRUD/admin patterns
- poor documentation continuity
- unsafe “big bang” builds

The framework is designed to make every project begin from the same trusted base and evolve safely in phases.

---

## 3. Core Principles

The framework must always be:

- modular
- incremental
- manual-deploy friendly
- framework-light
- documentation-driven
- release-based
- reusable across projects
- safe for long software conversations with ChatGPT

### Design Philosophy

- Use plain PHP for core application logic
- Avoid heavy framework lock-in
- Keep deployment compatible with shared hosting and cPanel
- Keep business logic out of templates
- Keep templates presentation-only
- Keep controllers thin
- Centralize database access patterns
- Standardize public and admin architecture
- Treat documentation as part of the application, not an afterthought

---

## 4. Recommended Tech Stack

### Default Stack

- PHP 8+
- MariaDB / MySQL
- PDO for database access
- Twig as primary template engine
- plain PHP renderer as fallback
- Bootstrap 5
- Font Awesome
- Apache with `.htaccess`
- Composer optional, not mandatory
- manual/cPanel deployment supported

### Why This Stack

This stack best matches the intended workflow:

- shared hosting compatibility
- easy manual uploads
- flexible architecture without heavyweight dependencies
- reusable layouts for public and admin layers
- strong support for documentation-first development
- better fit for incremental project growth

---

## 5. Template Engine Strategy

### Primary Recommendation

Use **Twig** as the default template engine.

### Reasons

- clean template inheritance
- reusable layouts and partials
- safer rendering defaults
- good long-term maintainability
- strong fit for public/admin shells
- better future-proofing than older template-first stacks

### Secondary/Fallback Option

Use **plain PHP templates** for smaller or simpler projects.

### Optional Compatibility Layer

Smarty may be supported only as an optional adapter if a specific project needs it, but it should not be the primary rendering direction.

### Rule

The template engine is for **presentation only**. It must not own application architecture.

---

## 6. Architecture Style

The framework should follow an **MVC-lite modular architecture**.

### Layers

#### Core Layer
Responsible for:
- bootstrap
- config loading
- environment loading
- request/response handling
- router
- session manager
- auth guard
- validation helpers
- flash messaging
- error handling

#### Application Layer
Responsible for:
- controllers
- services
- repositories
- models/entities
- form handlers
- permissions/policies

#### Presentation Layer
Responsible for:
- templates
- layouts
- partials
- components
- public views
- admin views
- email views

#### Infrastructure Layer
Responsible for:
- database
- uploads
- logging
- cache
- exports
- backups
- deployment packaging

---

## 7. Standard Folder Structure

```text
/project-root
  /app
    /Config
    /Core
    /Controllers
      /Public
      /Admin
      /Api
    /Middleware
    /Models
    /Repositories
    /Services
    /Support
    /Validation
    /Views
      /twig
        /layouts
        /partials
        /components
        /public
        /admin
        /emails
      /php
    /Lang
      /en
      /el

  /bootstrap
    app.php
    routes.php
    services.php

  /config
    app.php
    database.php
    auth.php
    mail.php
    seo.php
    storage.php

  /database
    /migrations
    /seeds
    /schema
    /snapshots

  /public
    /assets
      /css
      /js
      /images
      /fonts
      /uploads
    index.php
    .htaccess

  /admin
    index.php

  /api
    index.php

  /storage
    /cache
    /logs
    /sessions
    /exports
    /uploads

  /docs
    PROJECT_MASTER_CONTEXT.md
    SCOPE.md
    ROADMAP.md
    ARCHITECTURE.md
    DB_SCHEMA.md
    DEPLOYMENT.md
    CHANGELOG.md
    QA_CHECKLIST.md

  /specs
    /md
    /sql
    /deploy

  /scripts
  /vendor
```

---

## 8. Request Lifecycle

Each request should follow a consistent lifecycle:

1. `public/index.php`
2. load bootstrap
3. load config
4. load environment
5. start session
6. initialize database
7. initialize router
8. run middleware
9. call controller
10. call service/repository
11. render HTML or return JSON
12. log errors/events if needed

### Goal

Keep every request predictable and easy to debug.

---

## 9. Routing Standard

### Public Routes
Examples:

- `/`
- `/about`
- `/services`
- `/service/{slug}`
- `/catalog`
- `/item/{slug}`
- `/contact`
- `/request`
- `/search`

### Admin Routes
Examples:

- `/admin`
- `/admin/login`
- `/admin/dashboard`
- `/admin/{entity}`
- `/admin/{entity}/create`
- `/admin/{entity}/{id}/edit`
- `/admin/settings`
- `/admin/media`
- `/admin/docs`

### API Routes
Examples:

- `/api/...`

### Route Rules

- clean URLs preferred
- legacy compatibility layer allowed when needed
- route names standardized
- middleware applied by route group
- public, admin, and API concerns must stay clearly separated

---

## 10. Controller Standard

Controllers must remain thin.

### Controllers should:

- receive request input
- validate input
- call services
- call repositories through services when needed
- return views or JSON
- handle redirects and flash messages

### Controllers should not:

- contain large business rules
- write raw SQL everywhere
- generate large chunks of HTML
- mix auth, validation, DB, and view logic chaotically

---

## 11. Service Layer Standard

The service layer is where project-specific business logic belongs.

### Example Services

- `TourPlanService`
- `BookingService`
- `TurboCatalogService`
- `TrackLibraryService`
- `MediaUploadService`
- `SeoMetaService`

### Benefits

- cleaner controllers
- reusable business logic
- safer long-term refactoring
- easier testing and debugging
- better separation of responsibilities

---

## 12. Repository / Data Access Standard

Database access should be centralized through repositories.

### Example Repositories

- `TourRepository`
- `ServiceRepository`
- `BookingRepository`
- `ProductRepository`
- `ArtistRepository`

### Benefits

- SQL stays centralized
- easier schema evolution
- cleaner services/controllers
- safer prepared statement usage
- less duplication

---

## 13. Public and Admin Split

Every serious project should be built as two distinct shells.

### Public Shell

Responsible for:
- marketing pages
- content pages
- catalogs
- search
- booking/request forms
- legal pages
- front-end browsing experience

### Admin Shell

Responsible for:
- dashboard
- CRUD
- approvals
- uploads
- translations
- SEO controls
- docs/help
- settings
- logs/status

### Rule

The admin layer must feel like a proper application, not an afterthought.

---

## 14. CRUD Standard

Each entity should follow a predictable CRUD definition structure.

### Example Entity Definition

```php
return [
  'table' => 'services',
  'entity' => 'service',
  'label' => 'Services',
  'fields' => [
    'title' => ['type' => 'text', 'required' => true],
    'slug' => ['type' => 'slug', 'required' => true],
    'status' => ['type' => 'select', 'options' => ['draft', 'published']],
  ],
  'list_columns' => ['id', 'title', 'status', 'updated_at'],
  'searchable' => ['title', 'slug'],
];
```

### Standard CRUD Outputs

- list page
- create page
- edit page
- delete action
- status toggle
- filters
- search

### Goal

Reduce reinvention and make admin development repeatable.

---

## 15. Multilingual Standard

The framework must support multilingual projects natively.

### First-Class Languages

- English
- Greek

### Structure

- language files in `/app/Lang`
- translation keys instead of hardcoded strings
- translation groups in DB when content needs multilingual storage

### Encoding Rules

- database charset must be `utf8mb4`
- connection charset must be `utf8mb4`
- HTML charset must be UTF-8
- admin forms must be Greek-safe
- all content inputs must preserve full multilingual text safely

---

## 16. Database Standards

### Core Rules

- InnoDB
- utf8mb4 everywhere
- consistent collation across the project database

### Common Fields

Where relevant, tables should include:

- `id`
- `created_at`
- `updated_at`
- `deleted_at` for soft delete when appropriate
- `status`
- `sort_order`
- `created_by`
- `updated_by`

### Documentation Rules

Always maintain:

- current schema dump
- migrations
- rollback notes
- stable snapshot dump for major versions

---

## 17. Media and Upload Strategy

The framework needs a unified upload/media policy.

### Public Storage

- `/public/assets/uploads/...`

### Internal/Managed Storage

- `/storage/uploads/...`

### Managed File Types

- images
- PDFs
- audio
- video
- docs

### Upload Rules

- validate MIME type
- validate file size
- sanitize filenames
- separate original files from derived files
- record metadata in DB where needed

---

## 18. SEO and Metadata Layer

A reusable SEO system should be standard.

Each page or entity should support:

- meta title
- meta description
- canonical URL
- OG image
- OG title
- OG description
- Twitter card
- schema markup where useful

### Recommended Implementation

Create a small `SeoMetaService` and a shared head partial so meta handling is centralized and consistent.

---

## 19. Admin UX Standards

The admin shell should always include:

- dashboard
- sidebar navigation
- breadcrumbs
- flash messages
- validation feedback
- searchable/filterable tables
- media tools
- help/docs section
- readable forms
- consistent action buttons

### UI Direction

- Bootstrap 5 layout system
- Font Awesome icons
- mobile-safe behavior
- human-readable navigation
- no chaotic one-off styling

---

## 20. Documentation System

Documentation is a mandatory framework layer.

Every project should include these files:

### `PROJECT_MASTER_CONTEXT.md`
The complete source of truth for the project.

### `SCOPE.md`
What the application is meant to do.

### `ROADMAP.md`
Current phase, completed work, and next steps.

### `ARCHITECTURE.md`
Technical structure and implementation decisions.

### `DB_SCHEMA.md`
Tables, fields, relationships, and important DB rules.

### `DEPLOYMENT.md`
How to upload, configure, and verify the application.

### `CHANGELOG.md`
Version-by-version change history.

### `QA_CHECKLIST.md`
Pre-release verification checklist.

### Why This Matters

This directly solves the “long chat freeze” problem by allowing fresh chats to reload the project from documents instead of relying on huge conversation history.

---

## 21. Release Workflow

Projects should not exist as one endless unstructured state.

Use release packs.

### Release Structure

```text
/specs/deploy/release-v1.0.0/
/specs/deploy/release-v1.0.1/
/specs/deploy/release-v1.1.0/
```

### Each Release Should Contain

- deploy-ready files
- SQL changes
- release notes
- verification notes
- rollback notes
- build identity file
- patch chain record

### Goal

Make every milestone safer, clearer, and easier to recover.

---

## 22. ChatGPT Workflow Standard

This framework is designed to work well with AI-assisted development.

### Never Do

- one giant forever chat
- one enormous “build everything and zip it” instruction
- mixed architecture, packaging, debugging, scraping, and deployment in one step

### Always Do

- one fresh chat per major phase
- upload current docs/context into the new chat
- define one bounded deliverable at a time
- save artifacts locally at each milestone

### Recommended Chat Phases

1. framework design
2. database/schema
3. public shell
4. admin shell
5. CRUD entities
6. media/SEO/translations
7. release packaging

---

## 23. Project Specification Template

Every new project should begin with a structured specification.

### Identity

- project name
- version
- domain
- hosting style
- target users

### Purpose

- what problem it solves
- public goals
- admin goals

### Functional Modules

- catalog
- services
- bookings
- leads
- CMS
- translations
- uploads
- reporting

### Technical Requirements

- PHP version
- DB version
- template engine
- libraries
- JS strategy
- deployment method

### Content Requirements

- languages
- SEO
- schema markup
- images/media
- legal pages

### Delivery Phases

- phase 1 foundation
- phase 2 public pages
- phase 3 admin
- phase 4 polish
- phase 5 release

---

## 24. Best-Fit Identity for This Framework

**Name:** Cabnet Core  
**Style:** MVC-lite modular boilerplate  
**Primary Renderer:** Twig  
**Fallback Renderer:** native PHP  
**Database Layer:** PDO repositories  
**UI:** Bootstrap + Font Awesome  
**Deploy Mode:** cPanel-safe manual deployment  
**Languages:** English + Greek first-class  
**Workflow:** docs-first, release-pack based, ChatGPT-friendly

---

## 25. Problems This Framework Solves

This framework is designed to solve:

- long-chat instability
- repeated folder-structure redesign
- inconsistent admin pages
- weak public/admin separation
- poor continuity between chats
- unsafe “big bang” project requests
- weak documentation and deployment discipline

---

## 26. Build Order

Recommended implementation order:

1. `FRAMEWORK_MASTER_CONTEXT.md`
2. standard folder tree
3. bootstrap/app loader
4. config system
5. DB layer
6. router
7. request/response helpers
8. base controller
9. renderer abstraction
10. Twig renderer
11. public layout
12. admin shell
13. auth/session system
14. CRUD conventions
15. multilingual layer
16. SEO/meta service
17. docs pack
18. release-pack structure
19. starter zip

---

## 27. Most Important Rule

The framework must always be built so that a **new chat can continue the project from documents and files**, not from memory of an older conversation.

That principle is essential for stability, maintainability, and long-term productivity.

---

## 28. Next Deliverables

After this document, the next recommended deliverables are:

1. starter folder tree
2. `ARCHITECTURE.md`
3. `ROADMAP.md`
4. bootstrap core files
5. renderer abstraction
6. admin/public shell starter package

---

## 29. Final Summary

Cabnet Core Framework is a reusable development foundation tailored for:

- shared hosting
- manual deployment
- public/admin dual architecture
- multilingual business applications
- documentation-first project management
- safe phased development with ChatGPT

It should be treated as the reusable source-of-truth framework for future projects.

