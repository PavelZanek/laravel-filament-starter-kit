<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Tags',
    'navigation_group' => 'Taxonomy',
    'breadcrumb' => 'Tags',
    'list' => [
        'title' => 'Tags',
    ],
    'create' => [
        'title' => 'Create Tag',
    ],
    'edit' => [
        'title' => 'Edit Tag',
    ],
    'flash' => [
        'created' => 'Tag was successfully created.',
        'updated' => 'Tag was successfully updated.',
        'deleted' => 'Tag was successfully deleted.',
        'force_deleted' => 'Tag was permanently deleted.',
        'restored' => 'Tag was successfully restored.',
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
                    'heading' => 'Delete Tags',
                    'description' => 'Are you sure you want to delete the selected tags?',
                ],
                'single' => [
                    'heading' => 'Delete Tag',
                    'description' => 'Are you sure you want to delete this tag?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Permanently Delete Tags',
                    'description' => 'Are you sure you want to permanently delete the selected tags?',
                ],
                'single' => [
                    'heading' => 'Permanently Delete Tag',
                    'description' => 'Are you sure you want to permanently delete this tag?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Restore Tags',
                    'description' => 'Are you sure you want to restore the selected tags?',
                ],
                'single' => [
                    'heading' => 'Restore Tag',
                    'description' => 'Are you sure you want to restore this tag?',
                ],
            ],
        ],
    ],
];
