<?php
declare(strict_types=1);

final class ServiceController extends BaseCrudController
{
    protected function moduleKey(): string
    {
        return 'services';
    }
}
