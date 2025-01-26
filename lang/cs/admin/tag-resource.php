<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Štítky',
    'navigation_group' => 'Taxonomie',
    'breadcrumb' => 'Štítky',
    'list' => [
        'title' => 'Štítky',
    ],
    'create' => [
        'title' => 'Vytvořit štítek',
    ],
    'edit' => [
        'title' => 'Upravit štítek',
    ],
    'flash' => [
        'created' => 'Štítek byl úspěšně vytvořen.',
        'updated' => 'Štítek byl úspěšně aktualizován.',
        'deleted' => 'Štítek byl úspěšně smazán.',
        'force_deleted' => 'Štítek byl trvale smazán.',
        'restored' => 'Štítek byl úspěšně obnoven.',
    ],
    'attributes' => [
        'name' => 'Název',
        'slug' => 'Slug',
    ],
    'custom_attributes' => [
        //
    ],
    'actions' => [
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Smazat štítky',
                    'description' => 'Opravdu chcete smazat vybrané štítky?',
                ],
                'single' => [
                    'heading' => 'Smazat štítek',
                    'description' => 'Opravdu chcete smazat tento štítek?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Trvale smazat štítky',
                    'description' => 'Opravdu chcete trvale smazat vybrané štítky?',
                ],
                'single' => [
                    'heading' => 'Trvale smazat štítek',
                    'description' => 'Opravdu chcete trvale smazat tento štítek?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Obnovit štítky',
                    'description' => 'Opravdu chcete obnovit vybrané štítky?',
                ],
                'single' => [
                    'heading' => 'Obnovit štítek',
                    'description' => 'Opravdu chcete obnovit tento štítek?',
                ],
            ],
        ],
    ],
];
