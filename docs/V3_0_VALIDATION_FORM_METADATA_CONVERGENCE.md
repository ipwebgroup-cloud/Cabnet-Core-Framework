# V3_0_VALIDATION_FORM_METADATA_CONVERGENCE.md

## Summary

v3.0 converges CRUD validation and admin form behavior around the canonical field metadata stored in `CrudEntityDefinition`.

## What changed

- `CrudEntityDefinition` now derives validation rules from field metadata
- the validator now supports `in:` rules for select option enforcement
- `DefinitionCrudService` centralizes shared create/update validation flow
- `ServiceCrudService` now acts as a thin wrapper over the shared definition-driven service base
- shared admin CRUD form partials now render placeholders, help text, required flags, and length attributes from field metadata
- the src CRUD generator now emits richer field metadata and definition-driven services

## Why this phase matters

This removes one of the last duplicated seams in the CRUD stack:
- definitions described the fields
- services repeated the rules
- forms repeated the UX hints

Now the canonical definition can drive all three more consistently.
