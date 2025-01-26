<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Příspěvky',
    // 'navigation_group' => 'Taxonomie',
    'breadcrumb' => 'Příspěvky',
    'list' => [
        'title' => 'Příspěvky',
    ],
    'create' => [
        'title' => 'Vytvořit příspěvek',
    ],
    'edit' => [
        'title' => 'Upravit příspěvek',
    ],
    'view' => [
        'title' => 'Detail příspěvku',
    ],
    'relationships' => [
        'authors' => 'Autoři',
        'comments' => 'Komentáře',
        'categories' => 'Kategorie',
        'tags' => 'Štítky',
    ],
    'flash' => [
        'created' => 'Příspěvek byl úspěšně vytvořen.',
        'updated' => 'Příspěvek byl úspěšně aktualizován.',
        'deleted' => 'Příspěvek byl úspěšně smazán.',
        'force_deleted' => 'Příspěvek byl trvale smazán.',
        'restored' => 'Příspěvek byl úspěšně obnoven.',
    ],
    'filters' => [
        'published' => 'Publikováno',
        'unpublished' => 'Nepublikováno',
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
                    'heading' => 'Smazat příspěvky',
                    'description' => 'Opravdu chcete smazat vybrané příspěvky?',
                ],
                'single' => [
                    'heading' => 'Smazat příspěvek',
                    'description' => 'Opravdu chcete smazat tento příspěvek?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Trvale smazat příspěvky',
                    'description' => 'Opravdu chcete trvale smazat vybrané příspěvky?',
                ],
                'single' => [
                    'heading' => 'Trvale smazat příspěvek',
                    'description' => 'Opravdu chcete trvale smazat tento příspěvek?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Obnovit příspěvky',
                    'description' => 'Opravdu chcete obnovit vybrané příspěvky?',
                ],
                'single' => [
                    'heading' => 'Obnovit příspěvek',
                    'description' => 'Opravdu chcete obnovit tento příspěvek?',
                ],
            ],
        ],
    ],
];
