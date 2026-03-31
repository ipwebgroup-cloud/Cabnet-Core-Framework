<?php
declare(strict_types=1);

final class ScaffoldWriter
{
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

        $files = [];

        $definitionExport = var_export($fields, true);
        $listColumnsExport = var_export($listColumns, true);
        $searchableExport = var_export($searchable, true);

        $files["app/Crud/Definitions/{$definitionClass}.php"] = "<?php
declare(strict_types=1);

final class {$definitionClass}
{
    public static function make(): CrudEntityDefinition
    {
        return new CrudEntityDefinition(
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

        $files["app/Repositories/{$repositoryClass}.php"] = "<?php
declare(strict_types=1);

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
        $inputMap = [];
        foreach ($fields as $fieldName => $meta) {
            $fieldRules = [];
            if (!empty($meta['required'])) {
                $fieldRules[] = "'required'";
            }
            $type = (string)($meta['type'] ?? 'text');
            if ($type === 'email') {
                $fieldRules[] = "'email'";
            } else {
                $fieldRules[] = "'string'";
            }
            if ($fieldName === 'slug') {
                $fieldRules[] = "'slug'";
            }
            $fieldRules[] = $type === 'textarea' ? "'max:2000'" : "'max:255'";
            $rules[] = "            '{$fieldName}' => [" . implode(', ', $fieldRules) . "],";

            $default = "''";
            if ($type === 'select') {
                $options = (array)($meta['options'] ?? []);
                $keys = array_keys($options);
                $default = isset($keys[0]) ? "'" . (string)$keys[0] . "'" : "''";
            }
            $inputMap[] = "            '{$fieldName}' => \$app->request()->input('{$fieldName}', {$default}),";
        }

        $files["app/Services/{$serviceClass}.php"] = "<?php
declare(strict_types=1);

final class {$serviceClass} extends BaseService
{
    public function __construct(
        private {$repositoryClass} \$repository,
        private Validator \$validator
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

    public function create(array \$input): ValidationResult
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

    public function update(int \$id, array \$input): ValidationResult
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

        $files["app/Controllers/Admin/{$controllerClass}.php"] = "<?php
declare(strict_types=1);

final class {$controllerClass} extends BaseCrudController
{
    protected function entityDefinition(): CrudEntityDefinition
    {
        return {$definitionClass}::make();
    }

    public function index(App \$app, array \$params = []): Response
    {
        /** @var {$serviceClass} \$service */
        \$service = \$app->service('{$singularBase}Crud');
        \$search = trim((string)\$app->request()->query('q', ''));
        \$page = (int)\$app->request()->query('page', 1);

        \$pageData = \$service->paginate(\$search, \$page, 10);

        return \$this->render(\$app, 'admin/{$routeBase}/index.php', \$this->listViewData(
            \$app,
            \$pageData,
            \$search,
            'admin.{$routeBase}'
        ));
    }

    public function createForm(App \$app, array \$params = []): Response
    {
        return \$this->render(\$app, 'admin/{$routeBase}/create.php', \$this->formViewData(
            \$app,
            'create',
            \$app->url()->route('admin.{$routeBase}.store'),
            \$app->url()->route('admin.{$routeBase}.index')
        ));
    }

    public function store(App \$app, array \$params = []): Response
    {
        if (!\$app->csrf()->validate((string)\$app->request()->input('_token', ''))) {
            \$this->flash(\$app, 'danger', 'Invalid CSRF token.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.create'));
        }

        /** @var {$serviceClass} \$service */
        \$service = \$app->service('{$singularBase}Crud');

        \$input = [
" . implode("\n", $inputMap) . "
        ];

        \$result = \$service->create(\$input);

        if (!\$result->valid()) {
            \$app->viewState()->putOld(\$input);
            \$app->viewState()->putErrors(\$result->errors());
            \$this->flash(\$app, 'danger', 'Please correct the form errors.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.create'));
        }

        \$app->viewState()->clearFormState();
        \$this->flash(\$app, 'success', '{$singularLabel} created successfully.');
        return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
    }

    public function editForm(App \$app, array \$params = []): Response
    {
        \$id = (int)(\$params['id'] ?? 0);

        /** @var {$serviceClass} \$service */
        \$service = \$app->service('{$singularBase}Crud');
        \$row = \$service->find(\$id);

        if (!\$row) {
            \$this->flash(\$app, 'warning', '{$singularLabel} not found.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
        }

        return \$this->render(\$app, 'admin/{$routeBase}/edit.php', \$this->formViewData(
            \$app,
            'edit',
            \$app->url()->route('admin.{$routeBase}.update', ['id' => \$id]),
            \$app->url()->route('admin.{$routeBase}.index'),
            \$row
        ));
    }

    public function update(App \$app, array \$params = []): Response
    {
        \$id = (int)(\$params['id'] ?? 0);

        if (!\$app->csrf()->validate((string)\$app->request()->input('_token', ''))) {
            \$this->flash(\$app, 'danger', 'Invalid CSRF token.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.edit', ['id' => \$id]));
        }

        /** @var {$serviceClass} \$service */
        \$service = \$app->service('{$singularBase}Crud');
        \$row = \$service->find(\$id);

        if (!\$row) {
            \$this->flash(\$app, 'warning', '{$singularLabel} not found.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
        }

        \$input = [
" . implode("\n", $inputMap) . "
        ];

        \$result = \$service->update(\$id, \$input);

        if (!\$result->valid()) {
            \$app->viewState()->putOld(\$input);
            \$app->viewState()->putErrors(\$result->errors());
            \$this->flash(\$app, 'danger', 'Please correct the form errors.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.edit', ['id' => \$id]));
        }

        \$app->viewState()->clearFormState();
        \$this->flash(\$app, 'success', '{$singularLabel} updated successfully.');
        return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
    }

    public function destroy(App \$app, array \$params = []): Response
    {
        \$id = (int)(\$params['id'] ?? 0);

        if (!\$app->csrf()->validate((string)\$app->request()->input('_token', ''))) {
            \$this->flash(\$app, 'danger', 'Invalid CSRF token.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
        }

        /** @var {$serviceClass} \$service */
        \$service = \$app->service('{$singularBase}Crud');
        \$row = \$service->find(\$id);

        if (!\$row) {
            \$this->flash(\$app, 'warning', '{$singularLabel} not found.');
            return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
        }

        \$service->delete(\$id);
        \$this->flash(\$app, 'success', '{$singularLabel} deleted successfully.');
        return \$this->redirect(\$app, \$app->url()->route('admin.{$routeBase}.index'));
    }
}
";

        $files["app/Views/php/admin/{$routeBase}/index.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/index_table.php';\n";
        $files["app/Views/php/admin/{$routeBase}/create.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/form_page.php';\n";
        $files["app/Views/php/admin/{$routeBase}/edit.php"] = "<?php\ninclude BASE_PATH . '/app/Views/php/admin/crud/form_page.php';\n";

        $files["generated/{$routeBase}_route_snippets.php.txt"] = implode("\n", [
            "['method' => 'GET', 'path' => '/{$routeBase}', 'handler' => [{$controllerClass}::class, 'index'], 'name' => 'admin.{$routeBase}.index'],",
            "['method' => 'GET', 'path' => '/{$routeBase}/create', 'handler' => [{$controllerClass}::class, 'createForm'], 'name' => 'admin.{$routeBase}.create'],",
            "['method' => 'POST', 'path' => '/{$routeBase}', 'handler' => [{$controllerClass}::class, 'store'], 'name' => 'admin.{$routeBase}.store'],",
            "['method' => 'GET', 'path' => '/{$routeBase}/{id}/edit', 'handler' => [{$controllerClass}::class, 'editForm'], 'name' => 'admin.{$routeBase}.edit'],",
            "['method' => 'POST', 'path' => '/{$routeBase}/{id}/update', 'handler' => [{$controllerClass}::class, 'update'], 'name' => 'admin.{$routeBase}.update'],",
            "['method' => 'POST', 'path' => '/{$routeBase}/{id}/delete', 'handler' => [{$controllerClass}::class, 'destroy'], 'name' => 'admin.{$routeBase}.delete'],",
            ""
        ]);

        $files["generated/{$routeBase}_service_registration.php.txt"] = implode("\n", [
            "'{$singularBase}Repository' => function (App \$app): {$repositoryClass} {",
            "    return new {$repositoryClass}(\$app->service('db'));",
            "},",
            "",
            "'{$singularBase}Crud' => function (App \$app): {$serviceClass} {",
            "    return new {$serviceClass}(",
            "        \$app->service('{$singularBase}Repository'),",
            "        \$app->validator()",
            "    );",
            "},",
            ""
        ]);

        $schemaLines = [];
        $schemaLines[] = "CREATE TABLE IF NOT EXISTS `{$table}` (";
        $schemaLines[] = "  `id` int unsigned NOT NULL AUTO_INCREMENT,";
        foreach ($fields as $fieldName => $meta) {
            $type = (string)($meta['type'] ?? 'text');
            if ($type === 'textarea') {
                $columnType = 'text';
            } else {
                $columnType = 'varchar(255)';
            }

            if ($type === 'select') {
                $options = (array)($meta['options'] ?? []);
                $keys = array_keys($options);
                $default = (string)($keys[0] ?? '');
                $schemaLines[] = "  `{$fieldName}` varchar(255) NOT NULL DEFAULT '{$default}',";
            } else {
                $required = !empty($meta['required']);
                $schemaLines[] = "  `{$fieldName}` {$columnType} " . ($required ? "NOT NULL" : "DEFAULT NULL") . ",";
            }
        }
        $schemaLines[] = "  `created_at` datetime DEFAULT NULL,";
        $schemaLines[] = "  `updated_at` datetime DEFAULT NULL,";
        $schemaLines[] = "  PRIMARY KEY (`id`)";
        $schemaLines[] = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $files["database/schema/{$table}.sql"] = implode("\n", $schemaLines) . "\n";

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
