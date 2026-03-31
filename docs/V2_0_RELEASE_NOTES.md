# V2_0_RELEASE_NOTES.md

## Cabnet Core Framework v2.0.0

### Release type
Consolidation release

### Summary
This release stabilizes the framework direction after the migration-heavy v1.x series.

### Main outcomes
- preferred architecture is now explicitly `src/`-first
- legacy layer is formally documented as transitional
- runtime, auth, one full CRUD module, support utilities, and tests are now represented in the new structure
- documentation now reflects governance and migration status more clearly

### Recommended usage
Use v2.0 as:
- the baseline repository for future projects
- the base for project-specific framework forks
- the stable source-of-truth framework bundle

### Important note
v2.0 is not “finished forever.” It is the point where the framework becomes organized enough to evolve intentionally.
