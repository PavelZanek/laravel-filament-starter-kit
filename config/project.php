<?php

declare(strict_types=1);

return [
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
