# ROUTE_HELPERS.md

## Purpose

This phase introduces the first named-route infrastructure layer.

## Components

- `RouteRegistry`
- `UrlService::route()`

## Goal

Move the framework away from scattered hardcoded paths and toward reusable route generation.

## Current status

The registry and helper are in place. A future phase should fully wire route names from the route definitions automatically.

## Example target usage

```php
$app->url()->route('admin.services.edit', ['id' => 5]);
```

This should resolve to:

```text
/services/5/edit
```

## Next step

Add route names directly into `bootstrap/routes.php` and expose them to the registry automatically.
