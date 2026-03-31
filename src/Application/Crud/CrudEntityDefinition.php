<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

use InvalidArgumentException;

class CrudEntityDefinition
{
    /**
     * @param array<string, array<string, mixed>> $fields
     * @param array<int, string> $listColumns
     * @param array<int, string> $searchable
     */
    public function __construct(
        private string $key,
        private string $label,
        private string $table,
        private array $fields = [],
        private array $listColumns = [],
        private array $searchable = [],
        private string $defaultOrder = 'id DESC'
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function table(): string
    {
        return $this->table;
    }

    /** @return array<string, array<string, mixed>> */
    public function fields(): array
    {
        return $this->fields;
    }

    /** @return array<string, mixed> */
    public function field(string $field): array
    {
        if (!isset($this->fields[$field])) {
            throw new InvalidArgumentException("Unknown CRUD field [{$field}] for entity [{$this->key}].");
        }

        return $this->fields[$field];
    }

    public function hasField(string $field): bool
    {
        return isset($this->fields[$field]);
    }

    /** @return array<int, string> */
    public function listColumns(): array
    {
        return $this->listColumns;
    }

    /** @return array<int, string> */
    public function searchable(): array
    {
        return $this->searchable;
    }

    public function defaultOrder(): string
    {
        return $this->defaultOrder;
    }

    /** @return array<int, string> */
    public function fieldNames(): array
    {
        return array_keys($this->fields);
    }

    public function fieldLabel(string $field): string
    {
        return (string)($this->field($field)['label'] ?? ucfirst($field));
    }

    /** @return array<string, array<int, string>> */
    public function validationRules(): array
    {
        $rules = [];

        foreach ($this->fields as $field => $meta) {
            $rules[$field] = $this->buildValidationRulesForField($field, $meta);
        }

        return $rules;
    }

    /** @return array<int, string> */
    public function validationRulesForField(string $field): array
    {
        return $this->buildValidationRulesForField($field, $this->field($field));
    }

    /** @return array<string, mixed> */
    public function inputDefaults(): array
    {
        $defaults = [];

        foreach ($this->fields as $field => $meta) {
            $defaults[$field] = $this->defaultValueForField($meta);
        }

        return $defaults;
    }

    /**
     * @param array<string, mixed> $source
     * @return array<string, mixed>
     */
    public function inputPayload(array $source): array
    {
        $payload = [];

        foreach ($this->fields as $field => $meta) {
            $payload[$field] = array_key_exists($field, $source)
                ? $this->normalizeInputValue($source[$field], $meta)
                : $this->defaultValueForField($meta);
        }

        return $payload;
    }

    public function normalizeFieldValue(string $field, mixed $value): mixed
    {
        return $this->normalizeInputValue($value, $this->field($field));
    }

    /** @return array<string, mixed> */
    public function listFilter(string $field, array $meta = []): array
    {
        $fieldMeta = $this->field($field);

        $filter = [
            'field' => $field,
            'query_key' => (string)($meta['query_key'] ?? $field),
            'label' => (string)($meta['label'] ?? $fieldMeta['label'] ?? ucfirst($field)),
            'type' => (string)($meta['type'] ?? $fieldMeta['type'] ?? 'text'),
            'placeholder' => (string)($meta['placeholder'] ?? ''),
            'default' => $meta['default'] ?? null,
            'help' => (string)($meta['help'] ?? $fieldMeta['help'] ?? ''),
        ];

        if (($filter['type'] ?? 'text') === 'select') {
            $filter['options'] = is_array($meta['options'] ?? null)
                ? (array)$meta['options']
                : (array)($fieldMeta['options'] ?? []);
        }

        return $filter;
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, array<string, mixed>> $filters
     * @return array<string, mixed>
     */
    public function filterPayload(array $input, array $filters): array
    {
        $payload = [];

        foreach ($filters as $key => $meta) {
            $field = (string)($meta['field'] ?? $key);
            $queryKey = (string)($meta['query_key'] ?? $key);
            $value = $input[$queryKey] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === null || $value === '') {
                continue;
            }

            $payload[$field] = $this->normalizeFieldValue($field, $value);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $meta
     * @return array<int, string>
     */
    private function buildValidationRulesForField(string $field, array $meta): array
    {
        $explicitRules = $meta['rules'] ?? null;
        if (is_string($explicitRules) && trim($explicitRules) !== '') {
            return array_values(array_filter(array_map('trim', explode('|', $explicitRules))));
        }

        if (is_array($explicitRules) && $explicitRules !== []) {
            return array_values(array_filter(array_map(
                static fn (mixed $rule): ?string => is_string($rule) && trim($rule) !== '' ? trim($rule) : null,
                $explicitRules
            )));
        }

        $rules = [];

        if (!empty($meta['required'])) {
            $rules[] = 'required';
        }

        $type = (string)($meta['type'] ?? 'text');

        switch ($type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'integer':
            case 'number':
                $rules[] = 'integer';
                break;
            default:
                $rules[] = 'string';
                break;
        }

        if (!empty($meta['slug']) || ($field === 'slug' && $type === 'text')) {
            $rules[] = 'slug';
        }

        if (array_key_exists('min', $meta)) {
            $rules[] = 'min:' . max(0, (int)$meta['min']);
        }

        if (array_key_exists('max', $meta)) {
            $rules[] = 'max:' . max(0, (int)$meta['max']);
        }

        if ($type === 'select') {
            $options = (array)($meta['options'] ?? []);
            $keys = array_values(array_map(static fn (mixed $value): string => (string)$value, array_keys($options)));

            if ($keys !== []) {
                $rules[] = 'in:' . implode(',', $keys);
            }
        }

        return array_values(array_unique($rules));
    }

    /** @param array<string, mixed> $meta */
    private function defaultValueForField(array $meta): mixed
    {
        if (array_key_exists('default', $meta)) {
            return $meta['default'];
        }

        $type = (string)($meta['type'] ?? 'text');

        if ($type === 'select') {
            $options = (array)($meta['options'] ?? []);
            $keys = array_keys($options);
            return (string)($keys[0] ?? '');
        }

        return '';
    }

    /** @param array<string, mixed> $meta */
    private function normalizeInputValue(mixed $value, array $meta): mixed
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (($value === null || $value === '') && array_key_exists('default', $meta)) {
            return $meta['default'];
        }

        $type = (string)($meta['type'] ?? 'text');

        if (($type === 'integer' || $type === 'number') && $value !== null && $value !== '') {
            $validated = filter_var($value, FILTER_VALIDATE_INT);
            if ($validated !== false) {
                return (int)$validated;
            }
        }

        if ($type === 'select' && $value !== null) {
            return (string)$value;
        }

        return $value;
    }
}
