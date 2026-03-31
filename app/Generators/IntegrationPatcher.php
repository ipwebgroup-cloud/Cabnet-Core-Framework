<?php
declare(strict_types=1);

final class IntegrationPatcher
{
    public function buildPatches(array $blueprint): array
    {
        $entityKey = (string)($blueprint['entity_key'] ?? 'items');
        $singularLabel = (string)($blueprint['singular_label'] ?? 'Item');
        $pluralLabel = (string)($blueprint['plural_label'] ?? 'Items');

        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        $controllerClass = $classBase . 'Controller';
        $repositoryClass = $classBase . 'Repository';
        $serviceClass = $classBase . 'CrudService';

        $routePatch = implode(PHP_EOL, [
            "        ['method' => 'GET', 'path' => '/{$routeBase}', 'handler' => [{$controllerClass}::class, 'index'], 'name' => 'admin.{$routeBase}.index'],",
            "        ['method' => 'GET', 'path' => '/{$routeBase}/create', 'handler' => [{$controllerClass}::class, 'createForm'], 'name' => 'admin.{$routeBase}.create'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}', 'handler' => [{$controllerClass}::class, 'store'], 'name' => 'admin.{$routeBase}.store'],",
            "        ['method' => 'GET', 'path' => '/{$routeBase}/{id}/edit', 'handler' => [{$controllerClass}::class, 'editForm'], 'name' => 'admin.{$routeBase}.edit'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}/{id}/update', 'handler' => [{$controllerClass}::class, 'update'], 'name' => 'admin.{$routeBase}.update'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}/{id}/delete', 'handler' => [{$controllerClass}::class, 'destroy'], 'name' => 'admin.{$routeBase}.delete'],",
            "",
        ]);

        $servicePatch = implode(PHP_EOL, [
            "    '{$singularBase}Repository' => function (App \$app): {$repositoryClass} {",
            "        return new {$repositoryClass}(\$app->service('db'));",
            "    },",
            "",
            "    '{$singularBase}Crud' => function (App \$app): {$serviceClass} {",
            "        return new {$serviceClass}(",
            "            \$app->service('{$singularBase}Repository'),",
            "            \$app->validator()",
            "        );",
            "    },",
            "",
        ]);

        $sidebarPatch = implode(PHP_EOL, [
            "            <a class="nav-link text-white <?= str_starts_with(\$currentPath, '/{$routeBase}') ? 'fw-bold' : '' ?>" href="/{$routeBase}">{$pluralLabel}</a>",
            "",
        ]);

        $controllerRequire = "require_once BASE_PATH . '/app/Controllers/Admin/{$controllerClass}.php';";
        $repositoryRequire = "require_once BASE_PATH . '/app/Repositories/{$repositoryClass}.php';";
        $serviceRequire = "require_once BASE_PATH . '/app/Services/{$serviceClass}.php';";
        $definitionRequire = "require_once BASE_PATH . '/app/Crud/Definitions/{$classBase}EntityDefinition.php';";

        return [
            'generated/integration_patches/routes.patch.txt' => $routePatch,
            'generated/integration_patches/services.patch.txt' => $servicePatch,
            'generated/integration_patches/sidebar.patch.txt' => $sidebarPatch,
            'generated/integration_patches/bootstrap_requires.patch.txt' => implode(PHP_EOL, [
                $definitionRequire,
                $controllerRequire,
                $repositoryRequire,
                $serviceRequire,
                '',
            ]),
        ];
    }

    private function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $value = ucwords($value);
        return str_replace(' ', '', $value);
    }

    private function singularize(string $value): string
    {
        return str_ends_with($value, 's') ? substr($value, 0, -1) : $value;
    }

    private function pluralize(string $value): string
    {
        return str_ends_with($value, 's') ? $value : $value . 's';
    }
}
