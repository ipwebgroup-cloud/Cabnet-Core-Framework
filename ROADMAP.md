# ROADMAP.md

## Current release

- v4.2.0 — Blueprint Schema Validation

## Completed convergence phases

- v2.4 — Rendering convergence
- v2.5 — Legacy runtime reduction
- v2.6 — Service/repository convergence
- v2.7 — HTTP/runtime convergence
- v2.8 — CRUD metadata / entity-definition convergence
- v2.9 — module registry runtime adoption and generator integration patching
- v3.0 — validation and form-metadata convergence
- v3.1 — view packaging convergence
- v3.2 — shared layout and partial convergence
- v3.3 — Twig layout and partial parity
- v3.4 — module permissions and filter metadata
- v3.5 — richer field metadata for uploads, relations, and multilingual content
- v3.6 — Twig-aware generator output for src-owned CRUD module scaffolds
- v3.7 — lightweight policy hooks for module authorization and admin menu visibility
- v3.8 — generator/runtime metadata parity cleanup for CRUD scaffolds
- v3.9 — built-in blueprint library and executable example packs for safer scaffold authoring
- v4.0 — lightweight runtime dependency-injection bridge for controller and middleware construction
- v4.1 — relation filter option hydration for runtime and generator parity
- v4.2 — blueprint schema validation and baseline reconciliation for documented example/policy/DI features

## Next strongest moves

1. service registry formalization so typed runtime bindings become easier to extend without touching the legacy service array directly
2. controller/service autowiring refinements so runtime construction can reduce manual service glue without losing compatibility
3. blueprint authoring tooling so custom scaffold blueprints can be created from guided templates instead of raw JSON alone
