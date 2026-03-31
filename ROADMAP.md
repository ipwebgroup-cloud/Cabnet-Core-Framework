# ROADMAP.md

## Current release

- v3.9.0 — Blueprint Library and Examples

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

## Next strongest moves

1. runtime dependency-injection bridge so middleware/controller/service construction becomes less dependent on direct instantiation
2. relation-filter option hydration so relation-backed list filters can become first-class selects without custom per-project glue
3. blueprint schema validation so malformed scaffold blueprints fail earlier with clearer authoring feedback
