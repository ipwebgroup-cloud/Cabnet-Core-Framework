# README_BUNDLE.md

## Current bundle

- Version: v3.8.0
- Release: Generator Metadata Parity Cleanup
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- CRUD generation now preserves more runtime-ready module metadata instead of hardcoding admin-only defaults
- scaffold blueprints can now drive access roles, explicit per-action permissions, admin middleware overrides, admin menu visibility, and list filters
- field-level filter shortcuts reduce manual config work after generation while leaving runtime behavior backward compatible
