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

        $classBase = $this->studly($this->singularize($entityKey));
        $routeBase = strtolower($this->pluralize($entityKey));
        $singularBase = $this->singularize($routeBase);

        $definitionClass = $classBase . 'EntityDefinition';
        $repositoryClass = $classBase . 'Repository';
        $serviceClass = $classBase . 'CrudService';
        $controllerClass = $classBase . 'Controller';

        $definitionExport = var_export($fields, true);
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

        foreach ($fields as $fieldName => $meta) {
            $createColumns[] = "`{$fieldName}`";
            $insertValues[] = ":{$fieldName}";
            $updateAssignments[] = "`{$fieldName}` = :{$fieldName}";
            $createParams[] = "            '{$fieldName}' => \$data['{$fieldName}'] ?? '',";
            $updateParams[] = "            '{$fieldName}' => \$data['{$fieldName}'] ?? '',";
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

        $rules = [];
        foreach ($fields as $fieldName => $meta) {
            $fieldRules = [];
            if (!empty($meta['required'])) {
                $fieldRules[] = "'required'";
            }
            $type = (string)($meta['type'] ?? 'text');
            $fieldRules[] = $type === 'email' ? "'email'" : "'string'";
            if ($fieldName === 'slug') {
                $fieldRules[] = "'slug'";
            }
            $fieldRules[] = $type === 'textarea' ? "'max:2000'" : "'max:255'";
            $rules[] = "            '{$fieldName}' => [" . implode(', ', $fieldRules) . "],";
        }

        $files["src/Application/Services/{$serviceClass}.php"] = "<?php
declare(strict_types=1);

namespace Cabnet\\Application\\Services;

use Cabnet\\Application\\Crud\\Definitions\\{$definitionClass};
use Cabnet\\Infrastructure\\Repositories\\{$repositoryClass};

final class {$serviceClass} extends BaseService
{
    public function __construct(
        private {$repositoryClass} \$repository,
        private \\Validator \$validator
    ) {
    }

    public function paginate(string \$search = '', int \$page = 1, int \$perPage = 15): array
    {
        return \$this->repository->findPage(
            searchColumns: {$definitionClass}::make()->searchable(),
            search: \$search,
            page: \$page,
            perPage: \$perPage,
            orderBy: {$definitionClass}::make()->defaultOrder()
        );
    }

    public function find(int \$id): ?array
    {
        return \$this->repository->findById(\$id);
    }

    public function create(array \$input): \\ValidationResult
    {
        \$result = \$this->validator->validate(\$input, [
" . implode("\n", $rules) . "
        ]);

        if (!\$result->valid()) {
            return \$result;
        }

        \$this->repository->create(\$result->data());
        return \$result;
    }

    public function update(int \$id, array \$input): \\ValidationResult
    {
        \$result = \$this->validator->validate(\$input, [
" . implode("\n", $rules) . "
        ]);

        if (!\$result->valid()) {
            return \$result;
        }

        \$this->repository->updateById(\$id, \$result->data());
        return \$result;
    }

    public function delete(int \$id): bool
    {
        return \$this->repository->deleteById(\$id);
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

        $files["app/Views/php/admin/{$routeBase}/index.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/index_table.php';\n";
        $files["app/Views/php/admin/{$routeBase}/create.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/form_page.php';\n";
        $files["app/Views/php/admin/{$routeBase}/edit.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/form_page.php';\n";

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
            "    'show_in_admin_menu' => true,",
            "    'generator_target' => 'src',",
            "],",
            '',
        ]);

        $schemaLines = [];
        $schemaLines[] = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $schemaLines[] = '  `id` int unsigned NOT NULL AUTO_INCREMENT,';
        foreach ($fields as $fieldName => $meta) {
            $type = (string)($meta['type'] ?? 'text');
            $columnType = $type === 'textarea' ? 'text' : 'varchar(255)';

            if ($type === 'select') {
                $options = (array)($meta['options'] ?? []);
                $keys = array_keys($options);
                $default = (string)($keys[0] ?? '');
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
            'Admin routes, admin menu items, repository services, and CRUD services now derive from module metadata automatically.',
            'Admin PHP views remain under app/Views/php/admin until rendering is migrated fully to src/View.',
            '',
        ]);

        return $files;
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
