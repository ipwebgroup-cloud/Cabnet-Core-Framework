# Cabnet Core Framework Bundle v3.3.0

This patch continues the consolidated framework bundle with the next convergence phase.

## Included in this patch
- canonical src-owned Twig layouts and shared partials
- canonical src-owned Twig built-in page views and generic CRUD Twig views
- logical template-name mapping from `.php` to `.twig` inside the Twig renderer
- compatibility shims under `app/Views/twig`
- updated release documentation, handoff notes, and roadmap guidance
- expanded smoke coverage for logical Twig template mapping and src-first Twig resolution

## v3.3 highlights
- Twig now follows the same src-first ownership model as the PHP presentation layer
- existing controllers can keep rendering logical `.php` template names even when the renderer is switched to Twig
- generic admin CRUD list/form views now have Twig equivalents under `src/Presentation/Views/twig/admin/crud`
- legacy `app/Views/twig` files act as compatibility wrappers over the canonical src-owned Twig templates
