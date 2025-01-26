<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Kategorie',
    'navigation_group' => 'Taxonomie',
    'breadcrumb' => 'Kategorie',
    'list' => [
        'title' => 'Kategorie',
    ],
    'create' => [
        'title' => 'Vytvořit kategorii',
    ],
    'edit' => [
        'title' => 'Upravit kategorii',
    ],
    'flash' => [
        'created' => 'Kategorie byla úspěšně vytvořena.',
        'updated' => 'Kategorie byla úspěšně aktualizována.',
        'deleted' => 'Kategorie byla úspěšně smazána.',
        'force_deleted' => 'Kategorie byla trvale smazána.',
        'restored' => 'Kategorie byla úspěšně obnovena.',
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
                    'heading' => 'Smazat kategorie',
                    'description' => 'Opravdu chcete smazat vybrané kategorie?',
                ],
                'single' => [
                    'heading' => 'Smazat kategorii',
                    'description' => 'Opravdu chcete smazat tuto kategorii?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Trvale smazat kategorie',
                    'description' => 'Opravdu chcete trvale smazat vybrané kategorie?',
                ],
                'single' => [
                    'heading' => 'Trvale smazat kategorii',
                    'description' => 'Opravdu chcete trvale smazat tuto kategorii?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Obnovit kategorie',
                    'description' => 'Opravdu chcete obnovit vybrané kategorie?',
                ],
                'single' => [
                    'heading' => 'Obnovit kategorii',
                    'description' => 'Opravdu chcete obnovit tuto kategorii?',
                ],
            ],
        ],
    ],
];
