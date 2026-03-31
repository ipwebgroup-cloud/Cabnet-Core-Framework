# README_BUNDLE.md

## Current bundle

- Version: v3.6.0
- Release: Twig-Aware Generator Output
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- src-first CRUD generation now understands PHP and Twig presentation targets
- generated Twig admin stubs extend canonical src shared CRUD templates
- PHP generation remains the default so existing bootstrap and renderer behavior stay stable
