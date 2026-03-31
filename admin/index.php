<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap/app.php';

$kernel = bootstrap_kernel('admin');
$kernel->run();
