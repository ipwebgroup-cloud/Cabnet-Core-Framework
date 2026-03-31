# V2.7 HTTP / Runtime Convergence

## Release intent

V2.7 reduces the remaining runtime split between the legacy `app/` layer and the preferred `src/` architecture.

The goal is **not** a risky rewrite. The goal is to move canonical ownership of HTTP/runtime helpers into `src/` while keeping the legacy global classes available as compatibility shims.

---

## What changed

### Canonical runtime ownership now lives in `src/`

Added canonical runtime/support classes:

- `src/Http/Request.php`
- `src/Http/Response.php`
- `src/Http/ResponseResolver.php`
- `src/Http/ResponseEmitter.php`
- `src/Session/Session.php`
- `src/Session/Flash.php`
- `src/Security/Csrf.php`
- `src/Routing/RouteRegistry.php`
- `src/Routing/RouteDispatcher.php`
- `src/Support/UrlGenerator.php`

### Legacy runtime classes converted into wrappers/shims

The following legacy classes now narrow down to compatibility wrappers over `src/` runtime ownership:

- `app/Core/Request.php`
- `app/Core/Response.php`
- `app/Core/Router.php`
- `app/Core/Session/Session.php`
- `app/Core/Session/Flash.php`
- `app/Core/Security/Csrf.php`
- `app/Support/Routing/RouteRegistry.php`
- `app/Services/UrlService.php`
- `app/Support/ViewState.php`

### App container now resolves src-first runtime contracts

`app/Core/App.php` now depends on canonical runtime contracts from `src/` for:

- request
- response
- router
- session
- flash
- csrf
- url generation
- form/view state

The `App` object still preserves compatibility for older code, but the type direction is now clearly `src` first.

### Kernel runtime pipeline simplified

`src/Bootstrap/Kernel.php` now uses dedicated runtime helpers for:

- middleware execution
- route dispatch
- response normalization
- response emission

This reduces dispatch duplication and makes the runtime flow more reusable.

### Generator alignment

The src CRUD scaffold writer now generates controller methods with canonical `\Cabnet\Http\Response` return types.

---

## Why this phase matters

Before V2.7, the framework already had strong `src/` ownership for rendering, controllers, services, and repositories.

The biggest remaining architectural drag was the request/response/session/URL runtime boundary still leaning on legacy globals.

V2.7 improves that without breaking compatibility:

- `src/` becomes the clearer owner of runtime behavior
- `app/` becomes thinner and more honest as a transitional layer
- public/admin/API runtime behavior is more consistent
- redirect and post/redirect/get style flows are easier to standardize
- future generator/entity convergence work can target one runtime model

---

## Smoke coverage added

Added runtime-focused smoke assertions for:

- legacy runtime shims extending src runtime classes
- app container resolving src runtime services
- named-route URL generation for admin edit routes
- invalid create/update CSRF redirects returning to the expected route

Smoke suite status after V2.7:

- **19 passed**
- **0 failed**

---

## Compatibility stance

This release remains intentionally conservative.

No public/admin/API route paths were changed.
No required project config keys were removed.
No legacy runtime class names were deleted.

Projects depending on legacy globals should continue working while newer framework work should target `src/` first.

---

## Best next phase

The strongest next phase after V2.7 is:

# V2.8 — CRUD metadata / entity-definition convergence

That phase should:

- move canonical entity definition ownership into `src/`
- make generators and CRUD controllers depend on one metadata source of truth
- thin legacy CRUD definition classes into wrappers
- reduce remaining ambiguity in scaffolded module structure
