<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

final class RelationOptionsHydrator
{
    /** @var array<string, array<string, string>> */
    private array $optionsCache = [];

    public function __construct(private mixed $db = null)
    {
    }

    public function hydrateDefinition(CrudEntityDefinition $definition): CrudEntityDefinition
    {
        $fields = $definition->fields();
        $changed = false;

        foreach ($fields as $field => $meta) {
            $relation = is_array($meta['relation'] ?? null) ? (array)$meta['relation'] : null;
            if ($relation === null) {
                continue;
            }

            $options = $this->relationOptions($relation);
            if ($options === []) {
                continue;
            }

            $fields[$field]['options'] = $options;
            $changed = true;
        }

        return $changed ? $definition->withFields($fields) : $definition;
    }

    /**
     * @param array<string, array<string, mixed>> $filters
     * @return array<string, array<string, mixed>>
     */
    public function hydrateFilters(CrudEntityDefinition $definition, array $filters): array
    {
        if ($filters === []) {
            return [];
        }

        foreach ($filters as $filterKey => $filterMeta) {
            $field = (string)($filterMeta['field'] ?? $filterKey);
            if (!$definition->hasField($field)) {
                continue;
            }

            $type = (string)($filterMeta['type'] ?? 'text');
            if ($type !== 'select') {
                continue;
            }

            $options = is_array($filterMeta['options'] ?? null) ? (array)$filterMeta['options'] : [];
            if ($options !== []) {
                continue;
            }

            $fieldMeta = $definition->field($field);
            $relation = is_array($fieldMeta['relation'] ?? null) ? (array)$fieldMeta['relation'] : null;
            if ($relation === null) {
                continue;
            }

            $relationOptions = $this->relationOptions($relation);
            if ($relationOptions === []) {
                continue;
            }

            $filterMeta['options'] = $relationOptions;
            $filters[$filterKey] = $filterMeta;
        }

        return $filters;
    }

    /**
     * @param array<string, mixed> $relation
     * @return array<string, string>
     */
    public function relationOptions(array $relation): array
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

        $cacheKey = implode(':', [$table, $valueColumn, $labelColumn, $orderBy]);
        if (array_key_exists($cacheKey, $this->optionsCache)) {
            return $this->optionsCache[$cacheKey];
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

        return $this->optionsCache[$cacheKey] = $options;
    }

    private function sanitizeIdentifier(string $value): string
    {
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) ? $value : '';
    }
}
