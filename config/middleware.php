<?php
declare(strict_types=1);

return [
    'aliases' => [
        'session' => StartSessionMiddleware::class,
        'admin.auth' => AdminAuthMiddleware::class,
    ],
    'groups' => [
        'admin' => ['session', 'admin.auth'],
        'public' => ['session'],
        'api' => [],
    ],
];
