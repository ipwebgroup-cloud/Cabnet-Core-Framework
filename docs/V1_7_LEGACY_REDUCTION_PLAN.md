# V1_7_LEGACY_REDUCTION_PLAN.md

## Objective

Cabnet Core v1.7 starts formal legacy reduction by migrating auth and shared support into the `src/` layer and documenting deprecation boundaries.

## Included in this pack

- `src/Application/Controllers/Admin/AuthController`
- `src/Application/Services/AdminAuthService`
- `src/Infrastructure/Auth/DbUserProvider`
- `src/Support/ViewState`
- `src/Support/AdminMenu`
- `app/DEPRECATIONS.md`

## Why this matters

The framework now has fewer reasons to add new logic to the legacy `app/` layer.

## Recommended v1.8 focus

- migrate remaining reusable CRUD base logic
- migrate rendering helpers toward `src/`
- add automated tests for migrated auth and CRUD flows
- begin removing duplicate legacy implementations where safe
