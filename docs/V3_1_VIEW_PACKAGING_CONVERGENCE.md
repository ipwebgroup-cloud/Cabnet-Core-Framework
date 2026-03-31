# V3.1 — View Packaging Convergence

## Goal
Move canonical admin CRUD presentation ownership closer to `src/` while preserving `app/Views` compatibility.

## What changed
- `src/View/ViewEngineFactory` now resolves PHP and Twig templates from layered roots, preferring `src/Presentation/Views/*` before `app/Views/*`
- `src/View/TemplateResolver` supports layered root lookup and explicit `@src/...` / `@app/...` targeting for controlled fallback behavior
- canonical shared admin CRUD presentation partials now live in `src/Presentation/Views/php/admin/crud`
- canonical services CRUD views now live in `src/Presentation/Views/php/admin/services`
- generated CRUD packs now emit admin PHP views under `src/Presentation/Views/php/admin/{module}`

## Compatibility
- legacy `app/Views/php` remains available as a compatibility fallback
- layouts and older public/admin templates can remain under `app/Views` until a later layout/partial convergence phase
- explicit `@app/...` template resolution can still be used when an older compatibility view must be rendered intentionally

## Validation
- changed PHP files linted successfully
- smoke suite expanded for layered view resolution and src presentation generator output
