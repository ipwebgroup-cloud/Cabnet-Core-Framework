# README_BUNDLE.md

## Current bundle

- Version: v3.7.0
- Release: Lightweight Policy Hooks
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- CRUD modules can now attach optional policy hooks while keeping role arrays as the safe default
- admin menu visibility can now follow module policy decisions instead of relying only on static roles
- generated module config stubs can now preserve optional `policy_class` metadata
