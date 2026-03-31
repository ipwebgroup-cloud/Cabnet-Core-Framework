# V3_3_TWIG_LAYOUT_PARTIAL_PARITY.md

## Phase name

v3.3 — Twig Layout and Partial Parity

## Objective

Bring the optional Twig rendering layer into parity with the src-owned PHP presentation layer while keeping controllers renderer-agnostic and preserving `app/Views/twig` compatibility.

## Why this phase

After v3.2, the PHP presentation layer had a clear src-owned home for shared layouts, partials, and built-in views. Twig still lagged behind with only starter files under `app/Views/twig`, so switching renderers would leave the migration story incomplete and force renderer-specific template handling.

## What changed

- canonical Twig ownership moved into `src/Presentation/Views/twig`
- shared Twig layouts and flash partial now live under `src/Presentation/Views/twig/layouts` and `src/Presentation/Views/twig/partials`
- built-in admin/public Twig pages now live under `src/Presentation/Views/twig`
- generic CRUD list and form Twig templates now live under `src/Presentation/Views/twig/admin/crud`
- services CRUD starter Twig templates now live under `src/Presentation/Views/twig/admin/services`
- `app/Views/twig` files now act as compatibility wrappers where applicable
- `TwigRenderer` maps logical `.php` template names to `.twig`

## Expected outcome

- the Twig layer follows the same src-first ownership model as the PHP layer
- controllers can keep stable logical template names regardless of renderer choice
- the built-in services CRUD flow has Twig starter coverage
- future Twig-aware generator work can build on canonical src-owned templates instead of legacy app paths
