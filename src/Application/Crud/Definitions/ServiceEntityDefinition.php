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
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'required' => true,
                    'min' => 2,
                    'max' => 255,
                    'placeholder' => 'Premium island transfers',
                ],
                'slug' => [
                    'type' => 'text',
                    'label' => 'Slug',
                    'required' => true,
                    'slug' => true,
                    'min' => 2,
                    'max' => 255,
                    'placeholder' => 'premium-island-transfers',
                    'help' => 'Lowercase letters, numbers, and hyphens only.',
                ],
                'status' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'required' => true,
                    'default' => 'draft',
                    'options' => [
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ],
                    'help' => 'Draft records stay hidden until you publish them.',
                ],
                'summary' => [
                    'type' => 'textarea',
                    'label' => 'Summary',
                    'required' => false,
                    'max' => 1000,
                    'rows' => 5,
                    'placeholder' => 'Short internal or public-facing summary.',
                ],
            ],
            listColumns: ['id', 'title', 'slug', 'status', 'summary'],
            searchable: ['title', 'slug', 'summary'],
            defaultOrder: 'id DESC'
        );
    }
}
