# README_BUNDLE.md

## Current bundle

- Version: v3.5.0
- Release: Richer Field Metadata
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- CRUD field metadata now supports uploads, relation-driven selects, and translatable inputs
- request input now merges uploaded files into the canonical CRUD payload
- definition-driven CRUD services can hydrate relation options and persist uploads without controller rewrites
