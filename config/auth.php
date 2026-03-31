<?php
declare(strict_types=1);

return [
    'session_key' => 'cabnet_admin_user',
    'login_route' => '/login',
    'logout_route' => '/logout',
    'logout_method' => 'POST',
    'guard_admin_routes' => true,
    'allow_starter_credentials' => false,
    'csrf_protect_login' => true,
    'csrf_protect_logout' => true,
];
