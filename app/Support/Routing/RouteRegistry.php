<?php
declare(strict_types=1);

final class RouteRegistry
{
    public function __construct(private array $namedRoutes = [])
    {
    }

    public function get(string $name): ?string
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function all(): array
    {
        return $this->namedRoutes;
    }
}
