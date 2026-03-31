# V2_6_SERVICE_REPOSITORY_CONVERGENCE.md

## Goal

Move canonical service and repository ownership into `src/` while preserving the legacy `app/` layer as a compatibility shell.

## What changed

### 1. Canonical base service ownership now lives in `src/`

Added:

- `src/Application/Services/BaseService.php`

This is now the preferred service base for new namespaced application services.

### 2. Canonical base repository ownership now lives in `src/`

Added:

- `src/Infrastructure/Repositories/BaseRepository.php`

This moves shared repository pagination/search behavior into the preferred `src/Infrastructure` path.

### 3. Legacy base classes became thin compatibility shims

Updated:

- `app/Services/BaseService.php`
- `app/Repositories/BaseRepository.php`

They now extend the canonical `src/` bases instead of owning the shared logic themselves.

### 4. Legacy concrete service/repository classes now shim to canonical src implementations

Updated:

- `app/Services/AdminAuthService.php`
- `app/Services/ServiceCrudService.php`
- `app/Repositories/ServiceRepository.php`
- `app/Core/Auth/DbUserProvider.php`

This removes duplicate runtime ownership for the currently migrated service/repository stack while keeping legacy class names available.

### 5. Canonical src classes now depend on canonical src bases

Updated:

- `src/Application/Services/ServiceCrudService.php`
- `src/Infrastructure/Repositories/ServiceRepository.php`

These classes no longer depend on legacy global base classes.

### 6. Src-first CRUD generation now emits src-owned base inheritance

Updated:

- `src/Generators/CrudScaffoldWriter.php`
- `src/Generators/Templates/service.stub`
- `src/Generators/Templates/repository.stub`

Generated src modules now extend `BaseService`, `BaseRepository`, and `BaseCrudController` from their own namespaced layer instead of falling back to legacy globals.

## Why this phase matters

Before this phase:

- shared service/repository base ownership still lived in `app/`
- canonical src services/repositories still extended legacy global bases
- legacy concrete auth/service/repository classes duplicated logic that already existed in `src/`
- src-first generators still emitted legacy-style base inheritance in generated files

After this phase:

- base service ownership is in `src/Application/Services`
- base repository ownership is in `src/Infrastructure/Repositories`
- legacy base classes remain available as thin shims
- canonical src services/repositories no longer depend on global legacy bases
- current legacy concrete service/repository classes are compatibility wrappers over canonical src implementations
- generated src CRUD modules reinforce the preferred architecture path

## Safety posture

This phase intentionally does **not** remove:

- the legacy `App` container
- legacy global class names
- `app/Views`
- the legacy generator mode (`--legacy`)
- the global `CrudEntityDefinition` layer

Those remain in place for incremental compatibility.

## Validation

Smoke tests expanded and passed:

- `legacy_service_repository_layer_remains_shimmed_to_src`
- `src_crud_generator_uses_namespaced_base_classes`

Full smoke result after this phase:

- Passed: 14
- Failed: 0

## Next strongest move

The next strongest phase after this one is:

**entity definition and integration-surface convergence**

That phase should:

- move canonical CRUD entity definition ownership into `src/Application/Crud`
- reduce remaining generator/runtime dependence on global `CrudEntityDefinition`
- keep `app/Crud/*` as transitional shims where needed
- widen smoke coverage around generator output and scaffold integration seams
