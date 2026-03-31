<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud;

class CrudEntityDefinition
{
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

    public function fields(): array
    {
        return $this->fields;
    }

    public function listColumns(): array
    {
        return $this->listColumns;
    }

    public function searchable(): array
    {
        return $this->searchable;
    }

    public function defaultOrder(): string
    {
        return $this->defaultOrder;
    }
}
