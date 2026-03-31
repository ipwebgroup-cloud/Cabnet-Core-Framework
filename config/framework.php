<?php
declare(strict_types=1);

return [
    'version' => '2.6.0',
    'release_name' => 'Service Repository Convergence',
    'feature_flags' => [
        'db_auth' => true,
        'twig_renderer' => false,
        'named_routes' => true,
        'admin_menu_config' => true,
        'migration_runner' => true,
        'file_logging' => true,
        'src_first_generators' => true,
        'smoke_test_runner' => true,
        'src_view_renderer' => true,
        'src_controller_bases' => true,
        'src_service_bases' => true,
        'src_repository_bases' => true,
    ],
];
