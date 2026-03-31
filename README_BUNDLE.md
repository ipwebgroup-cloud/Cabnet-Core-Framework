# README_BUNDLE.md

## Current bundle

- Version: v3.4.0
- Release: Module Permissions and Filter Metadata
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- module-scoped CRUD action permissions now live in `config/modules.php`
- registry-driven list filters now live in `config/modules.php`
- shared CRUD list views now render metadata-driven filters
- admin menu visibility now respects role-aware menu metadata through the service layer
