# Cabnet Core Framework v3.5.0

Cabnet Core v3.5 adds richer CRUD field metadata on top of the earlier module permissions, Twig parity, shared view packaging, definition-driven validation, module-registry, CRUD metadata, HTTP/runtime, controller, service/repository, and renderer convergence work.

## What this patch adds

- metadata-driven upload fields
- metadata-driven relation-driven select fields
- metadata-driven translatable/multilingual fields
- request input now merges uploaded files into CRUD payloads
- definition-driven CRUD services can now hydrate relation options and persist uploads
- shared CRUD PHP and Twig form partials now render file, relation, and locale-aware inputs
- generator output now preserves upload, relation, and multilingual metadata

## Start here

- `docs/V3_5_RICHER_FIELD_METADATA.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
