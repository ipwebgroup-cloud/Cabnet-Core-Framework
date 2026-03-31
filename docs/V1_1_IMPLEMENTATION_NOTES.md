# V1_1_IMPLEMENTATION_NOTES.md

## Important note

Version 1.1 is a hardening pass, not a full rewrite.

That means:
- existing baseline code remains usable
- new hardening utilities are layered in
- some upgrades are introduced as optional integration points first

## Key architectural decisions

### Autoloading
Composer support is added through `composer.json`, but the framework still remains compatible with the current loading strategy.

### Logging and error handling
A dedicated logger and `ErrorHandler` are now available to support safer production behavior.

### Authentication
A DB-backed path is now available through:
- `DbUserProvider`
- `AdminAuthService`
- `database/schema/users.sql`

### Menu config
Admin navigation can now move toward `config/admin_menu.php` instead of hardcoded layout links.

### Migration support
A simple migration runner is included for SQL-based project upgrades.
