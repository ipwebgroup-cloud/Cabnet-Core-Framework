# README_BUNDLE.md

## Current bundle

- Version: v4.3.0
- Release: Service Registry Formalization
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- runtime service definitions are now centralized in `src/Bootstrap/ServiceRegistry.php`
- typed runtime lookup now prefers the formal service registry instead of depending only on `__service_types`
- legacy alias-array fallback is still preserved for compatibility
