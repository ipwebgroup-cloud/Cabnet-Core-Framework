# Cabnet Core Framework v3.7.0

Cabnet Core v3.7 adds lightweight CRUD policy hooks on top of the earlier Twig-aware generator output, richer field metadata, module permissions, Twig parity, shared view packaging, definition-driven validation, module-registry, CRUD metadata, HTTP/runtime, controller, service/repository, and renderer convergence work.

## What this patch adds

- optional module-level policy hooks for CRUD authorization without controller rewrites
- role arrays remain the default and still act as the fallback permission model
- admin menu visibility can now follow the same module policy decision path as controller authorization
- src-first CRUD generation can now preserve optional `policy_class` metadata in generated module config stubs
- policy evaluation can receive surface-aware context such as admin menu, index, create, update, and delete flows

## Start here

- `docs/V3_7_LIGHTWEIGHT_POLICY_HOOKS.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
