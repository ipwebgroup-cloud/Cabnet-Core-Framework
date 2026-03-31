# V2_0_DEPRECATION_POLICY.md

## Purpose

This file defines how Cabnet Core handles remaining legacy code after v2.0.

## Policy

### Preferred location for new framework work
- `src/`

### Allowed use of `app/`
- compatibility
- legacy bridge logic
- non-migrated shared helpers until replaced

### Disallowed pattern
Do not introduce new major architecture in `app/` when there is already a corresponding `src/` domain.

## Deprecation categories

### Soft-deprecated
Still usable, but should not receive major new work.

### Pending removal
Has a stable `src/` replacement and can be removed in a future major version.

### Archived
Kept only for historical or compatibility reasons.

## Recommendation

For every future release:
- add a short migration note
- list what moved to `src/`
- list what legacy pieces became more deprecated
