# V2.2 Auth Hardening

## Objective

Reduce unsafe starter behavior in the admin authentication flow without breaking the current hybrid runtime.

## Implemented

- login form now includes a CSRF token
- login POST now validates CSRF by default
- starter credentials are disabled by default
- logout route now uses POST instead of GET
- logout POST validates CSRF by default
- admin shell renders logout as a form submission instead of a link
- login screen no longer pre-fills or advertises default credentials

## Compatibility

The previous starter credential fallback can still be explicitly re-enabled in `config/auth.php` by setting:

```php
'allow_starter_credentials' => true,
```

This is intended only for controlled local development.

## Notes

Projects should create a real admin user through:

```bash
php scripts/create-admin-user.php <name> <username> <password> [role]
```

## Recommended next step

Add a minimal smoke-test baseline for:

- kernel bootstrap
- route resolution
- admin auth guard behavior
- CSRF-protected login/logout flow
