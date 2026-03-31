# V2_0_CONSOLIDATION_PLAN.md

## Objective

Cabnet Core v2.0 is a consolidation release.

The purpose is to stop uncontrolled drift between the legacy `app/` layer and the preferred `src/` layer, and to define the framework's stable direction clearly.

## Stable direction after v2.0

### Preferred layers
- `src/Bootstrap`
- `src/Application`
- `src/Infrastructure`
- `src/View`
- `src/Support`
- `src/Testing`

### Transitional compatibility layer
- `app/`

## Main v2.0 goals

- define preferred architecture boundaries
- centralize migration policy
- mark legacy layer status clearly
- provide a stable repository-style bundle
- keep the framework usable while reducing ambiguity

## What v2.0 does not attempt

- full deletion of legacy code
- full namespaced rewrite of every remaining class
- total replacement of all PHP views
- enterprise-grade test coverage

Those should happen incrementally after this release.

## Recommended next step after v2.0

Choose one:

1. **Framework cleanup track**
   - remove replaced legacy controllers/services
   - migrate rendering fully to `src/`
   - deepen tests

2. **Project fork track**
   - use v2.0 as the base for a real product starter
   - create a project-specific fork for Tours or Turbo
