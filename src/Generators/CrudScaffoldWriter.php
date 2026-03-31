<?php

declare(strict_types=1);

namespace Cabnet\Generators;

final class CrudScaffoldWriter
{
    /**
     * @param array<string, mixed> $blueprint
     * @return array<string, string>
     */
    public function buildCrudPack(array $blueprint): array
    {
        $entityKey = (string)($blueprint['entity_key'] ?? 'items');
        $singularLabel = (string)($blueprint['singular_label'] ?? 'Item');
        $pluralLabel = (string)($blueprint['plural_label'] ?? 'Items');
        $table = (string)($blueprint['table'] ?? $entityKey);
        $fields = (array)($blueprint['fields'] ?? []);
        $listColumns = (array)($blueprint['list_columns'] ?? ['id']);
        $searchable = (array)($blueprint['searchable'] ?? []);
        $defaultOrder = (string)($blueprint['default_order'] ?? 'id DESC');
        $viewEngines = $this->normalizeViewEngines($blueprint);
        $policyClass = is_string($blueprint['policy_class'] ?? null) && (string)$blueprint['policy_class'] !== ''
            ? (string)$blueprint['policy_class']
            : null;

        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        $definitionClass = $classBase . 'EntityDefinition';
        $repositoryClass = $classBase . 'Repository';
        $serviceClass = $classBase . 'CrudService';
        $controllerClass = $classBase . 'Controller';

        $normalizedFields = [];
        foreach ($fields as $fieldName => $meta) {
            $normalizedFields[$fieldName] = $this->normalizeFieldMetadata($fieldName, is_array($meta) ? $meta : []);
        }

        $definitionExport = var_export($normalizedFields, true);
        $listColumnsExport = var_export($listColumns, true);
        $searchableExport = var_export($searchable, true);

        $files = [];

        $files["src/Application/Crud/Definitions/{$definitionClass}.php"] = "<?php
declare(strict_types=1);

namespace Cabnet\\Application\\Crud\\Definitions;

final class {$definitionClass}
{
    public static function make(): \\Cabnet\\Application\\Crud\\CrudEntityDefinition
    {
        return new \\Cabnet\\Application\\Crud\\CrudEntityDefinition(
            key: '{$routeBase}',
            label: '{$pluralLabel}',
            table: '{$table}',
            fields: " . $definitionExport . ",
            listColumns: " . $listColumnsExport . ",
            searchable: " . $searchableExport . ",
            defaultOrder: '{$defaultOrder}'
        );
    }
}
";

        $createColumns = [];
        $insertValues = [];
        $updateAssignments = [];
        $createParams = [];
        $updateParams = [];

        foreach ($normalizedFields as $fieldName => $meta) {
            $createColumns[] = "`{$fieldName}`";
            $insertValues[] = ":{$fieldName}";
            $updateAssignments[] = "`{$fieldName}` = :{$fieldName}";
            $defaultValue = var_export($meta['default'] ?? '', true);
            $createParams[] = "            '{$fieldName}' => \$data['{$fieldName}'] ?? {$defaultValue},";
            $updateParams[] = "            '{$fieldName}' => \$data['{$fieldName}'] ?? {$defaultValue},";
        }

        $files["src/Infrastructure/Repositories/{$repositoryClass}.php"] = "<?php
declare(strict_types=1);

namespace Cabnet\\Infrastructure\\Repositories;

final class {$repositoryClass} extends BaseRepository
{
    protected function table(): string
    {
        return '{$table}';
    }

    public function create(array \$data): bool
    {
        \$sql = 'INSERT INTO `{$table}` (" . implode(', ', $createColumns) . ", `created_at`, `updated_at`)
                VALUES (" . implode(', ', $insertValues) . ", NOW(), NOW())';

        return \$this->db->execute(\$sql, [
" . implode("\n", $createParams) . "
        ]);
    }

    public function updateById(int \$id, array \$data): bool
    {
        \$sql = 'UPDATE `{$table}`
                SET " . implode(",\n                    ", $updateAssignments) . ",
                    `updated_at` = NOW()
                WHERE `id` = :id';

        return \$this->db->execute(\$sql, [
            'id' => \$id,
" . implode("\n", $updateParams) . "
        ]);
    }
}
";

        $files["src/Application/Services/{$serviceClass}.php"] = "<?php
declare(strict_types=1);

namespace Cabnet\\Application\\Services;

use Cabnet\\Application\\Crud\\Definitions\\{$definitionClass};
use Cabnet\\Infrastructure\\Repositories\\{$repositoryClass};
use Cabnet\\Support\\UploadManager;

final class {$serviceClass} extends DefinitionCrudService
{
    public function __construct(
        {$repositoryClass} \$repository,
        \\Validator \$validator,
        mixed \$db = null,
        ?UploadManager \$uploadManager = null
    ) {
        parent::__construct({$definitionClass}::make(), \$repository, \$validator, \$db, \$uploadManager);
    }
}
";

        $files["src/Application/Controllers/Admin/{$controllerClass}.php"] = "<?php
declare(strict_types=1);

namespace Cabnet\\Application\\Controllers\\Admin;

final class {$controllerClass} extends BaseCrudController
{
    protected function moduleKey(): string
    {
        return '{$routeBase}';
    }
}
";

        foreach ($viewEngines as $viewEngine) {
            foreach ($this->presentationStubFiles($routeBase, $viewEngine) as $relative => $content) {
                $files[$relative] = $content;
            }
        }

        $files["generated/{$routeBase}_module_config.php.txt"] = implode("\n", [
            "'{$routeBase}' => [",
            "    'enabled' => true,",
            "    'label' => '{$pluralLabel}',",
            "    'singular_label' => '{$singularLabel}',",
            "    'route_prefix' => '/{$routeBase}',",
            "    'table' => '{$table}',",
            "    'definition_class' => \\Cabnet\\Application\\Crud\\Definitions\\{$definitionClass}::class,",
            "    'controller_class' => \\Cabnet\\Application\\Controllers\\Admin\\{$controllerClass}::class,",
            "    'repository_class' => \\Cabnet\\Infrastructure\\Repositories\\{$repositoryClass}::class,",
            "    'service_class' => \\Cabnet\\Application\\Services\\{$serviceClass}::class,",
            "    'repository_service' => '{$singularBase}Repository',",
            "    'crud_service' => '{$singularBase}Crud',",
            "    'admin_route_base' => 'admin.{$routeBase}',",
            "    'admin_view_path' => 'admin/{$routeBase}',",
            "    'admin_middleware' => ['session', 'admin.auth'],",
            "    'permissions' => [",
            "        'view' => ['admin'],",
            "        'create' => ['admin'],",
            "        'edit' => ['admin'],",
            "        'delete' => ['admin'],",
            "    ],",
            "    'filters' => [],",
            "    'policy_class' => " . ($policyClass !== null ? $policyClass . '::class' : 'null') . ",",
            "    'show_in_admin_menu' => true,",
            "    'generator_target' => 'src',",
            '],',
            '',
        ]);

        $schemaLines = [];
        $schemaLines[] = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $schemaLines[] = '  `id` int unsigned NOT NULL AUTO_INCREMENT,';
        foreach ($normalizedFields as $fieldName => $meta) {
            $type = (string)($meta['type'] ?? 'text');
            $columnType = (!empty($meta['translatable']) || $type === 'textarea') ? 'text' : 'varchar(255)';

            if ($type === 'select') {
                $options = (array)($meta['options'] ?? []);
                $keys = array_keys($options);
                $default = str_replace("'", "''", (string)($meta['default'] ?? ($keys[0] ?? '')));
                $schemaLines[] = "  `{$fieldName}` varchar(255) NOT NULL DEFAULT '{$default}',";
            } else {
                $required = !empty($meta['required']);
                $schemaLines[] = "  `{$fieldName}` {$columnType} " . ($required ? 'NOT NULL' : 'DEFAULT NULL') . ',';
            }
        }
        $schemaLines[] = '  `created_at` datetime DEFAULT NULL,';
        $schemaLines[] = '  `updated_at` datetime DEFAULT NULL,';
        $schemaLines[] = '  PRIMARY KEY (`id`)';
        $schemaLines[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        $files["database/schema/{$table}.sql"] = implode("\n", $schemaLines) . "\n";

        $files["generated/{$routeBase}_implementation_notes.txt"] = implode("\n", [
            'Target: src-first',
            'Add the generated module metadata block to config/modules.php.',
            'Field metadata now carries validation and form rendering hints. Prefer editing the definition before editing the service or form partials.',
            'Admin routes, admin menu items, repository services, and CRUD services now derive from module metadata automatically.',
            'Module metadata can now also declare per-action roles, optional policy hooks, and list-filter metadata for cleaner admin behavior.',
            'Generated admin PHP views now target src/Presentation/Views/php/admin first, with app/Views/php remaining as a compatibility fallback.',
            'Generated admin Twig views now target src/Presentation/Views/twig/admin and extend the canonical shared CRUD Twig templates.',
            'Field metadata can now also describe uploads, relation-driven selects, and translatable inputs.',
            'Requested view engines: ' . implode(', ', $viewEngines) . '.',
            'Keep config/app.php renderer on php unless the project explicitly switches to twig and has twig/twig installed.',
            '',
        ]);

        return $files;
    }

    /** @param array<string, mixed> $meta
     *  @return array<string, mixed>
     */
    private function normalizeFieldMetadata(string $fieldName, array $meta): array
    {
        $type = (string)($meta['type'] ?? 'text');
        $label = (string)($meta['label'] ?? $this->studly(str_replace(['-', '_'], ' ', $fieldName)));
        $normalized = [
            'type' => $type,
            'label' => $label,
            'required' => !empty($meta['required']),
        ];

        if (isset($meta['options']) && is_array($meta['options'])) {
            $normalized['options'] = $meta['options'];
            $optionKeys = array_keys($meta['options']);
            $normalized['default'] = $meta['default'] ?? (string)($optionKeys[0] ?? '');
        } elseif (array_key_exists('default', $meta)) {
            $normalized['default'] = $meta['default'];
        }

        if (array_key_exists('placeholder', $meta)) {
            $normalized['placeholder'] = (string)$meta['placeholder'];
        }

        if (array_key_exists('help', $meta)) {
            $normalized['help'] = (string)$meta['help'];
        }

        if (array_key_exists('min', $meta)) {
            $normalized['min'] = (int)$meta['min'];
        }

        if (array_key_exists('max', $meta)) {
            $normalized['max'] = (int)$meta['max'];
        } else {
            $normalized['max'] = $type === 'textarea' ? 2000 : 255;
        }

        if ($type === 'textarea') {
            $normalized['rows'] = isset($meta['rows']) ? max(2, (int)$meta['rows']) : 5;
        }

        if (!empty($meta['translatable'])) {
            $normalized['translatable'] = true;
            $normalized['locales'] = is_array($meta['locales'] ?? null) ? array_values($meta['locales']) : ['en'];
        }

        if ($type === 'file' || $type === 'image' || !empty($meta['upload'])) {
            $normalized['upload'] = true;
            if ($type === 'image' || !empty($meta['image'])) {
                $normalized['image'] = true;
            }
            if (array_key_exists('accept', $meta)) {
                $normalized['accept'] = (string)$meta['accept'];
            }
            if (array_key_exists('max_size_kb', $meta)) {
                $normalized['max_size_kb'] = max(1, (int)$meta['max_size_kb']);
            }
            if (array_key_exists('upload_dir', $meta)) {
                $normalized['upload_dir'] = (string)$meta['upload_dir'];
            }
        }

        if (isset($meta['relation']) && is_array($meta['relation'])) {
            $normalized['relation'] = $meta['relation'];
        }

        if (!empty($meta['slug']) || $fieldName === 'slug') {
            $normalized['slug'] = true;
        }

        if (array_key_exists('rules', $meta)) {
            $normalized['rules'] = $meta['rules'];
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $blueprint
     * @return array<int, string>
     */
    private function normalizeViewEngines(array $blueprint): array
    {
        $raw = $blueprint['view_engines'] ?? ($blueprint['view_engine'] ?? ['php']);

        if (is_string($raw)) {
            $raw = [$raw];
        }

        $normalized = [];
        foreach ((array)$raw as $engine) {
            if (!is_string($engine)) {
                continue;
            }

            $engine = strtolower(trim($engine));
            if ($engine === 'both') {
                $normalized[] = 'php';
                $normalized[] = 'twig';
                continue;
            }

            if (in_array($engine, ['php', 'twig'], true)) {
                $normalized[] = $engine;
            }
        }

        $normalized = array_values(array_unique($normalized));
        return $normalized !== [] ? $normalized : ['php'];
    }

    /**
     * @return array<string, string>
     */
    private function presentationStubFiles(string $routeBase, string $viewEngine): array
    {
        if ($viewEngine === 'twig') {
            return [
                "src/Presentation/Views/twig/admin/{$routeBase}/index.twig" => "{% extends '@src/admin/crud/index_table.twig' %}\n",
                "src/Presentation/Views/twig/admin/{$routeBase}/create.twig" => "{% extends '@src/admin/crud/form_page.twig' %}\n",
                "src/Presentation/Views/twig/admin/{$routeBase}/edit.twig" => "{% extends '@src/admin/crud/form_page.twig' %}\n",
            ];
        }

        return [
            "src/Presentation/Views/php/admin/{$routeBase}/index.php" => "<?php\ninclude BASE_PATH . '/src/Presentation/Views/php/admin/crud/index_table.php';\n",
            "src/Presentation/Views/php/admin/{$routeBase}/create.php" => "<?php\ninclude BASE_PATH . '/src/Presentation/Views/php/admin/crud/form_page.php';\n",
            "src/Presentation/Views/php/admin/{$routeBase}/edit.php" => "<?php\ninclude BASE_PATH . '/src/Presentation/Views/php/admin/crud/form_page.php';\n",
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
