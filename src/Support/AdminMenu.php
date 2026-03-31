<?php
declare(strict_types=1);

namespace Cabnet\Support;

final class AdminMenu
{
    public function __construct(private array $items = [])
    {
    }

    public function items(): array
    {
        return $this->items;
    }
}
