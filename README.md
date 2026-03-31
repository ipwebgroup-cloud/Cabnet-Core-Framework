# Cabnet Core Framework v3.3.0

Cabnet Core v3.3 adds src-owned Twig layout and partial parity on top of the earlier shared PHP presentation convergence, definition-driven CRUD validation, module-registry adoption, CRUD metadata convergence, HTTP/runtime convergence, and service/repository convergence work.

## What this pack is

A **source-of-truth documentation pack** for consolidating the framework after the migration-heavy v1.x series.

## Purpose

Use this pack to:

- stabilize the architecture direction
- define `src/` as the preferred framework layer
- mark `app/` as transitional compatibility
- harden admin authentication defaults for safer project forks
- add a lightweight regression check for bootstrap, routes, and admin auth
- move canonical renderer ownership into `src/View` while keeping legacy wrappers
- move canonical controller base ownership into `src/Application/Controllers` while keeping legacy controller shims
- move canonical service/repository base ownership into `src/` while keeping legacy service/repository shims
- move canonical request/response/session/url runtime ownership into `src/` while keeping legacy runtime shims
- move canonical CRUD entity-definition ownership into `src/Application/Crud` while keeping legacy aliases
- add a module-backed CRUD metadata registry for safer generator/runtime alignment
- derive admin CRUD routes, CRUD services, and admin menu links from module metadata
- derive CRUD validation rules and admin form attributes from canonical field metadata
- shift generated CRUD integration guidance toward `config/modules.php` as the primary integration seam
- keep PHP and Twig presentation ownership aligned under `src/Presentation/Views`
- let Twig reuse the same logical template names as PHP views so controllers stay renderer-agnostic

## Start here

- `docs/V3_2_SHARED_LAYOUT_PARTIAL_CONVERGENCE.md`
- `docs/V3_3_TWIG_LAYOUT_PARTIAL_PARITY.md`
- `docs/TWIG_INTEGRATION.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
