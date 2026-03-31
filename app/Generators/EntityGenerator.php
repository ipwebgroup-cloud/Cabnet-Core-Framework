<?php
declare(strict_types=1);

final class EntityGenerator
{
    public function generate(string $entityKey, string $singularLabel, string $pluralLabel, string $table): array
    {
        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));

        return [
            'definition_class' => $classBase . 'EntityDefinition',
            'repository_class' => $classBase . 'Repository',
            'service_class' => $classBase . 'CrudService',
            'controller_class' => $classBase . 'Controller',
            'view_folder' => $routeBase,
            'route_base' => $routeBase,
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
