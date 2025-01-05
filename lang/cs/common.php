<?php

declare(strict_types=1);

return [
    'locales' => [
        'cs' => 'Čeština',
        'en' => 'Angličtina',
    ],
    'flags' => [
        'cs' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icon-css@master/flags/4x3/cz.svg',
        'en' => 'https://cdn.jsdelivr.net/gh/lipis/flag-icon-css@master/flags/4x3/gb.svg',
    ],
    'workspaces' => [
        'labels' => [
            'settings' => 'Nastavení prostoru',
            'register' => 'Vytvořit nový prostor',
        ],
        'fields' => [
            'name' => 'Název prostoru',
        ],
    ],
    'edit_profile' => [
        'heading' => 'Upravit profil',
        'profile' => [
            'subheading' => 'Informace o profilu',
            'description' => 'Aktualizujte informace o profilu a e-mailovou adresu vašeho účtu.',
            'fields' => [
                'name' => 'Jméno',
                'email' => 'E-mail',
            ],
        ],
        'password' => [
            'subheading' => 'Aktualizovat heslo',
            'description' => 'Ujistěte se, že váš účet používá dlouhé, náhodné heslo, abyste zůstali v bezpečí.',
            'fields' => [
                'current_password' => 'Stávající heslo',
                'new_password' => 'Nové heslo',
                'confirm_password' => 'Potvrdit nové heslo',
            ],
        ],
    ],
    'footer' => [
        'created_by' => 'Vytvořil',
        'rights' => 'Všechna práva vyhrazena',
    ],
];
