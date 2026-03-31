<?php
declare(strict_types=1);

namespace Cabnet\Support;

final class AdminMenu
{
    public function __construct(
        private array $items = [],
        private $visibilityResolver = null
    ) {
    }

    public function items(): array
    {
        return $this->items;
    }

    /** @return array<int, array<string, mixed>> */
    public function visibleFor(mixed $user): array
    {
        $role = is_array($user) && isset($user['role']) && is_string($user['role']) ? $user['role'] : null;

        return array_values(array_filter($this->items, function (array $item) use ($user, $role): bool {
            if (is_callable($this->visibilityResolver)) {
                $resolved = ($this->visibilityResolver)($item, $user);
                if (is_bool($resolved)) {
                    return $resolved;
                }
            }

            $roles = $item['roles'] ?? null;
            if ($roles === null) {
                return true;
            }

            if (is_string($roles) && $roles !== '') {
                $roles = [$roles];
            }

            if (!is_array($roles) || $roles === []) {
                return true;
            }

            if (in_array('*', $roles, true)) {
                return true;
            }

            return $role !== null && in_array($role, $roles, true);
        }));
    }
}
