<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Komentáře',
    'navigation_group' => 'Ostatní',
    'breadcrumb' => 'Komentáře',
    'list' => [
        'title' => 'Komentáře',
    ],
    'create' => [
        'title' => 'Vytvořit komentář',
    ],
    'edit' => [
        'title' => 'Upravit komentář',
    ],
    'flash' => [
        'created' => 'Komentář byl úspěšně vytvořen.',
        'updated' => 'Komentář byl úspěšně aktualizován.',
        'deleted' => 'Komentář byl úspěšně smazán.',
        'force_deleted' => 'Komentář byl trvale smazán.',
        'restored' => 'Komentář byl úspěšně obnoven.',
        'deleted_bulk' => 'Komentáře byly úspěšně smazány.',
    ],
    'attributes' => [
        'user_id' => 'Uživatel',
        'content' => 'Komentář',
        'commentable_id' => 'ID',
        'commentable_type' => 'Typ',
    ],
    'custom_attributes' => [
        'commentable' => 'Nastavení komentáře',
    ],
    'actions' => [
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Smazat komentáře',
                    'description' => 'Opravdu chcete smazat vybrané komentáře?',
                ],
                'single' => [
                    'heading' => 'Smazat komentář',
                    'description' => 'Opravdu chcete smazat tento komentář?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Trvale smazat komentáře',
                    'description' => 'Opravdu chcete trvale smazat vybrané komentáře?',
                ],
                'single' => [
                    'heading' => 'Trvale smazat komentář',
                    'description' => 'Opravdu chcete trvale smazat tento komentář?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Obnovit komentáře',
                    'description' => 'Opravdu chcete obnovit vybrané komentáře?',
                ],
                'single' => [
                    'heading' => 'Obnovit komentář',
                    'description' => 'Opravdu chcete obnovit tento komentář?',
                ],
            ],
        ],
    ],
];
