<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Uživatelské role',
    'navigation_group' => 'Správa uživatelů',
    'breadcrumb' => 'Role',
    'list' => [
        'title' => 'Uživatelské role',
    ],
    'create' => [
        'title' => 'Vytvořit roli',
        'subheading' => 'Tento formulář vytvoří novou uživatelskou roli',
    ],
    'edit' => [
        'title' => 'Upravit roli',
        'subheading' => 'Tento formulář upraví uživatelskou roli',
    ],
    'flash' => [
        'created' => 'Uživatelská role byla úspěšně vytvořena.',
        'updated' => 'Uživatelská role byla úspěšně aktualizována.',
        'deleted' => 'Uživatelská role byla úspěšně smazána.',
    ],
    'form' => [
        'sections' => [
            'role' => [
                'heading' => 'Informace o roli',
                'description' => 'Zadejte podrobnosti o roli.',
            ],
            'permissions' => [
                'heading' => 'Oprávnění',
                'description' => 'Vyberte oprávnění, která chcete přiřadit k této roli.',
            ],
        ],
    ],
    'attributes' => [
        'name' => 'Jméno',
        'guard_name' => 'Guard',
        'is_default' => 'Výchozí',
    ],
    'custom_attributes' => [
        'permissions' => 'Oprávnění',
        'users_count' => 'Počet uživatelů',
    ],
    'actions' => [
        'edit' => 'Upravit',
        'delete' => 'Smazat',
    ],
];
