# V3_4_MODULE_PERMISSIONS_FILTER_METADATA.md

## Phase summary

v3.4 makes `config/modules.php` expressive enough to describe two more pieces of runtime behavior without controller duplication:

- per-action CRUD access roles
- list-filter metadata for registry-driven admin listings

## What changed

- `permissions` metadata can now declare allowed roles for `view`, `create`, `edit`, and `delete`
- `filters` metadata can now declare list filters without hard-coding form controls in each module view
- `BaseCrudController` now enforces module action permissions before rendering or mutating records
- the shared admin CRUD list views now render filter controls from module metadata
- list pagination now preserves active filter query state
- `BaseController` now prefers the `adminMenu` service so role-aware menu filtering actually applies at render time

## Metadata example

```php
'services' => [
    'permissions' => [
        'view' => ['admin', 'editor'],
        'create' => ['admin'],
        'edit' => ['admin'],
        'delete' => ['admin'],
    ],
    'filters' => [
        'status' => [
            'field' => 'status',
            'label' => 'Status',
            'type' => 'select',
            'placeholder' => 'All statuses',
        ],
    ],
],
```

## Compatibility notes

- modules without `permissions` fall back to `admin`
- modules without `filters` keep the existing search-only list behavior
- layouts still receive a simple `adminMenuItems` array; the filtering logic now happens earlier through the `adminMenu` service
