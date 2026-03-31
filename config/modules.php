<?php
declare(strict_types=1);

return [
    'services' => [
        'enabled' => true,
        'label' => 'Services',
        'singular_label' => 'Service',
        'route_prefix' => '/services',
        'table' => 'services',
        'definition_class' => \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::class,
        'controller_class' => \Cabnet\Application\Controllers\Admin\ServiceController::class,
        'repository_class' => \Cabnet\Infrastructure\Repositories\ServiceRepository::class,
        'service_class' => \Cabnet\Application\Services\ServiceCrudService::class,
        'repository_service' => 'serviceRepository',
        'crud_service' => 'serviceCrud',
        'admin_route_base' => 'admin.services',
        'admin_view_path' => 'admin/services',
        'admin_middleware' => ['session', 'admin.auth'],
        'permissions' => [
            'view' => ['admin', 'editor'],
            'create' => ['admin'],
            'edit' => ['admin'],
            'delete' => ['admin'],
        ],
        'show_in_admin_menu' => true,
        'filters' => [
            'status' => [
                'field' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'placeholder' => 'All statuses',
            ],
        ],
        'generator_target' => 'src',
    ],
];
