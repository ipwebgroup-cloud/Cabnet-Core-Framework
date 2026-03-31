<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

final class AdminMenuService
{
    public function __construct(private array $items = [])
    {
    }

    public function items(): array
    {
        return $this->items;
    }
}
