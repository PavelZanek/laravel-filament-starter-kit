<?php

declare(strict_types=1);

return [
    'admin' => [
        'allowed_email' => env('HORIZON_ALLOWED_EMAIL'),
    ],
    'horizon' => [
        'allowed_email' => env('HORIZON_ALLOWED_EMAIL'),
    ],
    'telescope' => [
        'allowed_email' => env('TELESCOPE_ALLOWED_EMAIL'),
    ],
];
