<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

interface CrudModulePolicy
{
    /**
     * Return true/false to make an explicit authorization decision.
     * Return null to fall back to role-based module permissions.
     *
     * @param array<string, mixed> $moduleMeta
     * @param array<string, mixed> $context
     */
    public function allows(
        string $moduleKey,
        string $action,
        mixed $user,
        array $moduleMeta,
        CrudEntityDefinition $definition,
        array $context = []
    ): ?bool;
}
