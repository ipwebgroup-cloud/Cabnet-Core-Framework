# README_BUNDLE.md

## Current bundle

- Version: v3.9.0
- Release: Blueprint Library and Examples
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- CRUD and integration generators can now list built-in examples and resolve them by `example:<name>`
- canonical blueprint examples now demonstrate simple editorial CRUD, upload/media CRUD, and richer localized services with Twig, relations, translatable fields, permissions, and policy hooks
- blueprint authoring is now easier to continue safely across patch-only chat sessions because the framework ships with executable examples, not just prose
