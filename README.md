# Cabnet Core Framework v3.6.0

Cabnet Core v3.6 adds Twig-aware CRUD generator output on top of the earlier richer field metadata, module permissions, Twig parity, shared view packaging, definition-driven validation, module-registry, CRUD metadata, HTTP/runtime, controller, service/repository, and renderer convergence work.

## What this patch adds

- src-first CRUD generator can now emit src-owned Twig admin view stubs
- blueprint input can now declare `view_engine` or `view_engines`
- CLI generation can now request Twig output with `--twig` or `--twig-only`
- generated implementation notes now explain PHP vs Twig presentation targets
- generator output stays compatibility-safe by keeping PHP generation as the default

## Start here

- `docs/V3_6_TWIG_AWARE_GENERATOR_OUTPUT.md`
- `docs/FRAMEWORK_HANDOFF.md`
- `docs/CRUD_CONVENTIONS.md`
- `docs/ADD_NEW_ENTITY.md`
