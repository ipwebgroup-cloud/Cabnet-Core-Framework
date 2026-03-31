# README_BUNDLE.md

## Current bundle

- Version: v4.2.0
- Release: Blueprint Schema Validation
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- malformed CRUD blueprints now fail early with schema validation errors
- built-in examples can now be listed and resolved directly through the generator scripts
- the executable tree now matches the documented policy-hook and constructor-aware runtime behavior
