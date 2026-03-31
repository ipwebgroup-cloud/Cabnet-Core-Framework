# V2_5_LEGACY_RUNTIME_REDUCTION.md

## Goal

Reduce runtime duplication and move controller base ownership into `src/` without breaking the hybrid framework.

## What changed

### 1. Canonical controller base classes now live in `src/`

Added:

- `src/Application/Controllers/BaseController.php`
- `src/Application/Controllers/Admin/BaseCrudController.php`

These are now the preferred base classes for new namespaced controllers.

### 2. Legacy global controller bases became compatibility shims

Updated:

- `app/Controllers/BaseController.php`
- `app/Controllers/Admin/BaseCrudController.php`

They now extend the canonical `src/` classes instead of owning the logic themselves.

### 3. Existing src controllers now follow the src base path

Updated:

- `src/Application/Controllers/PublicSite/HomeController.php`
- `src/Application/Controllers/Admin/AuthController.php`
- `src/Application/Controllers/Admin/DashboardController.php`
- `src/Application/Controllers/Admin/ServiceController.php`

This reduces controller duplication and keeps new work on the preferred architecture path.

### 4. Generator output now follows the new base controller path

Updated:

- `src/Generators/CrudScaffoldWriter.php`
- `src/Generators/Templates/controller.stub`

Generated admin controllers now extend the canonical namespaced CRUD base controller.

### 5. `AppRuntime` is now a compatibility facade over the active kernel path

Updated:

- `src/AppRuntime.php`

Instead of keeping a second route-dispatch implementation, `AppRuntime` now normalizes its inputs and delegates to `src/Bootstrap/Kernel`.

This keeps the old runtime entry available for compatibility while reducing duplicate execution logic.

## Why this phase matters

Before this phase:

- controller base ownership still lived in `app/`
- namespaced controllers depended on legacy global base classes
- `AppRuntime` duplicated route/middleware/controller dispatch already handled by `Kernel`

After this phase:

- canonical controller base ownership is in `src/`
- legacy base controllers remain available as thin shims
- `AppRuntime` no longer maintains a second runtime path
- generator output reinforces the modern path instead of the transitional one

## Safety posture

This phase intentionally does **not** remove:

- global controller class names
- `app/Views`
- legacy service/repository base classes
- the legacy app container

Those remain for compatibility until later convergence phases.

## Validation

Smoke tests expanded and passed:

- `app_runtime_named_routes_match_kernel_context`
- `src_service_controller_uses_src_crud_base`

Full smoke result after this phase:

- Passed: 12
- Failed: 0

## Next strongest move

The next strongest phase is:

**service/repository convergence**

That phase should:

- move canonical base service ownership into `src/Application/Services`
- move canonical base repository ownership into `src/Infrastructure/Repositories`
- keep `app/Services/BaseService.php` and `app/Repositories/BaseRepository.php` as shims
- reduce remaining generator dependence on legacy global service/repository bases
