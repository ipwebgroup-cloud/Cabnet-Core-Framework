<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

final class ConfigLoader
{
    public function load(string $basePath): array
    {
        return [
            'app' => require $basePath . '/config/app.php',
            'framework' => require $basePath . '/config/framework.php',
            'modules' => require $basePath . '/config/modules.php',
            'admin_menu' => require $basePath . '/config/admin_menu.php',
            'logging' => require $basePath . '/config/logging.php',
            'middleware' => require $basePath . '/config/middleware.php',
            'database' => require $basePath . '/config/database.php',
            'auth' => require $basePath . '/config/auth.php',
            'mail' => require $basePath . '/config/mail.php',
            'seo' => require $basePath . '/config/seo.php',
            'storage' => require $basePath . '/config/storage.php',
        ];
    }
}
