# V1_5_SERVICE_CONTROLLER_MIGRATION_PLAN.md

## Objective

Cabnet Core v1.5 begins migrating active controllers and service objects from the legacy `app/` layer into the `src/` application layer.

## Included in this pack

- `src/Application/Controllers/Public/HomeController`
- `src/Application/Controllers/Admin/DashboardController`
- `src/Application/Controllers/Api/HealthController`
- `src/Application/Services/ClockService`
- `src/Application/Services/AdminMenuService`
- `src/Bootstrap/ServiceRegistry`

## What changed in runtime behavior

The main route definitions for:
- public home
- admin dashboard
- health endpoints

now point to namespaced `src/` controllers instead of legacy `app/` controllers.

## Why this matters

This is the first phase where active user-facing routes are now being served by the new application layer, not just the new runtime shell.

## Recommended v1.6 focus

- migrate admin auth controller
- migrate service CRUD controller and service
- begin moving repositories into `src/Infrastructure`
- introduce integration tests for migrated routes
