# PROJECT_FORK_GUIDE.md

## Purpose

This guide explains how to fork Cabnet Core v2.0 into a real project.

## Recommended fork process

1. Duplicate the bundle directory.
2. Rename the project and adjust:
   - `config/app.php`
   - branding strings
   - database config
   - admin menu
3. Decide whether to keep or remove the sample `services` module.
4. Add project-specific schemas.
5. Generate new modules from blueprints where needed.
6. Run smoke and integration tests.
7. Create a project-specific `PROJECT_MASTER_CONTEXT.md`.

## Example project forks

- Cabnet Tours
- Cabnet Turbo
- Sage-style content portal
- service business catalog
- multilingual B2B catalog

## Important rule

The framework bundle remains the reusable base.
The fork becomes the product-specific implementation.
