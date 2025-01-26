<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Comments',
    'navigation_group' => 'Others',
    'breadcrumb' => 'Comments',
    'list' => [
        'title' => 'Comments',
    ],
    'create' => [
        'title' => 'Create Comment',
    ],
    'edit' => [
        'title' => 'Edit Comment',
    ],
    'flash' => [
        'created' => 'Comment was successfully created.',
        'updated' => 'Comment was successfully updated.',
        'deleted' => 'Comment was successfully deleted.',
        'force_deleted' => 'Comment was permanently deleted.',
        'restored' => 'Comment was successfully restored.',
        'deleted_bulk' => 'Comments were successfully deleted.',
    ],
    'attributes' => [
        'user_id' => 'User',
        'content' => 'Comment',
        'commentable_id' => 'ID',
        'commentable_type' => 'Type',
    ],
    'custom_attributes' => [
        'commentable' => 'Comment Settings',
    ],
    'actions' => [
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Delete Comments',
                    'description' => 'Are you sure you want to delete the selected comments?',
                ],
                'single' => [
                    'heading' => 'Delete Comment',
                    'description' => 'Are you sure you want to delete this comment?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Permanently Delete Comments',
                    'description' => 'Are you sure you want to permanently delete the selected comments?',
                ],
                'single' => [
                    'heading' => 'Permanently Delete Comment',
                    'description' => 'Are you sure you want to permanently delete this comment?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Restore Comments',
                    'description' => 'Are you sure you want to restore the selected comments?',
                ],
                'single' => [
                    'heading' => 'Restore Comment',
                    'description' => 'Are you sure you want to restore this comment?',
                ],
            ],
        ],
    ],
];
