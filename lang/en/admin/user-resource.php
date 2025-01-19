<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Users',
    'navigation_group' => 'User Management',
    'breadcrumb' => 'Users',
    'list' => [
        'title' => 'Users',
        'export' => [
            'modal_heading' => 'Export Users',
        ],
    ],
    'create' => [
        'title' => 'Create User',
        'subheading' => 'This form will create a new user',
    ],
    'edit' => [
        'title' => 'Edit User',
        'subheading' => 'This form will edit an user',
    ],
    'flash' => [
        'created' => 'User was successfully created.',
        'updated' => 'User was successfully updated.',
        'deleted' => 'User was successfully deleted.',
        'password_changed' => 'Password was successfully changed.',
        'role_changed' => 'Role was successfully changed.',
    ],
    'attributes' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'email_verified_at' => 'Email Verified At',
    ],
    'custom_attributes' => [
        'new_password' => 'New Password',
        'new_password_confirmation' => 'Confirm New Password',
        'role' => 'Role',
    ],
    'actions' => [
        'change_password' => 'Change Password',
        'change_role' => 'Change Role',
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Delete Users',
                    'description' => 'Are you sure you want to delete selected users?',
                ],
                'single' => [
                    'heading' => 'Delete User',
                    'description' => 'Are you sure you want to delete this user?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Force Delete Users',
                    'description' => 'Are you sure you want to force delete selected users?',
                ],
                'single' => [
                    'heading' => 'Force Delete User',
                    'description' => 'Are you sure you want to force delete this user?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Restore Users',
                    'description' => 'Are you sure you want to restore selected users?',
                ],
                'single' => [
                    'heading' => 'Restore User',
                    'description' => 'Are you sure you want to restore this user?',
                ],
            ],
        ],
    ],
    'filters' => [
        'all' => 'All Users',
        'verified' => 'Verified Users',
        'unverified' => 'Unverified Users',
    ],
];
