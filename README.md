# CABNET CORE FRAMEWORK

A source-available **PHP MVC-lite framework baseline** designed for **cPanel/shared-hosting environments**, safe incremental modernization, and practical real-world project delivery.

This framework is built for developers and teams who want a clean, reusable starting point for PHP applications without the overhead of large framework ecosystems. Its direction is centered on **public/admin/API separation**, a **`src/`-first architecture**, a **transitional compatibility layer through `app/`**, and a workflow that supports **documentation-driven development**, **generator-assisted CRUD/module scaffolding**, and production-safe refactoring over time.

> **License:** Source-available for non-commercial use only. Commercial use requires prior express written consent.

---

## Overview

CABNET CORE FRAMEWORK is intended to serve as a durable foundation for modernizing or launching PHP applications in environments where simplicity, portability, and deployment practicality matter.

It is especially suited for:

- cPanel and shared-hosting deployments
- plain PHP projects that need better structure
- legacy PHP systems moving gradually toward a cleaner architecture
- admin/public/API split applications
- CRUD-heavy business systems
- projects that benefit from step-by-step modernization instead of risky rewrites

The framework favors a disciplined middle ground:

- more structure than ad hoc legacy PHP
- less weight and friction than full-stack enterprise frameworks
- compatibility with real hosting limitations
- gradual migration paths instead of all-at-once rebuilds

---

## Core Direction

The intended long-term direction of CABNET CORE FRAMEWORK is:

- **Reusable PHP MVC-lite baseline**
- **cPanel/shared-hosting friendly**
- **Public / Admin / API separation**
- **`src/` as the preferred future architecture**
- **`app/` as a transitional compatibility layer**
- **Docs-driven workflow**
- **Generator-assisted CRUD and module scaffolding**
- **Safe incremental modernization**
- **Legacy-aware migration planning**
- **Production-first implementation discipline**

This is not a framework built around abstraction for abstraction’s sake. It is designed to help real projects move from fragile legacy code toward a more maintainable structure while keeping existing deployments stable.

---

## Key Principles

### 1. Safe Incremental Modernization
The framework is built around the idea that production systems should be improved in controlled phases. Existing routes, workflows, and data expectations should be preserved whenever practical.

### 2. Source of Truth Comes First
Code decisions should be based on the actual current repository and live project state, not assumptions or idealized architecture diagrams.

### 3. `src/` First, `app/` Compatible
New architecture should prefer clean organization inside `src/`, while `app/` can remain available as a bridge layer during migration.

### 4. Public / Admin / API Separation
Applications should be structured so that front-end pages, administrative interfaces, and API endpoints evolve clearly and independently.

### 5. Hosting Reality Matters
The framework is intentionally compatible with cPanel and shared-hosting workflows, including manual deployment patterns and projects without heavy infrastructure.

### 6. Documentation Is Part of the System
Architecture notes, migration plans, scope files, and implementation handoff documents are treated as part of the project’s operational foundation.

---

## What This Framework Is Good For

CABNET CORE FRAMEWORK is a strong fit for:

- business websites with admin panels
- catalog systems
- booking or service applications
- content-management style platforms
- media libraries
- internal tools
- legacy PHP modernization projects
- modular project baselines that need repeatable CRUD patterns

It is less focused on:

- large cloud-native microservice ecosystems
- highly abstracted enterprise dependency graphs
- framework-heavy package ecosystems
- opinionated front-end SPA-first stacks

---

## Architecture Direction

A typical architectural direction for this framework includes:

- `public/` for public entry points
- `admin/` for administrative interfaces
- `api/` for endpoint routing
- `src/` for preferred core architecture
- `app/` for transitional compatibility and legacy bridging
- `config/` for configuration management
- `storage/` or equivalent for writable runtime data where needed
- `docs/` or project specs for documentation, planning, and handoff artifacts

The goal is not to force a single rigid structure immediately, but to provide a stable migration path toward a consistent internal system.

---

## Development Philosophy

CABNET CORE FRAMEWORK follows a practical engineering philosophy:

- inspect first
- assess second
- recommend the strongest next move
- implement in safe phases
- preserve production stability
- reduce duplication
- converge services and repositories gradually
- improve documentation alongside code
- avoid reckless rewrites

Every meaningful phase should leave the project more understandable, more modular, and less fragile than before.

---

## Typical Workflow

A recommended workflow when extending this framework is:

1. Inspect the actual uploaded or live codebase
2. Map the current architecture and runtime behavior
3. Identify the strongest next safe implementation phase
4. Implement only the changes required for that phase
5. Keep handoff notes and documentation current
6. Package only the altered files where practical
7. Repeat in controlled iterations

This framework is intended to support exactly that kind of disciplined progression.

---

## Generator and CRUD Direction

CABNET CORE FRAMEWORK is designed to support generator-assisted development patterns, especially for:

- CRUD module scaffolding
- controller/service/repository convergence
- admin-page creation
- repeatable model-based forms
- structured routing expansion
- migration from duplicated legacy patterns into reusable modules

The framework direction encourages scaffolding to accelerate implementation while still allowing manual refinement where project-specific behavior is required.

---

## Compatibility and Deployment

This framework is meant to work well in real hosting conditions, including:

- shared hosting
- cPanel file deployment
- manual zip-based updates
- projects without Composer-heavy workflows
- PHP applications that need progressive modernization rather than full platform replacement

That makes it especially useful for agencies, internal tooling, legacy business systems, and practical project delivery environments.

---

## Recommended Use Cases

Use CABNET CORE FRAMEWORK when you need:

- a structured PHP baseline without enterprise overhead
- a safe path from legacy PHP into a cleaner architecture
- reusable admin/public/API patterns
- documentation-supported project execution
- a modular baseline for custom business applications
- a framework that respects hosting and deployment constraints

---

## Project Status

CABNET CORE FRAMEWORK is an evolving baseline intended to mature through versioned, implementation-driven phases.

Its direction includes continued work in areas such as:

- service and repository convergence
- generator-assisted scaffolding
- legacy runtime reduction
- module standardization
- admin/public/API consistency
- documentation and handoff quality
- transitional compatibility cleanup
- safer extension patterns for future projects

---

## Usage Summary

You may use, study, and modify this software for:

- personal use
- educational use
- internal evaluation
- non-commercial testing and development

You may **not** use this project commercially unless you first obtain **prior express written consent** from the copyright holder.

Commercial use includes, but is not limited to:

- selling or relicensing the software
- using it in paid products or services
- using it for client work
- deploying it in revenue-generating business operations
- offering hosting, SaaS, consulting, implementation, or support for compensation

Downloading, cloning, forking, accessing, or modifying the source code does **not** grant any commercial rights.

---

## License

This project is released under the **CABNET CORE FRAMEWORK Custom Non-Commercial Source-Available License v1.0**.

Commercial use is strictly prohibited without prior express written consent.

See the `LICENSE` file for full terms.

### Commercial Licensing Contact

**Andreas Fiotodimitrakis**  
cabnet.app, Advanced Infrastructure Management  
AI Industry Solutions  
Email: **ccf@cabnet.app**  
Website: **ccf.cabnet.app**

---

## Final Note

CABNET CORE FRAMEWORK exists to make structured PHP development more practical, more portable, and more sustainable for real-world projects.

It is built for careful progress, clear architecture, and long-term maintainability without losing touch with deployment reality.