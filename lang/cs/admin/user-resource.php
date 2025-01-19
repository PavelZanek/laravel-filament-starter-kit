<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Uživatelé',
    'navigation_group' => 'Správa uživatelů',
    'breadcrumb' => 'Uživatelé',
    'list' => [
        'title' => 'Uživatelé',
        'export' => [
            'modal_heading' => 'Exportovat uživatele',
        ],
    ],
    'create' => [
        'title' => 'Vytvořit uživatele',
        'subheading' => 'Tento formulář vytvoří nového uživatele',
    ],
    'edit' => [
        'title' => 'Upravit uživatele',
        'subheading' => 'Tento formulář upraví uživatele',
    ],
    'flash' => [
        'created' => 'Uživatel byl úspěšně vytvořen.',
        'updated' => 'Uživatel byl úspěšně aktualizován.',
        'deleted' => 'Uživatel byl úspěšně smazán.',
        'password_changed' => 'Heslo bylo úspěšně změněno.',
        'role_changed' => 'Role byla úspěšně změněna.',
    ],
    'attributes' => [
        'name' => 'Jméno',
        'email' => 'Email',
        'password' => 'Heslo',
        'email_verified_at' => 'Ověření emailu',
    ],
    'custom_attributes' => [
        'new_password' => 'Nové heslo',
        'new_password_confirmation' => 'Potvrzení nového hesla',
        'role' => 'Role',
    ],
    'actions' => [
        'change_password' => 'Změnit heslo',
        'change_role' => 'Změnit roli',
        'modals' => [
            'delete' => [
                'bulk' => [
                    'heading' => 'Smazat uživatele',
                    'description' => 'Opravdu chcete smazat vybrané uživatele?',
                ],
                'single' => [
                    'heading' => 'Smazat uživatele',
                    'description' => 'Opravdu chcete smazat tohoto uživatele?',
                ],
            ],
            'force_delete' => [
                'bulk' => [
                    'heading' => 'Trvale smazat uživatele',
                    'description' => 'Opravdu chcete trvale smazat vybrané uživatele?',
                ],
                'single' => [
                    'heading' => 'Trvale smazat uživatele',
                    'description' => 'Opravdu chcete trvale smazat tohoto uživatele?',
                ],
            ],
            'restore' => [
                'bulk' => [
                    'heading' => 'Obnovit uživatele',
                    'description' => 'Opravdu chcete obnovit vybrané uživatele?',
                ],
                'single' => [
                    'heading' => 'Obnovit uživatele',
                    'description' => 'Opravdu chcete obnovit tohoto uživatele?',
                ],
            ],
        ],
    ],
    'filters' => [
        'all' => 'Všichni uživatelé',
        'verified' => 'Ověření uživatelé',
        'unverified' => 'Neověření uživatelé',
    ],
];
