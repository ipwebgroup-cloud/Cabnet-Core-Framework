# README_BUNDLE.md

## Current bundle

- Version: v4.1.0
- Release: Relation Filter Option Hydration
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- relation-backed list filters now hydrate select options from relation metadata at runtime
- CRUD form relation options and list-filter relation options now share the same hydration path
- scaffolded relation filters stay `select` controls even when their options are expected to be provided by database-backed relation metadata
