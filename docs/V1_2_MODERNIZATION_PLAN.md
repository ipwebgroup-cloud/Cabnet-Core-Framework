# V1_2_MODERNIZATION_PLAN.md

## Objective

Cabnet Core v1.2 begins reducing long-term framework fragility.

## Main themes

- PSR-4 modernization path
- namespaced `src/` layer
- route middleware metadata
- cleaner generator evolution through stub templates

## Included in this pack

- Composer PSR-4 autoload direction
- namespaced logger and error handler examples in `src/`
- namespaced route definition value object
- stub-template renderer
- generator template stubs
- middleware config file
- route definitions upgraded with middleware metadata

## Why this matters

The baseline framework is now positioned for a gradual migration instead of a dangerous big-bang rewrite.

## Recommended v1.3 focus

- migrate runtime classes from `app/` to `src/`
- update router to execute route-specific middleware directly
- add CLI user creation/seeding
- expand stub-template generator to emit all code through template files
