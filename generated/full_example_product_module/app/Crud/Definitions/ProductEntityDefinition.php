<?php
declare(strict_types=1);

final class ProductEntityDefinition
{
    public static function make(): CrudEntityDefinition
    {
        return new CrudEntityDefinition(
            key: 'products',
            label: 'Products',
            table: 'products',
            fields: [
                'title' => [
                    'type' => 'text',
                    'label' => 'Title',
                    'required' => true
                ],
                'slug' => [
                    'type' => 'text',
                    'label' => 'Slug',
                    'required' => true
                ],
                'status' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'required' => true,
                    'options' => [
                        'draft' => 'Draft',
                        'published' => 'Published'
                    ]
                ],
                'summary' => [
                    'type' => 'textarea',
                    'label' => 'Summary',
                    'required' => false
                ]
            ],
            listColumns: [
                'id',
                'title',
                'slug',
                'status',
                'summary'
            ],
            searchable: [
                'title',
                'slug',
                'summary'
            ],
            defaultOrder: 'id DESC'
        );
    }
}
