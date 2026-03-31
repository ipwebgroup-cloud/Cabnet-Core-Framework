<?php
declare(strict_types=1);

namespace Cabnet\Application\Crud\Definitions;

final class ServiceEntityDefinition
{
    public static function make(): \Cabnet\Application\Crud\CrudEntityDefinition
    {
        return new \Cabnet\Application\Crud\CrudEntityDefinition(
            key: 'services',
            label: 'Services',
            table: 'services',
            fields: [
                'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
                'slug' => ['type' => 'text', 'label' => 'Slug', 'required' => true],
                'status' => ['type' => 'select', 'label' => 'Status', 'required' => true, 'options' => ['draft' => 'Draft', 'published' => 'Published']],
                'summary' => ['type' => 'textarea', 'label' => 'Summary', 'required' => false],
            ],
            listColumns: ['id', 'title', 'slug', 'status', 'summary'],
            searchable: ['title', 'slug', 'summary'],
            defaultOrder: 'id DESC'
        );
    }
}
