<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

final class ClockService
{
    public function now(): string
    {
        return date('c');
    }
}
