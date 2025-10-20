<?php

declare(strict_types=1);

return [
    'super_admin_name' => env('SUPER_ADMIN_NAME', 'SuperAdmin'),
    'super_admin_email' => env('SUPER_ADMIN_EMAIL', 'superadmin@test.com'),
    'super_admin_password' => env('SUPER_ADMIN_PASSWORD', 'password'),
    'records_per_page' => [
        'default' => 10,
        'options' => [10, 25, 50, 100],
    ],
    'horizon' => [
        'allowed_email' => env('HORIZON_ALLOWED_EMAIL'),
    ],
    'telescope' => [
        'allowed_email' => env('TELESCOPE_ALLOWED_EMAIL'),
    ],
];
