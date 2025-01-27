<?php

declare(strict_types=1);

arch('livewire components')
    ->expect('App\Livewire')
    ->toBeClasses()
    ->toExtend('Livewire\Component')
    ->toHaveMethod('render')
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'App\Http\Livewire',
        'App\Providers\AppServiceProvider',
    ])
    ->ignoring('App\Livewire\Concerns')
    ->not->toUse(['redirect', 'to_route', 'back']);

arch('livewire concerns')
    ->expect('App\Livewire\Concerns')
    ->toBeTraits();
