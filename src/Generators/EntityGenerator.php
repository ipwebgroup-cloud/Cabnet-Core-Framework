<?php
declare(strict_types=1);

namespace Cabnet\Generators;

final class EntityGenerator
{
    public function generate(string $entityKey, string $singularLabel, string $pluralLabel, string $table): array
    {
        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        return [
            'target' => 'src',
            'definition_class' => 'Cabnet\\Application\\Crud\\Definitions\\' . $classBase . 'EntityDefinition',
            'definition_file' => 'src/Application/Crud/Definitions/' . $classBase . 'EntityDefinition.php',
            'repository_class' => 'Cabnet\\Infrastructure\\Repositories\\' . $classBase . 'Repository',
            'repository_file' => 'src/Infrastructure/Repositories/' . $classBase . 'Repository.php',
            'service_class' => 'Cabnet\\Application\\Services\\' . $classBase . 'CrudService',
            'service_file' => 'src/Application/Services/' . $classBase . 'CrudService.php',
            'controller_class' => 'Cabnet\\Application\\Controllers\\Admin\\' . $classBase . 'Controller',
            'controller_file' => 'src/Application/Controllers/Admin/' . $classBase . 'Controller.php',
            'view_folder' => $routeBase,
            'view_path' => 'app/Views/php/admin/' . $routeBase,
            'route_base' => $routeBase,
            'service_keys' => [
                'repository' => $singularBase . 'Repository',
                'crud' => $singularBase . 'Crud',
            ],
            'table' => $table,
            'labels' => [
                'singular' => $singularLabel,
                'plural' => $pluralLabel,
            ],
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
