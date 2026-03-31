<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

require_once BASE_PATH . '/bootstrap/app.php';
require_once BASE_PATH . '/app/Core/Database/Migrator.php';

if ($argc < 2) {
    echo "Usage: php scripts/migrate.php <relative-sql-file>\n";
    exit(1);
}

$app = bootstrap_app('admin');
$migrator = new Migrator($app->service('db'));
$path = BASE_PATH . '/' . ltrim($argv[1], '/');
$migrator->runSqlFile($path);

echo "Migration executed: {$argv[1]}\n";
