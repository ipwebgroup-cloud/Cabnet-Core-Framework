<?php
declare(strict_types=1);

namespace Cabnet\Application\Controllers\Admin;

final class ServiceController extends BaseCrudController
{
    protected function moduleKey(): string
    {
        return 'services';
    }
}
