# v3.2 — Shared Layout and Partial Convergence

## Summary

This phase moves the shipped PHP layout shells, flash partial, CRUD partials, and baseline built-in admin/public views into `src/Presentation/Views/php` as the canonical presentation layer.

`app/Views/php` remains available, but the built-in files now act as compatibility shims that include the src-owned templates.

## Why this phase

The framework already preferred `src/Presentation/Views/*` during layered template resolution, and the generator already emitted new admin CRUD views into `src`.

The remaining gap was that the framework's own shared PHP layouts, partials, and built-in page views were still owned by `app/Views/php`. That meant the runtime preference and the shipped canonical templates were not fully aligned.

## Implemented changes

- Added canonical src-owned PHP layouts:
  - `src/Presentation/Views/php/layouts/admin.php`
  - `src/Presentation/Views/php/layouts/public.php`
- Added canonical src-owned shared partial:
  - `src/Presentation/Views/php/partials/flash.php`
- Added canonical src-owned CRUD partials:
  - `src/Presentation/Views/php/admin/crud/index_table.php`
  - `src/Presentation/Views/php/admin/crud/form_page.php`
  - `src/Presentation/Views/php/admin/crud/form_fields.php`
- Added canonical src-owned built-in PHP views:
  - `src/Presentation/Views/php/admin/dashboard.php`
  - `src/Presentation/Views/php/admin/login.php`
  - `src/Presentation/Views/php/admin/services/index.php`
  - `src/Presentation/Views/php/admin/services/create.php`
  - `src/Presentation/Views/php/admin/services/edit.php`
  - `src/Presentation/Views/php/public/home.php`
- Converted matching `app/Views/php/*` files into compatibility shims that include the src-owned equivalents.
- Expanded smoke coverage for:
  - canonical shared template resolution
  - built-in src module view resolution
  - app-layer shim delegation

## Result

The shipped PHP presentation baseline is now materially src-owned rather than only src-preferred. This reduces ambiguity for future work and gives generators, new modules, and handoff documentation a cleaner default.

## Compatibility

No public/admin route changes were introduced.
No controller signatures changed.
No service container keys changed.

Existing direct includes to the old `app/Views/php/*` files remain functional because those files now delegate to `src`.
