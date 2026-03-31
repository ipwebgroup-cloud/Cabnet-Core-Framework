<?php
declare(strict_types=1);

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
