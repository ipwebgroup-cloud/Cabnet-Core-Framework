# ROADMAP.md

## Current release

- v3.5.0 — Richer Field Metadata

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

## Next strongest moves

1. Twig-aware generator output so generated module packs can optionally emit src-owned Twig presentation files
2. lightweight policy hooks so module permissions can expand beyond role arrays without controller rewrites
3. generator/runtime parity cleanup so richer field metadata flows into generated modules with less manual follow-up
