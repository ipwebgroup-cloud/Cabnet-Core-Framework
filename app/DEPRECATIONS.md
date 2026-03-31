# Legacy Layer Deprecations

This file marks legacy classes that should gradually stop being used as the `src/` layer becomes the preferred architecture.

## Deprecated direction targets

### Controllers moving to `src/Application`
- `app/Controllers/Admin/AuthController.php`
- `app/Controllers/Admin/DashboardController.php`
- `app/Controllers/Admin/ServiceController.php`
- `app/Controllers/Public/HomeController.php`
- `app/Controllers/Api/HealthController.php`

### Services moving to `src/Application`
- `app/Services/BaseService.php`
- `app/Services/AdminAuthService.php`
- `app/Services/ServiceCrudService.php`
- time/menu services

### Infrastructure moving to `src/Infrastructure`
- `app/Repositories/BaseRepository.php`
- `app/Repositories/ServiceRepository.php`
- `app/Core/Auth/DbUserProvider.php`

### Support moving to `src/Support`
- view state
- admin menu

## Rule

Do not create new major framework features in legacy `app/` when a corresponding `src/` layer already exists.

When compatibility is required, keep legacy classes as thin shims over the canonical `src/` implementation.
