# V3_7_LIGHTWEIGHT_POLICY_HOOKS.md

## Objective

Add a lightweight module-policy layer so projects can extend CRUD authorization beyond static role arrays without rewriting controllers or abandoning the existing safe fallback model.

## Why this phase

Before v3.7:

- module permissions were limited to role arrays in `config/modules.php`
- controller authorization and admin menu visibility were both ultimately driven by static role checks
- projects that needed slightly richer access rules had to drift into controller-specific customization

That made module authorization harder to extend cleanly while the framework was otherwise becoming more metadata-driven.

## What changed

- `src/Application/Crud/CrudModulePolicy.php`
  - introduces the canonical policy interface for module-level authorization hooks
- `src/Application/Crud/CrudModuleRegistry.php`
  - now supports `allowsForUser(...)`
  - can resolve optional `policy` or `policy_class` metadata
  - still falls back to role arrays when the policy returns `null`
- `src/Application/Controllers/Admin/BaseCrudController.php`
  - now routes authorization through the policy-aware registry path
  - passes lightweight surface context such as index, create, store, edit, update, and delete
- `src/Application/Crud/CrudModuleBootstrap.php`
  - now tags generated admin menu items with module metadata for policy-aware visibility checks
- `src/Support/AdminMenu.php`
  - now supports an optional visibility resolver so menu filtering can use policy-aware access decisions
- `bootstrap/services.php`
  - now wires the admin menu through the module registry for policy-aware visibility
- `src/Generators/CrudScaffoldWriter.php`
  - now preserves optional `policy_class` metadata in generated module config stubs
- `tests/Smoke/FrameworkSmokeTest.php`
  - now verifies registry policy expansion, policy-aware menu visibility, and generator preservation of `policy_class`

## Compatibility notes

- role arrays remain the default and continue to work unchanged
- modules do not need a policy to keep working
- policy hooks are intentionally lightweight: they extend the current system instead of replacing it
- this phase does not attempt a full DI rewrite of middleware, controllers, or policies

## Example module metadata

```php
'products' => [
    'enabled' => true,
    'label' => 'Products',
    'permissions' => [
        'view' => ['admin', 'editor'],
        'create' => ['admin'],
        'edit' => ['admin'],
        'delete' => ['admin'],
    ],
    'policy_class' => \App\Policies\ProductPolicy::class,
],
```

## Next strongest move after v3.7

- generator/runtime parity cleanup
- module blueprint authoring examples
- runtime dependency-injection bridge
