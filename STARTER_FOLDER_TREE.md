# STARTER_FOLDER_TREE.md

## 1. Starter Folder Tree

This is the canonical starter structure for the Cabnet Core Framework.

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

## 2. Directory Intent

### `/app`
Primary application code.

### `/app/Core`
Framework internals and shared infrastructure classes.

### `/app/Controllers`
Controller entry points split by public, admin, and API.

### `/app/Repositories`
Data-access logic and SQL ownership.

### `/app/Services`
Business logic orchestration.

### `/app/Views`
Presentation layer, with renderer-specific directories.

### `/app/Lang`
Language dictionaries and translation maps.

### `/bootstrap`
Application startup files.

### `/config`
Environment-aware configuration definitions.

### `/database`
Migrations, seeds, schema files, and DB snapshots.

### `/public`
Main web root and static public assets.

### `/admin`
Dedicated admin front controller.

### `/api`
Dedicated API front controller.

### `/storage`
Writable runtime data and generated files.

### `/docs`
Source-of-truth project documentation.

### `/specs`
Structured release, SQL, and deployment artifacts.

### `/scripts`
Utility or local helper scripts.

### `/vendor`
Third-party dependencies when used.

---

## 3. Notes

- This structure is optimized for shared hosting and manual deployments.
- Public and admin concerns are intentionally separated.
- The `/docs` folder is part of the framework design, not optional decoration.
- The `/specs` folder is intended to support release-oriented workflows.
- Template engine choice should not change the overall framework structure.

---

## 4. Usage Rule

Every new project should begin from this tree and then extend it carefully rather than inventing a new structure from scratch.
