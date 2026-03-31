<?php
declare(strict_types=1);

namespace Cabnet\Generators;

final class IntegrationPatcher
{
    public function buildPatches(array $blueprint): array
    {
        $entityKey = (string)($blueprint['entity_key'] ?? 'items');
        $pluralLabel = (string)($blueprint['plural_label'] ?? 'Items');

        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        $controllerClass = $classBase . 'Controller';
        $repositoryClass = $classBase . 'Repository';
        $serviceClass = $classBase . 'CrudService';

        $routePatch = implode(PHP_EOL, [
            "        ['method' => 'GET', 'path' => '/{$routeBase}', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'index'], 'name' => 'admin.{$routeBase}.index'],",
            "        ['method' => 'GET', 'path' => '/{$routeBase}/create', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'createForm'], 'name' => 'admin.{$routeBase}.create'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'store'], 'name' => 'admin.{$routeBase}.store'],",
            "        ['method' => 'GET', 'path' => '/{$routeBase}/{id}/edit', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'editForm'], 'name' => 'admin.{$routeBase}.edit'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}/{id}/update', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'update'], 'name' => 'admin.{$routeBase}.update'],",
            "        ['method' => 'POST', 'path' => '/{$routeBase}/{id}/delete', 'handler' => [\\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class, 'destroy'], 'name' => 'admin.{$routeBase}.delete'],",
            '',
        ]);

        $servicePatch = implode(PHP_EOL, [
            "    '{$singularBase}Repository' => function (App \$app): \\Cabnet\\Infrastructure\\Repositories\\{$repositoryClass} {",
            "        return new \\Cabnet\\Infrastructure\\Repositories\\{$repositoryClass}(\$app->service('db'));",
            '    },',
            '',
            "    '{$singularBase}Crud' => function (App \$app): \\Cabnet\\Application\\Services\\{$serviceClass} {",
            "        return new \\Cabnet\\Application\\Services\\{$serviceClass}(",
            "            \$app->service('{$singularBase}Repository'),",
            '            $app->validator()',
            '        );',
            '    },',
            '',
        ]);

        $sidebarPatch = implode(PHP_EOL, [
            "            <a class=\"nav-link text-white <?= str_starts_with(\$currentPath, '/{$routeBase}') ? 'fw-bold' : '' ?>\" href=\"/{$routeBase}\">{$pluralLabel}</a>",
            '',
        ]);

        return [
            'generated/integration_patches/routes.patch.txt' => $routePatch,
            'generated/integration_patches/services.patch.txt' => $servicePatch,
            'generated/integration_patches/sidebar.patch.txt' => $sidebarPatch,
            'generated/integration_patches/bootstrap_requires.patch.txt' => implode(PHP_EOL, [
                "require_once BASE_PATH . '/src/Application/Crud/Definitions/{$classBase}EntityDefinition.php';",
                "require_once BASE_PATH . '/src/Application/Controllers/Admin/{$controllerClass}.php';",
                "require_once BASE_PATH . '/src/Infrastructure/Repositories/{$repositoryClass}.php';",
                "require_once BASE_PATH . '/src/Application/Services/{$serviceClass}.php';",
                '',
            ]),
            'generated/integration_patches/notes.txt' => implode(PHP_EOL, [
                'Target: src-first',
                'Code patches reference namespaced src/Application and src/Infrastructure classes.',
                'Admin view files still belong in app/Views/php/admin during the rendering bridge phase.',
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
