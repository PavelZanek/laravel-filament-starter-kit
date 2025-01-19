<?php

declare(strict_types=1);

return [
    'navigation_label' => 'User Roles',
    'navigation_group' => 'User Management',
    'breadcrumb' => 'Roles',
    'list' => [
        'title' => 'User Roles',
    ],
    'create' => [
        'title' => 'Create Role',
        'subheading' => 'This form will create a new user role',
    ],
    'edit' => [
        'title' => 'Edit Role',
        'subheading' => 'This form will edit a user role',
    ],
    'flash' => [
        'created' => 'The user role was successfully created.',
        'updated' => 'The user role was successfully updated.',
        'deleted' => 'The user role was successfully deleted.',
    ],
    'form' => [
        'sections' => [
            'role' => [
                'heading' => 'Role Information',
                'description' => 'Enter details about the role.',
            ],
            'permissions' => [
                'heading' => 'Permissions',
                'description' => 'Select the permissions you want to assign to this role.',
            ],
        ],
    ],
    'attributes' => [
        'name' => 'Name',
        'guard_name' => 'Guard',
        'is_default' => 'Default',
    ],
    'custom_attributes' => [
        'permissions' => 'Permissions',
        'users_count' => 'Users Count',
    ],
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
];
