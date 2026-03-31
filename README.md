# Cabnet Core Framework v2.5.0

Cabnet Core v2.5 is the consolidated baseline plus runtime bootstrap convergence, auth hardening, a native smoke-test baseline, src-owned renderer convergence, and src-owned controller base convergence.

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
- `docs/V2_0_DEPRECATION_POLICY.md`
- `docs/PROJECT_FORK_GUIDE.md`
