<?php
declare(strict_types=1);

namespace Cabnet\Application\Services;

use Cabnet\Application\Crud\CrudEntityDefinition;
use Cabnet\Infrastructure\Repositories\CrudRepositoryContract;
use Cabnet\Support\UploadManager;

abstract class DefinitionCrudService extends BaseService
{
    private ?CrudEntityDefinition $resolvedDefinition = null;

    public function __construct(
        protected CrudEntityDefinition $definition,
        protected CrudRepositoryContract $repository,
        protected \Validator $validator,
        protected mixed $db = null,
        protected ?UploadManager $uploadManager = null
    ) {
    }

    public function formDefinition(): CrudEntityDefinition
    {
        return $this->resolvedDefinition();
    }

    /** @return array<string, mixed> */
    public function paginate(string $search = '', int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $definition = $this->resolvedDefinition();

        return $this->repository->findPage(
            searchColumns: $definition->searchable(),
            search: $search,
            page: $page,
            perPage: $perPage,
            filters: $filters,
            orderBy: $definition->defaultOrder()
        );
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $row = $this->repository->findById($id);

        if (!is_array($row)) {
            return null;
        }

        return $this->hydrateStoredRow($row, $this->resolvedDefinition());
    }

    public function create(array $input): \ValidationResult
    {
        $definition = $this->resolvedDefinition();
        $result = $this->validator->validate(
            $definition->inputPayload($input),
            $definition->validationRules()
        );

        if (!$result->valid()) {
            return $result;
        }

        $data = $this->preparePersistenceData($result->data(), $definition);
        if ($this->uploadManager instanceof UploadManager) {
            $data = $this->uploadManager->persistConfiguredUploads($definition, $data);
        }

        $this->repository->create($data);

        return new \ValidationResult(true, [], $data);
    }

    public function update(int $id, array $input): \ValidationResult
    {
        $definition = $this->resolvedDefinition();
        $result = $this->validator->validate(
            $definition->inputPayload($input),
            $definition->validationRules()
        );

        if (!$result->valid()) {
            return $result;
        }

        $existing = $this->repository->findById($id) ?? [];
        $data = $this->preparePersistenceData($result->data(), $definition);

        if ($this->uploadManager instanceof UploadManager) {
            $data = $this->uploadManager->persistConfiguredUploads($definition, $data, $existing);
        }

        $this->repository->updateById($id, $data);

        return new \ValidationResult(true, [], $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->deleteById($id);
    }

    private function resolvedDefinition(): CrudEntityDefinition
    {
        if ($this->resolvedDefinition instanceof CrudEntityDefinition) {
            return $this->resolvedDefinition;
        }

        $fields = $this->definition->fields();

        foreach ($fields as $field => $meta) {
            $relation = $meta['relation'] ?? null;
            if (!is_array($relation)) {
                continue;
            }

            $options = $this->relationOptions($relation);
            if ($options !== []) {
                $fields[$field]['options'] = $options;
            }
        }

        $this->resolvedDefinition = $this->definition->withFields($fields);

        return $this->resolvedDefinition;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function hydrateStoredRow(array $row, CrudEntityDefinition $definition): array
    {
        foreach ($definition->fields() as $field => $meta) {
            if (empty($meta['translatable']) || !array_key_exists($field, $row) || !is_string($row[$field])) {
                continue;
            }

            $decoded = json_decode($row[$field], true);
            if (is_array($decoded)) {
                $row[$field] = $decoded;
            }
        }

        return $row;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function preparePersistenceData(array $data, CrudEntityDefinition $definition): array
    {
        foreach ($definition->fields() as $field => $meta) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            if (!empty($meta['translatable']) && is_array($data[$field])) {
                $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $relation
     * @return array<string, string>
     */
    private function relationOptions(array $relation): array
    {
        if (!is_object($this->db) || !method_exists($this->db, 'select')) {
            return [];
        }

        $table = $this->sanitizeIdentifier((string)($relation['table'] ?? ''));
        $valueColumn = $this->sanitizeIdentifier((string)($relation['value_column'] ?? 'id'));
        $labelColumn = $this->sanitizeIdentifier((string)($relation['label_column'] ?? 'name'));
        $orderBy = $this->sanitizeIdentifier((string)($relation['order_by'] ?? $labelColumn));

        if ($table === '' || $valueColumn === '' || $labelColumn === '' || $orderBy === '') {
            return [];
        }

        $sql = sprintf(
            'SELECT `%s` AS `value`, `%s` AS `label` FROM `%s` ORDER BY `%s` ASC',
            $valueColumn,
            $labelColumn,
            $table,
            $orderBy
        );

        $rows = $this->db->select($sql);

        $options = [];
        foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists('value', $row)) {
                continue;
            }

            $options[(string)$row['value']] = (string)($row['label'] ?? $row['value']);
        }

        return $options;
    }

    private function sanitizeIdentifier(string $value): string
    {
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) ? $value : '';
    }
}
