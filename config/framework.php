<?php
declare(strict_types=1);

return [
    'version' => '2.5.0',
    'release_name' => 'Legacy Runtime Reduction',
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
    ],
];
