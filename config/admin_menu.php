<?php
declare(strict_types=1);

$items = [
    [
        'label' => 'Dashboard',
        'path' => '/',
        'match' => '/',
    ],
    [
        'label' => 'Logout',
        'path' => '/logout',
        'match' => '/logout',
        'method' => 'POST',
        'requires_auth' => true,
    ],
];

return \Cabnet\Application\Crud\CrudModuleBootstrap::appendAdminMenu(
    $items,
    require BASE_PATH . '/config/modules.php'
);
