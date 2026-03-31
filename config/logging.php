<?php
declare(strict_types=1);

return [
    'default' => 'file',
    'channels' => [
        'file' => [
            'driver' => 'file',
            'path' => BASE_PATH . '/storage/logs/app.log',
            'level' => 'error',
        ],
    ],
];
