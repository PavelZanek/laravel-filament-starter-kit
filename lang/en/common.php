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
            'settings' => 'Register Workspace',
            'register' => 'Workspace Settings',
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
];
