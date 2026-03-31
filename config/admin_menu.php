<?php
declare(strict_types=1);

return [
    [
        'label' => 'Dashboard',
        'path' => '/',
        'match' => '/',
    ],
    [
        'label' => 'Services',
        'path' => '/services',
        'match' => '/services',
    ],
    [
        'label' => 'Logout',
        'path' => '/logout',
        'match' => '/logout',
        'method' => 'POST',
        'requires_auth' => true,
    ],
];
