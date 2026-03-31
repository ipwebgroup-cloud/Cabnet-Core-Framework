<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

if (is_file(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
}

require_once BASE_PATH . '/bootstrap/app.php';

if ($argc < 4) {
    echo "Usage: php scripts/create-admin-user.php <name> <username> <password> [role]\n";
    exit(1);
}

[$script, $name, $username, $password] = $argv;
$role = $argv[4] ?? 'admin';

$app = bootstrap_app('admin');
$db = $app->service('db');

$hash = password_hash($password, PASSWORD_DEFAULT);

$db->execute(
    'INSERT INTO users (name, username, password_hash, role, created_at, updated_at) VALUES (:name, :username, :password_hash, :role, NOW(), NOW())',
    [
        'name' => $name,
        'username' => $username,
        'password_hash' => $hash,
        'role' => $role,
    ]
);

echo "Admin user created: {$username}\n";
