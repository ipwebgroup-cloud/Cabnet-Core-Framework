<?php

declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/bootstrap/app.php';
require_once __DIR__ . '/Support/SmokeAssert.php';
require_once __DIR__ . '/Support/ResponseInspector.php';
require_once __DIR__ . '/Support/TestEnvironment.php';
require_once __DIR__ . '/Smoke/FrameworkSmokeTest.php';
