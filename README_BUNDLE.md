# Cabnet Core Framework Bundle v3.1.0

This patch continues the consolidated framework bundle with the next convergence phase.

## Included in this patch
- layered src-first PHP and Twig view resolution
- src-owned admin CRUD presentation package files
- updated src CRUD generator view output targets
- release documentation and handoff notes
- expanded smoke coverage for layered view resolution and src presentation output

## v3.1 highlights
- the renderer now searches `src/Presentation/Views/*` before `app/Views/*`
- explicit `@app/...` and `@src/...` template targeting is supported for controlled fallback behavior
- canonical CRUD admin views now live under `src/Presentation/Views/php/admin`
- generated CRUD packs now emit src-owned admin presentation view files instead of legacy `app/Views` wrappers
