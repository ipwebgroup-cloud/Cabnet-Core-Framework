# README_BUNDLE.md

## Current bundle

- Version: v4.0.0
- Release: Runtime Dependency-Injection Bridge
- Delivery mode: patch-only overlay

## Apply order

1. overlay this patch onto the latest framework tree
2. read `FRAMEWORK_STATUS.json`
3. read `docs/FRAMEWORK_HANDOFF.md`
4. read `PATCH_MANIFEST.txt`
5. use `HANDOFF_PROMPT.txt` when starting a new chat

## Highlights

- runtime controller and middleware construction can now use lightweight constructor injection without replacing the legacy `App`
- the app service map now publishes typed service bindings so common runtime services can be resolved by class/interface
- the bridge remains compatibility-first because route and middleware dispatch still fall back to direct instantiation if constructor-aware resolution cannot be completed
