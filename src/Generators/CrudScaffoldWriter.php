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
        $accessRoles = $this->normalizeRoles($blueprint['access_roles'] ?? null);
        $permissions = $this->normalizePermissions($blueprint['permissions'] ?? null, $accessRoles);
        $adminMiddleware = $this->normalizeAdminMiddleware($blueprint['admin_middleware'] ?? null);
        $showInAdminMenu = array_key_exists('show_in_admin_menu', $blueprint)
            ? (bool)$blueprint['show_in_admin_menu']
            : true;

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

        $filters = $this->mergeFilters(
            $this->deriveFiltersFromFields($normalizedFields),
            $this->normalizeFilters($blueprint['filters'] ?? null, $normalizedFields)
        );

        $definitionExport = var_export($normalizedFields, true);
        $listColumnsExport = var_export($listColumns, true);
        $searchableExport = var_export($searchable, true);
        $accessRolesExport = var_export($accessRoles, true);
        $permissionsExport = var_export($permissions, true);
        $adminMiddlewareExport = var_export($adminMiddleware, true);
        $filtersExport = var_export($filters, true);

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
            "    'admin_middleware' => " . $adminMiddlewareExport . ",",
            "    'access_roles' => " . $accessRolesExport . ",",
            "    'permissions' => " . $permissionsExport . ",",
            "    'filters' => " . $filtersExport . ",",
            "    'policy_class' => " . ($policyClass !== null ? $policyClass . '::class' : 'null') . ",",
            "    'show_in_admin_menu' => " . ($showInAdminMenu ? 'true' : 'false') . ",",
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
            'Module metadata can now also declare access_roles, per-action roles, optional policy hooks, admin middleware overrides, admin-menu visibility, and list-filter metadata for cleaner admin behavior.',
            'Generated admin PHP views now target src/Presentation/Views/php/admin first, with app/Views/php remaining as a compatibility fallback.',
            'Generated admin Twig views now target src/Presentation/Views/twig/admin and extend the canonical shared CRUD Twig templates.',
            'Field metadata can now also describe uploads, relation-driven selects, translatable inputs, and filter shortcuts via filter/filterable/list_filter.',
            'Requested view engines: ' . implode(', ', $viewEngines) . '.',
            'Generated filters: ' . ($filters !== [] ? implode(', ', array_keys($filters)) : 'none') . '.',
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
        $label = (string)($meta['label'] ?? $this->studly(str_replace('_', ' ', $fieldName)));

        $normalized = [
            'type' => $type,
            'label' => $label,
            'required' => (bool)($meta['required'] ?? false),
            'default' => $meta['default'] ?? '',
        ];

        if (array_key_exists('placeholder', $meta)) {
            $normalized['placeholder'] = (string)$meta['placeholder'];
        }

        if ($type === 'select') {
            $normalized['options'] = is_array($meta['options'] ?? null) ? $meta['options'] : [];
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

        if (array_key_exists('filter', $meta)) {
            $normalized['filter'] = $meta['filter'];
        }

        if (array_key_exists('filterable', $meta)) {
            $normalized['filterable'] = (bool)$meta['filterable'];
        }

        if (array_key_exists('list_filter', $meta)) {
            $normalized['list_filter'] = $meta['list_filter'];
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

    /** @return array<int, string> */
    private function normalizeRoles(mixed $roles): array
    {
        if (is_string($roles) && trim($roles) !== '') {
            $roles = [trim($roles)];
        }

        if (!is_array($roles) || $roles === []) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            static function (mixed $role): ?string {
                if (!is_string($role)) {
                    return null;
                }

                $role = trim($role);
                return $role !== '' ? $role : null;
            },
            $roles
        ))));
    }

    /**
     * @param array<string, mixed>|mixed $configured
     * @param array<int, string> $accessRoles
     * @return array<string, array<int, string>>
     */
    private function normalizePermissions(mixed $configured, array $accessRoles): array
    {
        $configured = is_array($configured) ? $configured : [];
        $fallback = $accessRoles !== [] ? $accessRoles : ['admin'];
        $permissions = [];

        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            $roles = $this->normalizeRoles($configured[$action] ?? null);
            $permissions[$action] = $roles !== [] ? $roles : $fallback;
        }

        return $permissions;
    }

    /** @return array<int, string> */
    private function normalizeAdminMiddleware(mixed $middleware): array
    {
        $middleware = $this->normalizeRoles($middleware);
        return $middleware !== [] ? $middleware : ['session', 'admin.auth'];
    }

    /**
     * @param array<string, array<string, mixed>> $fields
     * @return array<string, array<string, mixed>>
     */
    private function normalizeFilters(mixed $configured, array $fields): array
    {
        if (!is_array($configured) || $configured === []) {
            return [];
        }

        $filters = [];
        foreach ($configured as $filterKey => $filterMeta) {
            if (is_string($filterMeta)) {
                $field = $filterMeta;
                $filterMeta = [];
            } elseif (is_array($filterMeta)) {
                $field = (string)($filterMeta['field'] ?? $filterKey);
            } else {
                continue;
            }

            if (!isset($fields[$field])) {
                continue;
            }

            $metaArray = is_array($filterMeta) ? $filterMeta : [];
            $filters[(string)$filterKey] = $this->buildFilterDefinition((string)$filterKey, $field, $fields[$field], $metaArray);
        }

        return $filters;
    }

    /**
     * @param array<string, array<string, mixed>> $fields
     * @return array<string, array<string, mixed>>
     */
    private function deriveFiltersFromFields(array $fields): array
    {
        $filters = [];

        foreach ($fields as $fieldName => $fieldMeta) {
            $shortcut = $fieldMeta['filter'] ?? ($fieldMeta['list_filter'] ?? ($fieldMeta['filterable'] ?? null));
            if ($shortcut === null || $shortcut === false) {
                continue;
            }

            $meta = [];
            if (is_array($shortcut)) {
                $meta = $shortcut;
            } elseif (is_string($shortcut) && trim($shortcut) !== '') {
                $meta = ['label' => trim($shortcut)];
            }

            if (!$this->canDeriveFilterFromField($fieldMeta, $meta)) {
                continue;
            }

            $filterKey = (string)($meta['query_key'] ?? $fieldName);
            $filters[$filterKey] = $this->buildFilterDefinition($filterKey, $fieldName, $fieldMeta, $meta);
        }

        return $filters;
    }

    /**
     * @param array<string, array<string, mixed>> $derived
     * @param array<string, array<string, mixed>> $configured
     * @return array<string, array<string, mixed>>
     */
    private function mergeFilters(array $derived, array $configured): array
    {
        if ($derived === []) {
            return $configured;
        }

        if ($configured === []) {
            return $derived;
        }

        return array_replace($derived, $configured);
    }

    /**
     * @param array<string, mixed> $fieldMeta
     * @param array<string, mixed> $filterMeta
     * @return array<string, mixed>
     */
    private function buildFilterDefinition(string $filterKey, string $field, array $fieldMeta, array $filterMeta): array
    {
        $options = [];
        if (is_array($filterMeta['options'] ?? null)) {
            $options = (array)$filterMeta['options'];
        } elseif (is_array($fieldMeta['options'] ?? null)) {
            $options = (array)$fieldMeta['options'];
        }

        $type = (string)($filterMeta['type'] ?? $fieldMeta['type'] ?? 'text');
        if ($type === 'select' && $options === []) {
            $type = 'text';
        }

        $filter = [
            'field' => $field,
            'query_key' => (string)($filterMeta['query_key'] ?? $filterKey),
            'label' => (string)($filterMeta['label'] ?? $fieldMeta['label'] ?? ucfirst($field)),
            'type' => $type,
            'placeholder' => (string)($filterMeta['placeholder'] ?? ''),
            'default' => $filterMeta['default'] ?? null,
            'help' => (string)($filterMeta['help'] ?? $fieldMeta['help'] ?? ''),
        ];

        if ($type === 'select') {
            $filter['options'] = $options;
        }

        return $filter;
    }

    /**
     * @param array<string, mixed> $fieldMeta
     * @param array<string, mixed> $filterMeta
     */
    private function canDeriveFilterFromField(array $fieldMeta, array $filterMeta): bool
    {
        if (!empty($fieldMeta['upload']) || !empty($fieldMeta['translatable'])) {
            return false;
        }

        if (isset($filterMeta['type']) && is_string($filterMeta['type']) && trim($filterMeta['type']) !== '') {
            return true;
        }

        $type = (string)($fieldMeta['type'] ?? 'text');
        if ($type === 'select') {
            return true;
        }

        return in_array($type, ['text', 'email', 'integer', 'number', 'textarea'], true);
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
