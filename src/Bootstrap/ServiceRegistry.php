<?php
declare(strict_types=1);

namespace Cabnet\Bootstrap;

use Cabnet\Application\Services\AdminMenuService;
use Cabnet\Application\Services\ClockService;

final class ServiceRegistry
{
    public function register(object $app): void
    {
        // Transitional no-op registry hook.
        // This file marks the migration point where services start getting formalized in src/.
    }

    public function makeClockService(): ClockService
    {
        return new ClockService();
    }

    public function makeAdminMenuService(array $items): AdminMenuService
    {
        return new AdminMenuService($items);
    }
}
