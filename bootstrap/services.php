<?php
declare(strict_types=1);

$serviceRegistry = new \Cabnet\Bootstrap\ServiceRegistry();
$services = $serviceRegistry->register();

return \Cabnet\Application\Crud\CrudModuleBootstrap::registerServices(
    $services,
    require BASE_PATH . '/config/modules.php'
);
