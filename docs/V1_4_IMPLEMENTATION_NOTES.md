# V1_4_IMPLEMENTATION_NOTES.md

## Important note

Version 1.4 is the first pack where the front controllers prefer the new kernel path.

## What changed

### Preferred entry path
Public, admin, and API entry points now call:

- `bootstrap_kernel()`

instead of directly calling:
- `bootstrap_app()->run()`

### Config loading
A dedicated `ConfigLoader` now centralizes framework configuration loading.

### Legacy bridge
`LegacyAppFactory` keeps the existing app/services/controllers usable while the new kernel takes over request orchestration.

### CLI improvement
A helper script now exists to create DB-backed admin users:
- `scripts/create-admin-user.php`
