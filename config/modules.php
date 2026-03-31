<?php
declare(strict_types=1);

return [
    'services' => [
        'enabled' => true,
        'label' => 'Services',
        'route_prefix' => '/services',
        'table' => 'services',
        'definition_class' => \Cabnet\Application\Crud\Definitions\ServiceEntityDefinition::class,
        'controller_class' => \Cabnet\Application\Controllers\Admin\ServiceController::class,
        'repository_service' => 'serviceRepository',
        'crud_service' => 'serviceCrud',
        'admin_route_base' => 'admin.services',
        'admin_view_path' => 'admin/services',
        'generator_target' => 'src',
    ],
];
