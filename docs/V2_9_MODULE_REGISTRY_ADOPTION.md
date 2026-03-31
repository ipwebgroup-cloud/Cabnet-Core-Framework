# V2_9_MODULE_REGISTRY_ADOPTION.md

## Summary

v2.9 makes `config/modules.php` an active runtime integration seam instead of a passive metadata file.

## Main changes

- added `src/Application/Crud/CrudModuleBootstrap.php`
- expanded `CrudModuleRegistry` to expose route, service, controller, and menu-relevant metadata
- expanded `CrudEntityDefinition` with input default/payload helpers
- shifted admin CRUD route registration in `bootstrap/routes.php` to module-driven bootstrapping
- shifted CRUD repository/service registration in `bootstrap/services.php` to module-driven bootstrapping
- shifted admin menu CRUD entries in `config/admin_menu.php` to module-driven bootstrapping
- converted `ServiceController` into a thin `moduleKey()` wrapper over the shared src CRUD base controller
- updated generator output to emit module config snippets for `config/modules.php`
- expanded smoke coverage around module-driven services, routes, and menu registration

## Why this phase matters

This removes one of the remaining high-friction integration seams in the framework. CRUD metadata now drives more of the real runtime behavior, which makes the framework safer to extend and easier to scaffold without manually repeating the same registration work in multiple files.

## Compatibility direction

- legacy/global CRUD aliases remain intact
- legacy/global admin CRUD controller shims remain intact
- built-in `services` module keeps the same URLs and service keys

## Recommended next move

Converge validation and form metadata more deeply so CRUD services and controllers can derive more behavior directly from canonical field definitions.
