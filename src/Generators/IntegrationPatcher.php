<?php
declare(strict_types=1);

namespace Cabnet\Generators;

final class IntegrationPatcher
{
    /**
     * @param array<string, mixed> $blueprint
     * @return array<string, string>
     */
    public function buildPatches(array $blueprint): array
    {
        $entityKey = (string)($blueprint['entity_key'] ?? 'items');
        $pluralLabel = (string)($blueprint['plural_label'] ?? 'Items');
        $singularLabel = (string)($blueprint['singular_label'] ?? 'Item');

        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        $controllerClass = $classBase . 'Controller';
        $repositoryClass = $classBase . 'Repository';
        $serviceClass = $classBase . 'CrudService';
        $definitionClass = $classBase . 'EntityDefinition';

        return [
            'generated/integration_patches/modules.patch.txt' => implode(PHP_EOL, [
                "    '{$routeBase}' => [",
                "        'enabled' => true,",
                "        'label' => '{$pluralLabel}',",
                "        'singular_label' => '{$singularLabel}',",
                "        'route_prefix' => '/{$routeBase}',",
                "        'definition_class' => \\Cabnet\\Application\\Crud\\Definitions\\{$definitionClass}::class,",
                "        'controller_class' => \\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class,",
                "        'repository_class' => \\Cabnet\\Infrastructure\\Repositories\\{$repositoryClass}::class,",
                "        'service_class' => \\Cabnet\\Application\\Services\\{$serviceClass}::class,",
                "        'repository_service' => '{$singularBase}Repository',",
                "        'crud_service' => '{$singularBase}Crud',",
                "        'admin_route_base' => 'admin.{$routeBase}',",
                "        'admin_view_path' => 'admin/{$routeBase}',",
                "        'admin_middleware' => ['session', 'admin.auth'],",
                "        'show_in_admin_menu' => true,",
                "        'generator_target' => 'src',",
                "    ],",
                '',
            ]),
            'generated/integration_patches/notes.txt' => implode(PHP_EOL, [
                'Target: src-first registry adoption',
                'Add the generated block to config/modules.php.',
                'Admin routes, repository services, CRUD services, and admin menu links are now derived automatically from module metadata.',
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
