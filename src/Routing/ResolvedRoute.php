<?php
declare(strict_types=1);

namespace Cabnet\Routing;

final class ResolvedRoute
{
    public function __construct(
        public readonly RouteDefinition $route,
        public readonly array $params = []
    ) {
    }
}
