# Cabnet Core Framework v2.8.0

Cabnet Core v2.8 adds src-owned CRUD entity-definition modeling and module metadata convergence on top of the earlier runtime, controller, service/repository, renderer, and HTTP/runtime convergence work.

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
- decide the next path:
  - cleanup the framework
  - or fork it into a real project starter

## Start here

- `docs/V2_0_CONSOLIDATION_PLAN.md`
- `docs/V2_0_ARCHITECTURE_STATUS.md`
- `docs/V2_1_RUNTIME_BOOTSTRAP_CONVERGENCE.md`
- `docs/V2_2_AUTH_HARDENING.md`
- `docs/V2_3_SMOKE_TEST_BASELINE.md`
- `docs/V2_4_RENDERING_CONVERGENCE.md`
- `docs/V2_5_LEGACY_RUNTIME_REDUCTION.md`
- `docs/V2_6_SERVICE_REPOSITORY_CONVERGENCE.md`
- `docs/V2_7_HTTP_RUNTIME_CONVERGENCE.md`
- `docs/V2_8_CRUD_METADATA_CONVERGENCE.md`
- `docs/V2_0_DEPRECATION_POLICY.md`
- `docs/PROJECT_FORK_GUIDE.md`
