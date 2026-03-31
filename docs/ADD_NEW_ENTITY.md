# ADD_NEW_ENTITY.md

## Preferred flow

1. define or generate the entity definition in `src/Application/Crud/Definitions`
2. add the module metadata block to `config/modules.php`
3. review the generated repository and service
4. rely on metadata-driven validation and form rendering first
5. only add custom controller/service logic where the generic CRUD flow is not enough

## Field metadata checklist

For each field, decide:

- type
- label
- required
- default
- min/max
- options for selects
- placeholder/help for admin forms
- explicit rules only when inference is insufficient

## v3.0 note

With v3.0, canonical field metadata is the preferred source for both validation and CRUD form behavior.
