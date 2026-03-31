# V1_7_IMPLEMENTATION_NOTES.md

## Important note

Version 1.7 is about shrinking the legacy layer intentionally.

## What changed

### Auth migration
Admin auth routes now use the namespaced `src/` auth controller.

### Infrastructure migration
DB-backed user lookup now lives in:
- `src/Infrastructure/Auth/DbUserProvider`

### Shared support migration
View-state and admin-menu support now have namespaced `src/` versions.

### Governance change
The framework now includes `app/DEPRECATIONS.md` to mark the legacy layer as transitional rather than open-ended.
