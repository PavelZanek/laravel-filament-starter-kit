<?php

declare(strict_types=1);

return [
    'locales' => [
        'cs' => 'Czech',
        'en' => 'English',
    ],
    'flags' => [
        'cs' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icon-css@master/flags/4x3/cz.svg',
        'en' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icon-css@master/flags/4x3/gb.svg',
    ],
    'workspaces' => [
        'labels' => [
            'register' => 'Register Workspace',
            'settings' => 'Workspace Settings',
        ],
        'fields' => [
            'name' => 'Workspace Name',
        ],
    ],
    'edit_profile' => [
        'heading' => 'Edit Profile',
        'profile' => [
            'subheading' => 'Profile Information',
            'description' => 'Update your account\'s profile information and email address.',
            'fields' => [
                'name' => 'Name',
                'email' => 'Email',
            ],
        ],
        'password' => [
            'subheading' => 'Update Password',
            'description' => 'Ensure your account is using long, random password to stay secure.',
            'fields' => [
                'current_password' => 'Current Password',
                'new_password' => 'New Password',
                'confirm_password' => 'Confirm New Password',
            ],
        ],
    ],
    'footer' => [
        'created_by' => 'Created by',
        'rights' => 'All rights reserved',
    ],
    'id' => 'ID',
    'created_at' => 'Created',
    'updated_at' => 'Updated',
    'deleted_at' => 'Deleted',
    'created_from' => 'Created From',
    'created_until' => 'Created Until',
    'all' => 'All',
    'is_active' => 'Active',
    'is_default' => 'Default',
    'is_verified' => 'Verified',
    'is_published' => 'Published',
    'order' => 'Order',
    'formats' => [
        'datetime' => 'm/d/Y H:i',
        'date' => 'm/d/Y',
        'date_string' => 'F j, Y',
    ],
    'export' => 'Export',
    'blocks' => [
        'content' => 'Content',
        'add_block' => 'Add Block',
        'heading' => 'Heading',
        'level' => 'Level',
        'paragraph' => 'Paragraph',
        'image' => 'Image',
    ],
];
