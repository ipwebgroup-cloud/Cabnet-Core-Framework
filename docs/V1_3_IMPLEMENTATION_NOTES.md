# V1_3_IMPLEMENTATION_NOTES.md

## Important note

Version 1.3 is a hybrid migration phase.

The framework now contains both:
- legacy runtime classes in `app/`
- future runtime classes in `src/`

This is intentional.

## What changed

### Active runtime
The legacy `App` now resolves route-specific middleware metadata and executes route-level middleware aliases.

### Future runtime
The `src/` layer now contains executable runtime pieces:
- HTTP request
- HTTP response
- route definition
- route resolution
- router
- middleware executor

### Why this is safe
The old runtime is still present, so the framework remains recoverable while modernization continues incrementally.
