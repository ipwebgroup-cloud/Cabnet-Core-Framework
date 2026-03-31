# V1_1_HARDENING_PLAN.md

## Objective

Cabnet Core v1.1 focuses on hardening the baseline framework rather than expanding surface area blindly.

## Included in this pack

- Composer project file
- environment example file
- framework/module/admin-menu config split
- file logger
- centralized error handler
- database-backed auth service layer
- users schema
- migration runner
- FormRequest base
- config-driven admin menu foundation

## Recommended next steps after v1.1

- replace demo login flow with DB-backed login flow in the controller
- add password-hash seeder or CLI user creation tool
- expand router to support route middleware metadata natively
- introduce namespaces + PSR-4 in v1.2
- convert generator templates from string-heavy logic to stub-file rendering

## What remains intentionally conservative

This pack still preserves the existing starter framework structure to avoid a breaking rewrite.
