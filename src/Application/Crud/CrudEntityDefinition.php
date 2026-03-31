<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

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
                ? $source[$field]
                : $this->defaultValueForField($meta);
        }

        return $payload;
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
}
