<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Categories',
    'navigation_group' => 'Taxonomy',
    'breadcrumb' => 'Categories',
    'list' => [
        'title' => 'Categories',
    ],
    'create' => [
        'title' => 'Create Category',
    ],
    'edit' => [
        'title' => 'Edit Category',
    ],
    'flash' => [
        'created' => 'Category was successfully created.',
        'updated' => 'Category was successfully updated.',
        'deleted' => 'Category was successfully deleted.',
        'force_deleted' => 'Category was permanently deleted.',
        'restored' => 'Category was successfully restored.',
    ],
    'attributes' => [
        'name' => 'Name',
        'slug' => 'Slug',
    ],
    'custom_attributes' => [
        //
    ],
    'actions' => [
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Delete Categories',
                    'description' => 'Are you sure you want to delete the selected categories?',
                ],
                'single' => [
                    'heading' => 'Delete Category',
                    'description' => 'Are you sure you want to delete this category?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Permanently Delete Categories',
                    'description' => 'Are you sure you want to permanently delete the selected categories?',
                ],
                'single' => [
                    'heading' => 'Permanently Delete Category',
                    'description' => 'Are you sure you want to permanently delete this category?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Restore Categories',
                    'description' => 'Are you sure you want to restore the selected categories?',
                ],
                'single' => [
                    'heading' => 'Restore Category',
                    'description' => 'Are you sure you want to restore this category?',
                ],
            ],
        ],
    ],
];
