# V2_4_RENDERING_CONVERGENCE.md

## Summary

V2.4 moves **renderer ownership** from the legacy `app/Core/Rendering` layer into `src/View` while preserving existing compatibility.

## Why this phase mattered

Before v2.4:

- the framework already advertised `src/View` as the preferred direction
- the view factory lived in `src/View`
- but the actual renderer contract and implementations still lived in `app/Core/Rendering`

That meant the framework was still relying on legacy ownership for one of its core runtime boundaries.

## What changed

New canonical view layer files:

- `src/View/Renderer.php`
- `src/View/TemplateResolver.php`
- `src/View/ViewNotFoundException.php`
- `src/View/PhpRenderer.php`
- `src/View/TwigRenderer.php`
- `src/View/ViewEngineFactory.php`

Legacy bridge files retained:

- `app/Core/Rendering/RendererInterface.php`
- `app/Core/Rendering/PhpRenderer.php`
- `app/Core/Rendering/TwigRenderer.php`

The legacy rendering files now act as **compatibility wrappers** instead of the primary implementation layer.

## Resulting ownership model

### Canonical renderer ownership

Renderer contracts and implementations now belong to:

- `src/View`

### Transitional compatibility

The following remain in `app/` only to avoid breaking older code:

- global `RendererInterface`
- global `PhpRenderer`
- global `TwigRenderer`

## Safety rules preserved

- existing controllers continue to call `$app->renderer()->render(...)`
- views remain under `app/Views/*`
- the current hybrid runtime keeps working without route or controller rewrites
- legacy global renderer classes still work if older code instantiates them directly

## What did not change yet

This phase does **not** migrate:

- view files out of `app/Views`
- base controller/view helper behavior out of `app/`
- full layout/templating abstractions
- CRUD base controller rendering contracts

## Smoke coverage added

The smoke test baseline now also verifies:

- renderer service returns the canonical `Cabnet\\View\\Renderer` contract
- default renderer uses the `src/View` PHP renderer
- the legacy global `PhpRenderer` wrapper remains compatible and functional

## Next likely phase

The next strong move after v2.4 is **legacy runtime reduction**, especially:

- shrinking duplicated `AppRuntime`/`Kernel` concerns
- reducing remaining `app/` ownership in base controller and CRUD support layers
- deciding whether `app/Views` stays as long-term compatibility or becomes a formal mapped resource path
