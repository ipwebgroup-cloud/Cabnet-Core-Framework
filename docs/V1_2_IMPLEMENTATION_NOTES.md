# V1_2_IMPLEMENTATION_NOTES.md

## Important note

This is a transitional modernization pack.

It intentionally keeps the current framework runnable while adding the structure needed for a cleaner future core.

## What changed

### Autoloading
Composer now points toward a PSR-4 namespace root:
- `Cabnet\Core\` => `src/`

### Namespaced core direction
The new `src/` layer includes namespaced examples for:
- logger interface
- file logger
- error handler
- route definition
- generator template renderer

### Route middleware metadata
Routes now declare `middleware` arrays directly.

### Generator cleanup direction
Stub-template files now exist under:
- `src/Generators/Templates/`

This is the first step away from heavy string-built code generation.
