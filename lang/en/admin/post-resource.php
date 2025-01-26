<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Posts',
    // 'navigation_group' => 'Taxonomy',
    'breadcrumb' => 'Posts',
    'list' => [
        'title' => 'Posts',
    ],
    'create' => [
        'title' => 'Create Post',
    ],
    'edit' => [
        'title' => 'Edit Post',
    ],
    'view' => [
        'title' => 'Post Detail',
    ],
    'relationships' => [
        'authors' => 'Authors',
        'comments' => 'Comments',
        'categories' => 'Categories',
        'tags' => 'Tags',
    ],
    'flash' => [
        'created' => 'Post was successfully created.',
        'updated' => 'Post was successfully updated.',
        'deleted' => 'Post was successfully deleted.',
        'force_deleted' => 'Post was permanently deleted.',
        'restored' => 'Post was successfully restored.',
    ],
    'filters' => [
        'published' => 'Published',
        'unpublished' => 'Unpublished',
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
                    'heading' => 'Delete Posts',
                    'description' => 'Are you sure you want to delete the selected posts?',
                ],
                'single' => [
                    'heading' => 'Delete Post',
                    'description' => 'Are you sure you want to delete this post?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Permanently Delete Posts',
                    'description' => 'Are you sure you want to permanently delete the selected posts?',
                ],
                'single' => [
                    'heading' => 'Permanently Delete Post',
                    'description' => 'Are you sure you want to permanently delete this post?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Restore Posts',
                    'description' => 'Are you sure you want to restore the selected posts?',
                ],
                'single' => [
                    'heading' => 'Restore Post',
                    'description' => 'Are you sure you want to restore this post?',
                ],
            ],
        ],
    ],
];
