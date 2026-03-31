<?php
declare(strict_types=1);

return [
    'version' => '2.9.0',
    'release_name' => 'Module Registry Adoption',
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
        'src_http_runtime' => true,
        'src_session_runtime' => true,
        'src_url_generator' => true,
        'src_crud_definition_model' => true,
        'crud_module_registry' => true,
        'crud_module_runtime_bootstrap' => true,
        'crud_module_menu_bootstrap' => true,
        'crud_module_generator_integration' => true,
    ],
];
